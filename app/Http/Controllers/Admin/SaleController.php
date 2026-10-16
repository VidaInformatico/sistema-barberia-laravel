<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CreditSalesExport;
use App\Exports\PaymentsSalesExport;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Luecano\NumeroALetras\NumeroALetras;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function index()
    {
        return view('admin.sales.index');
    }

    public function create()
    {
        return view('admin.sales.create');
    }

    public function credit()
    {
        return view('admin.sales.credit');
    }

    public function edit($encryptId)
    {
        try {
            $saleId = decrypt($encryptId);
            return view('admin.sales.edit', compact('saleId'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function ticket($encryptId)
    {
        $id = Crypt::decrypt($encryptId);

        $sale = Sale::with(['items.comboItems', 'payments.method'])->findOrFail($id);

        $size = request('size', 58);

        $width_mm = $size - 10;
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

        $totalTexto = $formatter->toInvoice($sale->total, 2, 'soles');

        $pdf = Pdf::loadView('admin.sales.receipt', compact('sale', 'logoBase64', 'totalTexto'))
            ->setPaper([0, 0, $width_pt, $height_pt], 'portrait');

        return $pdf->stream("ticket_{$id}.pdf");
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

        return view('admin.sales.payment', compact('sale'));
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

    public function export(Request $request)
    {
        $query = Sale::with(['client', 'creator'])
            ->where('branch_id', Auth::user()->branch_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sale_number', 'like', '%' . $search . '%')
                        ->orWhereHas('client', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($request->barber, function ($query, $barberId) {
                $query->where('barber_id', $barberId);
            })
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->orderBy('id', 'desc');

        $type = $request->query('type', 'pdf');

        if ($type === 'excel') {
            return Excel::download(
                new SalesExport($request->search, $request->start_date, $request->end_date, $request->barber),
                'reporte-ventas.xlsx'
            );
        }

        $sales = $query->get();

        $pdf = Pdf::loadView('admin.sales.pdf', compact('sales'));
        return $pdf->stream('reporte-ventas.pdf');
    }

    public function exportCredit(Request $request)
    {
        $query = Sale::with(['creator', 'client', 'payments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('credit', '>', 0)
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->when($request->search, function ($query, $search) {
                $query->whereHas('client', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc');

        $type = $request->query('type', 'pdf');

        if ($type === 'excel') {
            return Excel::download(
                new CreditSalesExport($request->search, $request->start_date, $request->end_date),
                'reporte-creditos.xlsx'
            );
        }

        $sales = $query->get();

        $pdf = Pdf::loadView('admin.sales.credit-pdf', compact('sales'));
        return $pdf->stream('reporte-creditos.pdf');
    }

    public function exportPayment(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        $query = Payment::with(['method', 'paymentable.client'])
            ->where('paymentable_type', 'App\Models\Sale')
            ->whereHasMorph('paymentable', ['App\Models\Sale'], function ($q) use ($branchId, $request) {
                $q->where('branch_id', $branchId);

                if ($request->filled('search')) {
                    $search = $request->search;
                    $q->where(function ($sub) use ($search) {
                        $sub->where('sale_number', 'like', "%{$search}%")
                            ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%"));
                    });
                }
            });

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $type = $request->query('type', 'pdf');

        if ($type === 'excel') {
            return Excel::download(
                new PaymentsSalesExport($request->search, $request->from, $request->to),
                'reporte-pagos.xlsx'
            );
        }

        $payments = $query->latest()->get();
        $pdf = Pdf::loadView('admin.sales.payments-pdf', compact('payments'));

        return $pdf->stream('reporte-pagos.pdf');
    }

    private function mmToPoints($mm)
    {
        return $mm * 2.83464567;
    }
}
