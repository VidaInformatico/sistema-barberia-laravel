<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BillsExport;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BillController extends Controller
{
    public function index()
    {
        return view('admin.boxes.expense');
    }
    
    private function getQuery(Request $request)
    {
        return Expense::with(['cashRegister', 'paymentMethod', 'user'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where(function ($query) use ($request) {
                if ($request->filled('search')) {
                    $query->where('concept', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
            })
            ->when($request->start_date, function ($query) use ($request) {
                $query->whereDate('expense_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->whereDate('expense_date', '<=', $request->end_date);
            })
            ->latest();
    }

    public function export(Request $request)
    {
        if ($request->input('type') === 'excel') {
            return Excel::download(
                new BillsExport($this->getQuery($request)),
                'reporte-egresos-' . now()->format('Ymd') . '.xlsx'
            );
        }

        $expenses = $this->getQuery($request)->get();
        $search = $request->search;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $pdf = Pdf::loadView('admin.boxes.bill-pdf', compact('expenses', 'search', 'start_date', 'end_date'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('reporte-egresos-' . now()->format('Ymd') . '.pdf');
    }
}
