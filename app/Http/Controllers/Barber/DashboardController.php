<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $allUserBookings = Booking::where('barber_id', $user->barber->id);

        $nextBooking = (clone $allUserBookings)
            ->upcoming()
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'desc')
            ->first();

        $totalCompleted = (clone $allUserBookings)
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();

        $activeBookings = (clone $allUserBookings)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->get();

        $history = (clone $allUserBookings)
            ->whereNotIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->latest()
            ->limit(5)
            ->get();

        $barber = Barber::where('user_id', $user->id)->firstOrFail();
        $averageRating = $barber->ratings()->avg('score') ?? 0;

        return view('barber.dashboard', compact('nextBooking', 'totalCompleted', 'activeBookings', 'history', 'averageRating'));
    }
}
