<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderState;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaypalController extends Controller
{
    private $baseUrl;
    private $clientId;
    private $secret;
    private $currency;

    public function __construct()
    {
        $company = Company::first();
        $paypalConfig = $company?->extra_config['paypal'] ?? [];

        $mode = $paypalConfig['mode'] ?? 'live';
        $this->baseUrl = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $this->clientId = $paypalConfig['client_id'] ?? '';
        $this->secret = $paypalConfig['secret'] ?? '';
        $this->currency = $paypalConfig['currency'] ?? 'USD';
    }

    private function getAccessToken()
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json()['access_token'];
    }

    public function create(Request $request)
    {
        $token = $this->getAccessToken();
        $bookingId = $request->input('booking_id');

        $items = [];
        $totalAmount = 0;

        if ($bookingId) {
            $booking = Booking::with('services')->find($bookingId);
            if (!$booking) {
                return response()->json(['error' => 'Booking not found.'], 404);
            }

            foreach ($booking->services as $item) {
                $price = (float) $item->price;
                $name = $item->service_name;
                $totalAmount += $price;

                $items[] = [
                    "name" => $name,
                    "quantity" => 1,
                    "unit_amount" => [
                        "currency_code" => $this->currency,
                        "value" => number_format($price, 2, '.', '')
                    ]
                ];
            }
        } else {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return response()->json(['error' => 'El carrito está vacío'], 400);
            }

            foreach ($cart as $id => $item) {
                $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $name = $item['name'] ?? 'Producto';
                $totalAmount += ($qty * $price);

                $items[] = [
                    "name" => $name,
                    "quantity" => $qty,
                    "unit_amount" => [
                        "currency_code" => $this->currency,
                        "value" => number_format($price, 2, '.', '')
                    ]
                ];
            }
        }

        $totalFormatted = number_format($totalAmount, 2, '.', '');

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => $this->currency,
                        "value" => $totalFormatted,
                        "breakdown" => [
                            "item_total" => [
                                "currency_code" => $this->currency,
                                "value" => $totalFormatted
                            ]
                        ]
                    ],
                    "items" => $items
                ]],
                "application_context" => [
                    "shipping_preference" => "NO_SHIPPING"
                ]
            ]);

        return response()->json($response->json());
    }

    public function capture(Request $request)
    {
        $token = $this->getAccessToken();
        $orderID = $request->orderID;
        $bookingId = $request->input('booking_id');

        $response = Http::withToken($token)
            ->withHeaders(["Content-Type" => "application/json"])
            ->withBody('{}', 'application/json')
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderID}/capture");

        $paypalData = $response->json();

        if (isset($paypalData['status']) && $paypalData['status'] === 'COMPLETED') {
            try {
                return DB::transaction(function () use ($paypalData, $bookingId) {
                    $transactionId = $paypalData['id'];

                    if ($bookingId) {
                        $booking = Booking::find($bookingId);
                        if ($booking) {
                            $booking->payment_method = 'paypal';
                            $booking->status = Booking::STATUS_CONFIRMED;
                            $booking->save();
                        }
                        return response()->json($paypalData);
                    }

                    // Lógica de productos existente
                    $exists = Order::where('payment_id', (string) $transactionId)->lockForUpdate()->exists();
                    if ($exists) {
                        return response()->json(['status' => 'already_processed'], 200);
                    }

                    $cart = session()->get('cart', []);
                    if (empty($cart)) {
                        return response()->json(['error' => 'No se encontró el carrito en la sesión'], 400);
                    }

                    $initialState = OrderState::where('is_initial', true)->first() ?? OrderState::first();

                    $totalAmount = 0;
                    foreach ($cart as $item) {
                        $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                        $price = (float) ($item['price'] ?? 0);
                        $totalAmount += ($qty * $price);
                    }

                    $branchId = session('current_branch') ?? \App\Models\Branch::where('active', true)->first()?->id;

                    $order = Order::create([
                        'order_number'    => 'ORD-' . strtoupper(Str::random(8)),
                        'client_id'       => Auth::id() ?: null,
                        'order_state_id'  => $initialState?->id,
                        'branch_id' => $branchId,
                        'payment_gateway' => 'paypal',
                        'payment_id'      => (string) $transactionId,
                        'gross_total'     => $totalAmount,
                        'tax_total'       => 0.00,
                        'discount_total'  => 0.00,
                        'total'           => $totalAmount,
                        'notes'           => 'Pago procesado exitosamente vía PayPal',
                    ]);

                    if ($initialState) {
                        $order->trackings()->create([
                            'order_state_id' => $initialState->id,
                            'user_id'        => $order->client_id,
                            'comment'        => 'Pedido creado mediante pasarela PayPal',
                        ]);
                    }

                    $accumulatedGross = 0.00;
                    $accumulatedTax = 0.00;

                    foreach ($cart as $id => $item) {
                        $productId = $item['id'] ?? $id;
                        $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                        $price = (float) ($item['price'] ?? 0);
                        $itemTotal = $qty * $price;

                        $product = Product::find($productId);
                        $isExempt = $product ? (bool) $product->is_exempt : false;
                        $taxRate = ($product && !$isExempt) ? (float) $product->tax : 0.00;

                        if ($taxRate > 0 && !$isExempt) {
                            $netSubtotal = round($itemTotal / (1 + ($taxRate / 100)), 2);
                            $taxAmount = round($itemTotal - $netSubtotal, 2);
                        } else {
                            $netSubtotal = $itemTotal;
                            $taxAmount = 0.00;
                        }

                        $accumulatedGross += $netSubtotal;
                        $accumulatedTax += $taxAmount;

                        $itemType = $item['type'] ?? ($product ? $product->type : 'product');

                        if ($itemType === 'product') {
                            if ($product) {
                                $product->decrementStock($qty, 'Venta realizada mediante PayPal', $order);
                            }
                        } elseif ($itemType === 'combo') {
                            if (!empty($item['combo_components'])) {
                                foreach ($item['combo_components'] as $component) {
                                    $componentQuantity = $component['quantity'] * $qty;
                                    Product::where('id', $component['product_id'])->decrement('stock', $componentQuantity);
                                }
                            }
                        }

                        $orderDetail = $order->details()->create([
                            'product_id'      => $product?->id,
                            'product_name'    => $item['name'] ?? $product?->name ?? 'Producto',
                            'quantity'        => $qty,
                            'cost'            => $product?->cost_price ?? 0.00,
                            'price'           => $price,
                            'itbis_rate'      => $taxRate,
                            'discount_rate'   => 0.00,
                            'itbis_amount'    => $taxAmount,
                            'gross_subtotal'  => $itemTotal,
                            'discount_amount' => 0.00,
                            'net_subtotal'    => $netSubtotal,
                            'total'           => $itemTotal,
                            'is_exempt'       => $isExempt,
                        ]);

                        if ($itemType === 'combo' && !empty($item['combo_components'])) {
                            foreach ($item['combo_components'] as $component) {
                                $componentQuantity = $component['quantity'] * $qty;
                                $compProduct = Product::find($component['product_id']);

                                $orderDetail->comboItems()->create([
                                    'product_id'     => $component['product_id'],
                                    'product_name'   => $compProduct ? $compProduct->name : ($component['name'] ?? ''),
                                    'unit_quantity'  => $component['quantity'],
                                    'total_quantity' => $componentQuantity,
                                ]);
                            }
                        }
                    }

                    $order->update([
                        'gross_total' => $accumulatedGross,
                        'tax_total'   => $accumulatedTax,
                    ]);

                    session()->forget('cart');

                    return response()->json($paypalData);
                });
            } catch (\Exception $e) {
                Log::error('Excepción al registrar la orden en DB', [
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                    'file'  => $e->getFile(),
                ]);

                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 'Pago no completado en PayPal'], 500);
    }
}
