<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CreditPurchasesExport;
use App\Exports\PaymentsPurchasesExport;
use App\Exports\PurchasesExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Luecano\NumeroALetras\NumeroALetras;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('admin.purchases.index');
    }

    public function create()
    {
        return view('admin.purchases.create');
    }

    public function credit()
    {
        return view('admin.purchases.credit');
    }

    public function edit($encryptId)
    {
        try {
            $purchaseId = decrypt($encryptId);
            return view('admin.purchases.edit', compact('purchaseId'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function ticket($encryptId)
    {
        $id = Crypt::decrypt($encryptId);

        $purchase = Purchase::with(['items', 'payments.method'])->findOrFail($id);

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

        $totalTexto = $formatter->toInvoice($purchase->total, 2, 'pesos');

        $pdf = Pdf::loadView('admin.purchases.receipt', compact('purchase', 'logoBase64', 'totalTexto'))
            ->setPaper([0, 0, $width_pt, $height_pt], 'portrait');

        return $pdf->stream("ticket_{$id}.pdf");
    }

    public function payments(Request $request)
    {
        $purchase = null;

        if ($request->has('id')) {
            try {
                $purchase = Purchase::find(Crypt::decrypt($request->id));
            } catch (\Exception $e) {
                // ID inválido, $purchase queda null
            }
        }

        return view('admin.purchases.payment', compact('purchase'));
    }

    public function paymentReceipt($encryptedId)
    {
        try {
            $paymentId = Crypt::decryptString($encryptedId);

            $payment = \App\Models\Payment::with([
                'paymentable' => function ($morphTo) {
                    $morphTo->withTrashed();
                },
                'paymentable.supplier',
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

            $pdf = Pdf::loadView('admin.purchases.payment-ticket', compact('payment', 'logoBase64'))
                ->setPaper([0, 0, $width_pt, $height_pt], 'portrait');


            return $pdf->stream('ticket-pago-' . $paymentId . '.pdf');
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function export(Request $request)
    {
        $query = Purchase::with(['supplier', 'user'])
            ->where('branch_id', Auth::user()->branch_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('purchase_number', 'like', '%' . $search . '%')
                        ->orWhereHas('supplier', function ($subQuery) use ($search) {
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
                new PurchasesExport($request->search, $request->start_date, $request->end_date, $request->barber),
                'reporte-compras.xlsx'
            );
        }

        $purchases = $query->get();

        $pdf = Pdf::loadView('admin.purchases.pdf', compact('purchases'));
        return $pdf->stream('reporte-compras.pdf');
    }

    public function exportCredit(Request $request)
    {
        $query = Purchase::with(['user', 'supplier', 'payments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('credit', '>', 0)
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->when($request->search, function ($query, $search) {
                $query->whereHas('supplier', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc');

        $type = $request->query('type', 'pdf');

        if ($type === 'excel') {
            return Excel::download(
                new CreditPurchasesExport($request->search, $request->start_date, $request->end_date),
                'reporte-creditos.xlsx'
            );
        }

        $purchases = $query->get();

        $pdf = Pdf::loadView('admin.purchases.credit-pdf', compact('purchases'));
        return $pdf->stream('reporte-creditos.pdf');
    }

    public function exportPayment(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        $query = Payment::with(['method', 'paymentable.supplier'])
            ->where('paymentable_type', 'App\Models\Purchase')
            ->whereHasMorph('paymentable', ['App\Models\Purchase'], function ($q) use ($branchId, $request) {
                $q->where('branch_id', $branchId);

                if ($request->filled('search')) {
                    $search = $request->search;
                    $q->where(function ($sub) use ($search) {
                        $sub->where('purchase_number', 'like', "%{$search}%")
                            ->orWhereHas('supplier', fn($c) => $c->where('name', 'like', "%{$search}%"));
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
                new PaymentsPurchasesExport($request->search, $request->from, $request->to),
                'reporte-pagos.xlsx'
            );
        }

        $payments = $query->latest()->get();
        $pdf = Pdf::loadView('admin.purchases.payments-pdf', compact('payments'));

        return $pdf->stream('reporte-pagos.pdf');
    }

    function mmToPoints($mm)
    {
        return $mm * 2.83464567;
    }
}
