<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderState;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Tu carrito está vacío.');
        }

        $company = Company::first();
        $mercadoPagoConfig = $company?->extra_config['mercadopago'] ?? [];
        $branchId = session('current_branch') ?? \App\Models\Branch::where('active', true)->first()?->id;
        $total = 0;

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        $preferenceId = null;

        $token = $mercadoPagoConfig['token'] ?? '';

        if (!empty($token)) {
            try {
                $currency = $mercadoPagoConfig['currency'] ?? 'PEN';

                MercadoPagoConfig::setAccessToken($token);

                $items = [];

                foreach ($cart as $id => $item) {
                    $items[] = [
                        "id" => (string) $id,
                        "title" => $item['name'],
                        "quantity" => (int) $item['quantity'],
                        "unit_price" => (float) $item['price'],
                        "currency_id" => $currency,
                    ];
                }

                $user_id = Auth::id();

                $client = new PreferenceClient();
                $preference = $client->create([
                    "items" => $items,
                    "notification_url" => route('webhook.mercadopago', [], true),
                    "back_urls" => [
                        "success" => route('payment.success'),
                        "failure" => route('payment.failure'),
                        "pending" => route('payment.pending'),
                    ],
                    "external_reference" => "branch_" . $branchId . "_" . $user_id,
                    "binary_mode" => true,
                ]);

                $preferenceId = $preference->id;
            } catch (\Exception $e) {
                Log::error('Error al generar la preferencia de Mercado Pago: ' . $e->getMessage());
            }
        }

        return view('landing.checkout.index', [
            'preferenceId' => $preferenceId,
            'cart' => $cart,
            'total' => $total
        ]);
    }

    public function success()
    {
        session()->forget('cart');
        return view('landing.checkout.success');
    }

    public function failure()
    {
        return view('landing.checkout.failure');
    }

    public function pending()
    {
        return view('landing.checkout.pending');
    }

    public function cash()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'El carrito está vacío.');
        }

        try {
            return DB::transaction(function () use ($cart) {
                $initialState = OrderState::where('is_initial', true)->first()
                    ?? OrderState::first();

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
                    'payment_gateway' => 'cash',
                    'payment_id'      => null,
                    'gross_total'     => $totalAmount,
                    'tax_total'       => 0.00,
                    'discount_total'  => 0.00,
                    'total'           => $totalAmount,
                    'notes'           => 'Pago en efectivo / contraentrega',
                ]);

                if ($initialState) {
                    $order->trackings()->create([
                        'order_state_id' => $initialState->id,
                        'user_id'        => $order->client_id,
                        'comment'        => 'Pedido creado mediante pago en efectivo',
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

                    if ($product) {
                        $product->decrementStock($qty, 'Venta realizada mediante Pago en Efectivo', $order);
                    }

                    $order->details()->create([
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
                }

                $order->update([
                    'gross_total' => $accumulatedGross,
                    'tax_total'   => $accumulatedTax,
                ]);

                session()->forget('cart');

                return redirect()->route('payment.success')->with('success', 'Orden creada exitosamente. Por favor, espere a que el vendedor confirme su pedido.');
            });
        } catch (\Exception $e) {
            Log::error('Excepción al registrar la orden en efectivo en DB', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Ocurrió un error al procesar la orden: ' . $e->getMessage());
        }
    }
}
