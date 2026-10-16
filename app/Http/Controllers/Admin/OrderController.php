<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function states()
    {
        return view('admin.orders.state');
    }

    public function export(Request $request, $type)
    {
        $branchId = Auth::user()->branch_id;
        $search = $request->search;
        $selectedStatus = $request->filled('selectedStatus') ? (int) $request->selectedStatus : null;
        $selectedDate = $request->selectedDate;

        $orders = Order::query()
            ->with(['state', 'client', 'details'])
            ->where('branch_id', $branchId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', '%' . $search . '%')
                        ->orWhere('payment_id', 'like', '%' . $search . '%');
                });
            })
            ->when($selectedStatus, fn($q) => $q->where('order_state_id', $selectedStatus))
            ->when($selectedDate, fn($q) => $q->whereDate('created_at', $selectedDate))
            ->orderBy('created_at', 'desc')
            ->get();

        $company = Company::first();

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.orders.pdf', compact('orders'));
            return $pdf->stream('reporte-ordenes.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(new OrdersExport($search, $selectedStatus, $selectedDate), 'reporte-ordenes.xlsx');
        }

        abort(404);
    }
}
