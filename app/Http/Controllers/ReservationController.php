<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class ReservationController extends Controller
{
    public function index($slug, $barber = null)
    {
        $service = Product::where('slug', $slug)->firstOrFail();
        return view('landing.reservation', compact('service', 'barber'));
    }

    public function confirmation()
    {
        $user_id = Auth::id();
        $booking_number = request()->query('booking_number');
        $booking = Booking::with(['client', 'services', 'barber.user'])->where('booking_number', $booking_number)->first();
        if (!$booking) {
            return redirect()->route('welcome')->with('error', 'Booking not found.');
        }
        if ($booking->client_id !== $user_id) {
            return redirect()->route('welcome')->with('error', 'You are not authorized to view this booking.');
        }

        return view('landing.confirmation', compact('booking'));
    }

    public function payment($booking_number)
    {
        $user_id = Auth::id();
        $booking = Booking::with(['client', 'services', 'barber.user'])->where('booking_number', $booking_number)->first();

        if (!$booking) {
            return redirect()->route('welcome')->with('error', 'Booking not found.');
        }

        if ($booking->client_id !== $user_id) {
            return redirect()->route('welcome')->with('error', 'You are not authorized to view this booking.');
        }

        $company = \App\Models\Company::first();
        $mercadoPagoConfig = $company?->extra_config['mercadopago'] ?? [];

        $token = $mercadoPagoConfig['token'] ?? '';
        $currency = $mercadoPagoConfig['currency'] ?? 'PEN';

        $items = [];
        $total = 0;

        foreach ($booking->services as $item) {
            $items[] = [
                "id" => (string) $item->product_id,
                "title" => $item->service_name,
                "quantity" => (int) 1,
                "unit_price" => (float) $item->price,
                "currency_id" => $currency,
            ];
            $total += $item->price;
        }

        $preferenceId = null;

        if (!empty($token)) {
            try {
                MercadoPagoConfig::setAccessToken($token);

                $client = new PreferenceClient();
                $preference = $client->create([
                    "items" => $items,
                    "notification_url" => route('webhook.mercadopago', [], true),
                    "back_urls" => [
                        "success" => route('payment.success'),
                        "failure" => route('payment.failure'),
                        "pending" => route('payment.pending'),
                    ],
                    "external_reference" => "booking_" . $booking->id . "_" . $user_id,
                    "binary_mode" => true,
                ]);

                $preferenceId = $preference->id;
            } catch (\Exception $e) {
                Log::error('Error al generar la preferencia de Mercado Pago para la reserva: ' . $e->getMessage());
            }
        }

        return view('landing.pago-online', compact('booking', 'preferenceId', 'total', 'company'));
    }
}
