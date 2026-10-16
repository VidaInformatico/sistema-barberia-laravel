<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $allUserBookings = Booking::with('branch')->where('client_id', $user->id);

        $nextBooking = (clone $allUserBookings)
            ->upcoming()
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();

        $totalCompleted = (clone $allUserBookings)
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();

        $activeBookings = (clone $allUserBookings)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->latest()
            ->get();

        $history = (clone $allUserBookings)
            ->whereNotIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->latest()
            ->limit(5)
            ->get();

        $totalBarbers = Barber::count();

        $recentOrders = Order::with('branch')->where('client_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('nextBooking', 'totalCompleted', 'activeBookings', 'history', 'totalBarbers', 'recentOrders'));
    }
}