<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Luecano\NumeroALetras\NumeroALetras;

class BookingController extends Controller
{
    /**
     * Muestra las reservas del cliente.
     */
    public function index()
    {
        return view('clients.bookings.index');
    }

    /**
     * Muestra el formulario para crear una reserva.
     */
    public function create()
    {
        return view('clients.bookings.create');
    }

    public function ticket($encryptId)
    {
        $id = Crypt::decrypt($encryptId);

        $booking = Booking::with(['services.service', 'client', 'barber.user', 'branch'])->findOrFail($id);

        $size = request('size', 58);

        $width_mm = $size - 10;
        $height_mm = 297;

        $width_pt = $this->mmToPoints($width_mm);
        $height_pt = $this->mmToPoints($height_mm);

        $setting = Company::first();

        $logoBase64 = null;
        if ($setting && $setting->logo_path) {
            $relativePath = str_replace('/storage/', '', $setting->logo_path);

            $path = storage_path('app/public/' . $relativePath);

            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $formatter = new NumeroALetras();
        $totalTexto = $formatter->toInvoice($booking->total_price, 2, 'soles');

        $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking', 'logoBase64', 'totalTexto'))
            ->setPaper([0, 0, $width_pt, $height_pt], 'portrait');

        return $pdf->stream("ticket_booking_{$id}.pdf");
    }

    private function mmToPoints($mm)
    {
        return $mm * 2.83464567;
    }
}
