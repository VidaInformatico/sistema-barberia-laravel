<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BookingsExport;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    public function index()
    {
        return view('admin.bookings.index');
    }

    public function create()
    {
        return view('admin.bookings.create');
    }

    public function history()
    {
        return view('admin.bookings.history');
    }

    public function policies()
    {
        return view('admin.bookings.policies');
    }

    public function export(Request $request, $type)
    {
        $branchId = auth()->user()->branch_id;

        $bookings = Booking::query()
            ->with(['client', 'barber.user', 'services.service'])
            ->where('branch_id', $branchId)
            ->when($request->filled('selectedBarber'), fn($q) => $q->forBarber($request->selectedBarber))
            ->when($request->filled('selectedDate'), fn($q) => $q->forDate($request->selectedDate))
            ->when($request->filled('selectedStatus'), fn($q) => $q->where('status', $request->selectedStatus))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('booking_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('client', fn($c) => $c->where('phone', 'like', "%{$search}%"));
                });
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('admin.bookings.pdf', compact('bookings'));
            return $pdf->stream('reporte-reservas.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(
                new BookingsExport(
                    $request->selectedBarber,
                    $request->selectedDate,
                    $request->selectedStatus,
                    $request->search
                ),
                'reporte-reservas.xlsx'
            );
        }

        abort(404);
    }

}
