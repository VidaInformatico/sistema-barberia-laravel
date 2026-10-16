<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Sale, Barber, CashRegister, Order, Product};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $branch_id = Auth::user()->branch_id;

        $totalSalesMonth = Sale::where('branch_id', $branch_id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total');

        $totalOrdersMonth = Order::where('branch_id', $branch_id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total');

        $totalBookingsMonth = Booking::where('branch_id', $branch_id)
            ->whereMonth('booking_date', $now->month)
            ->whereYear('booking_date', $now->year)
            ->count();

        $salesToday = Sale::where('branch_id', $branch_id)
            ->whereDate('created_at', $now->today())->sum('total');

        $cashBalance = CashRegister::where('branch_id', $branch_id)
            ->where('status', 'open')
            ->value('current_amount') ?? 0;

        $totalProducts = Product::where('branch_id', $branch_id)
            ->where('type', 'product')->count();

        $activeServices = Product::where('branch_id', $branch_id)
            ->where('type', 'service')->where('is_active', true)->count();

        $totalRevenue = Sale::where('branch_id', $branch_id)
            ->sum('total') + Order::sum('total');

        $avgTicket = Sale::where('branch_id', $branch_id)
            ->avg('total') ?? 0;

        $lowStockProducts = Product::where('branch_id', $branch_id)
            ->whereColumn('stock', '<=', 'stock_min')->count();

        $salesRaw = Sale::selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->where('branch_id', $branch_id)
            ->whereYear('created_at', $now->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $bookingsRaw = Booking::selectRaw('MONTH(booking_date) as month, SUM(total_price) as total')
            ->where('branch_id', $branch_id)
            ->whereYear('booking_date', $now->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $ordersRaw = Order::selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->where('branch_id', $branch_id)
            ->whereYear('created_at', $now->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $salesByMonth = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'total' => $salesRaw[$m] ?? 0,
        ]);

        $bookingsByMonth = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'total' => $bookingsRaw[$m] ?? 0,
        ]);

        $ordersByMonth = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'total' => $ordersRaw[$m] ?? 0,
        ]);

        $monthsEs = [
            1 => 'Ene',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dic'
        ];

        $salesLabels = $salesByMonth->pluck('month')->map(fn($m) => $monthsEs[$m]);
        $bookingsLabels = $bookingsByMonth->pluck('month')->map(fn($m) => $monthsEs[$m]);
        $ordersLabels = $ordersByMonth->pluck('month')->map(fn($m) => $monthsEs[$m]);

        $startWeek = now()->startOfWeek();
        $endWeek = now()->endOfWeek();

        $salesWeekRaw = Sale::selectRaw('DAYOFWEEK(created_at) as day, SUM(total) as total')
            ->where('branch_id', $branch_id)
            ->whereBetween('created_at', [$startWeek, $endWeek])
            ->groupBy('day')
            ->pluck('total', 'day');

        $bookingsWeekRaw = Booking::selectRaw('DAYOFWEEK(created_at) as day, SUM(total_price) as total')
            ->where('branch_id', $branch_id)
            ->whereBetween('created_at', [$startWeek, $endWeek])
            ->groupBy('day')
            ->pluck('total', 'day');

        $ordersWeekRaw = Order::selectRaw('DAYOFWEEK(created_at) as day, SUM(total) as total')
            ->where('branch_id', $branch_id)
            ->whereBetween('created_at', [$startWeek, $endWeek])
            ->groupBy('day')
            ->pluck('total', 'day');

        $daysEs = [
            1 => 'Dom',
            2 => 'Lun',
            3 => 'Mar',
            4 => 'Mié',
            5 => 'Jue',
            6 => 'Vie',
            7 => 'Sáb'
        ];

        $salesByWeek = collect(range(1, 7))->map(fn($d) => [
            'day' => $daysEs[$d],
            'total' => $salesWeekRaw[$d] ?? 0,
        ]);

        $bookingsByWeek = collect(range(1, 7))->map(fn($d) => [
            'day' => $daysEs[$d],
            'total' => $bookingsWeekRaw[$d] ?? 0,
        ]);

        $ordersByWeek = collect(range(1, 7))->map(fn($d) => [
            'day' => $daysEs[$d],
            'total' => $ordersWeekRaw[$d] ?? 0,
        ]);

        $topServices = Product::select('products.id', 'products.name')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->leftJoin('barber_services', 'barber_services.product_id', '=', 'products.id')
            ->leftJoin('bookings', 'bookings.barber_id', '=', 'barber_services.barber_id')
            ->where('products.branch_id', $branch_id)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

            $topBarbers = Barber::withCount('bookings')
            ->whereHas('user', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        $topProducts = Product::select('products.id', 'products.name')
            ->selectRaw('COUNT(sale_items.id) as sale_items_count')
            ->leftJoin('sale_items', 'sale_items.product_id', '=', 'products.id')
            ->where('products.branch_id', $branch_id)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sale_items_count')
            ->limit(5)
            ->get();

        $lowStock = Product::where('branch_id', $branch_id)
            ->whereColumn('stock', '<=', 'stock_min')
            ->orderBy('stock')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalSalesMonth',
            'totalOrdersMonth',
            'totalBookingsMonth',
            'salesToday',
            'cashBalance',
            'totalProducts',
            'activeServices',
            'totalRevenue',
            'avgTicket',
            'lowStockProducts',
            'salesByMonth',
            'bookingsByMonth',
            'ordersByMonth',
            'salesByWeek',
            'bookingsByWeek',
            'ordersByWeek',
            'salesLabels',
            'bookingsLabels',
            'ordersLabels',
            'topServices',
            'topBarbers',
            'topProducts',
            'lowStock'
        ));
    }
}
