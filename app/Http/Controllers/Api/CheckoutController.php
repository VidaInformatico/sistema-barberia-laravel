<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderState;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MercadoPago\Client\MerchantOrder\MerchantOrderClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $topic = $request->input('topic') ?? $request->input('type');
        $id = $request->input('id') ?? $request->input('data.id');

        if ($topic === 'merchant_order' || $topic === 'payment') {
            try {
                $company = Company::first();
                if (!$company || !isset($company->extra_config['mercadopago']['token'])) {
                    \Illuminate\Support\Facades\Log::error("Webhook MercadoPago - Company o Token no encontrado");
                    return response()->json(['error' => 'Configuration error'], 500);
                }
                $accessToken = $company->extra_config['mercadopago']['token'];

                MercadoPagoConfig::setAccessToken($accessToken);

                if ($topic === 'payment') {
                    $paymentClient = new PaymentClient();
                    $payment = $paymentClient->get($id);
                    if (!isset($payment->order->id)) {
                        \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Pago sin order id asociado: {$id}");
                        return response()->json(['status' => 'pending'], 200);
                    }
                    $id = $payment->order->id;
                }

                $orderClient = new MerchantOrderClient();
                $merchantOrder = $orderClient->get($id);

                if ($merchantOrder->status !== 'closed') {
                    \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Merchant Order no cerrada ID: {$id}, Status: {$merchantOrder->status}");
                    return response()->json(['status' => 'waiting'], 200);
                }

                $externalReference = $merchantOrder->external_reference;

                if (str_starts_with($externalReference, 'booking_')) {
                    $parts = explode('_', $externalReference);
                    $bookingId = $parts[1] ?? null;

                    if (!$bookingId) {
                        \Illuminate\Support\Facades\Log::error("Webhook MercadoPago - Referencia externa de reserva inválida: {$externalReference}");
                        return response()->json(['status' => 'invalid_booking_reference'], 400);
                    }

                    return DB::transaction(function () use ($merchantOrder, $bookingId) {
                        $booking = Booking::lockForUpdate()->find($bookingId);

                        if (!$booking) {
                            \Illuminate\Support\Facades\Log::error("Webhook MercadoPago - Booking no encontrada ID: {$bookingId}");
                            return response()->json(['status' => 'booking_not_found'], 404);
                        }

                        if ($booking->payment_method === 'mercadopago' || $booking->status === Booking::STATUS_CONFIRMED) {
                            \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Booking ya procesada ID: {$bookingId}");
                            return response()->json(['status' => 'already_processed'], 200);
                        }

                        $booking->payment_method = 'mercadopago';
                        $booking->status = Booking::STATUS_CONFIRMED;
                        $booking->save();

                        \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Reserva pagada ID: {$bookingId}, Merchant Order ID: {$merchantOrder->id}");

                        return response()->json(['status' => 'success_booking'], 200);
                    });
                }

                return DB::transaction(function () use ($merchantOrder) {
                    $exists = Order::where('payment_id', (string) $merchantOrder->id)->lockForUpdate()->exists();

                    if ($exists) {
                        \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Order ya procesada Payment ID: {$merchantOrder->id}");
                        return response()->json(['status' => 'already_processed'], 200);
                    }

                    $initialState = OrderState::where('is_initial', true)->first()
                        ?? OrderState::first();

                    $totalAmount = (float) $merchantOrder->total_amount;

                    $branchId = null;
                    $clientId = null;
                    if ($merchantOrder->external_reference) {
                        $parts = explode('_', $merchantOrder->external_reference);
                        $branchId = $parts[1] ?? null;
                        $clientId = end($parts) !== false ? end($parts) : $merchantOrder->external_reference;
                    }

                    $order = Order::create([
                        'order_number'    => 'ORD-' . strtoupper(Str::random(8)),
                        'client_id'       => $clientId,
                        'order_state_id'  => $initialState?->id,
                        'branch_id'       => $branchId,
                        'payment_gateway' => 'mercadopago',
                        'payment_id'      => (string) $merchantOrder->id,
                        'gross_total'     => $totalAmount,
                        'tax_total'       => 0.00,
                        'discount_total'  => 0.00,
                        'total'           => $totalAmount,
                        'notes'           => 'Pago procesado exitosamente vía Mercado Pago',
                    ]);

                    if ($initialState) {
                        $order->trackings()->create([
                            'order_state_id' => $initialState->id,
                            'user_id'        => $order->client_id,
                            'comment'        => 'Pedido creado mediante pasarela Mercado Pago',
                        ]);
                    }

                    $accumulatedGross = 0.00;
                    $accumulatedTax = 0.00;

                    foreach ($merchantOrder->items as $mpItem) {
                        $qty = (int) $mpItem->quantity;
                        $price = (float) $mpItem->unit_price;
                        $itemTotal = $qty * $price;

                        $product = Product::with('comboItems')->find($mpItem->id);

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

                        $isCombo = $product && $product->comboItems->isNotEmpty();

                        if (!$isCombo) {
                            if ($product) {
                                $product->decrementStock(min($product->stock, $qty), 'Venta realizada a través de Mercado Pago', $order);
                            }
                        } else {
                            foreach ($product->comboItems as $component) {
                                $componentQuantity = $component->pivot->quantity * $qty;
                                Product::where('id', $component->id)->decrement('stock', $componentQuantity);
                            }
                        }

                        $orderDetail = $order->details()->create([
                            'product_id'      => $product?->id,
                            'product_name'    => $mpItem->title,
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

                        if ($isCombo) {
                            foreach ($product->comboItems as $component) {
                                $componentQuantity = $component->pivot->quantity * $qty;

                                $orderDetail->comboItems()->create([
                                    'product_id'     => $component->id,
                                    'product_name'   => $component->name,
                                    'unit_quantity'  => $component->pivot->quantity,
                                    'total_quantity' => $componentQuantity,
                                ]);
                            }
                        }
                    }

                    $order->update([
                        'gross_total' => $accumulatedGross,
                        'tax_total'   => $accumulatedTax,
                    ]);

                    \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Orden creada exitosamente ID: {$order->id}, Merchant Order ID: {$merchantOrder->id}");

                    return response()->json(['status' => 'success'], 200);
                });
            } catch (\Illuminate\Database\QueryException $e) {
                \Illuminate\Support\Facades\Log::error("Webhook MercadoPago - QueryException: {$e->getMessage()} en línea {$e->getLine()}");
                if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                    return response()->json(['status' => 'already_processed_concurrently'], 200);
                }
                return response()->json(['error' => $e->getMessage()], 500);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Webhook MercadoPago - Exception: {$e->getMessage()} en línea {$e->getLine()}");
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        \Illuminate\Support\Facades\Log::info("Webhook MercadoPago - Topic ignorado: {$topic}");
        return response()->json(['status' => 'ignored'], 200);
    }
}
