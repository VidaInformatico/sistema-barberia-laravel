<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Luecano\NumeroALetras\NumeroALetras;

class SaleController extends Controller
{
    public function index()
    {
        return view('barber.sales.index');
    }

    public function create()
    {
        return view('barber.sales.create');
    }

    public function credit()
    {
        return view('barber.sales.credit');
    }

    public function payments(Request $request)
    {
        $sale = null;

        if ($request->has('id')) {
            try {
                $sale = Sale::find(Crypt::decrypt($request->id));
            } catch (\Exception $e) {
                // ID inválido, $sale queda null
            }
        }

        return view('barber.sales.payment', compact('sale'));
    }

    public function ticket($encryptId)
    {
        $id = Crypt::decrypt($encryptId);

        $sale = Sale::with(['items.comboItems', 'payments.method'])->findOrFail($id);

        $width_mm = 80 - 10;
        $height_mm = 297;

        $width_pt = $this->mmToPoints($width_mm);
        $height_pt = $this->mmToPoints($height_mm);

        $setting = Company::first();

        $logoBase64 = null;
        if ($setting->logo_path) {
            $relativePath = str_replace('/storage/', '', $setting->logo_path);

            $path = storage_path('app/public/' . $relativePath);

            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $formatter = new NumeroALetras();

        $totalTexto = $formatter->toInvoice($sale->total, 2, 'pesos');

        $pdf = Pdf::loadView('admin.sales.receipt', compact('sale', 'logoBase64', 'totalTexto'))
            ->setPaper([0, 0, $width_pt, $height_pt], 'portrait');

        return $pdf->stream("ticket_{$id}.pdf");
    }

    public function paymentReceipt($encryptedId)
    {
        try {
            $paymentId = Crypt::decryptString($encryptedId);

            $payment = \App\Models\Payment::with([
                'paymentable' => function ($morphTo) {
                    $morphTo->withTrashed();
                },
                'paymentable.client',
                'paymentable.payments',
                'method'
            ])->find($paymentId);

            $width_mm = 80 - 10;
            $height_mm = 297;

            $width_pt = $this->mmToPoints($width_mm);
            $height_pt = $this->mmToPoints($height_mm);

            $setting = Company::first();
            $logoBase64 = null;
            if ($setting->logo_path) {
                $relativePath = str_replace('/storage/', '', $setting->logo_path ?? '');
                $path = storage_path('app/public/' . $relativePath);

                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            $pdf = Pdf::loadView('admin.sales.payment-ticket', compact('payment', 'logoBase64'))
                ->setPaper([0, 0, $width_pt, $height_pt], 'portrait');


            return $pdf->stream('ticket-pago-' . $paymentId . '.pdf');
        } catch (\Exception $e) {
            abort(404);
        }
    }

    function mmToPoints($mm)
    {
        return $mm * 2.83464567;
    }
}
