<?php

namespace App\Http\Controllers;


class WelcomeController extends Controller
{
    public function index()
    {
        $slides = \App\Models\HeroSlide::where('is_active', true)
            ->orderBy('order')
            ->get();

        $branchId = session('current_branch') ?? \App\Models\Branch::where('active', true)->first()?->id;

        $services = \App\Models\Product::where('type', 'service')
            ->where('is_active', true)
            ->where('branch_id', $branchId)
            ->orderBy('sort_order')
            ->get();

        $gallery = \App\Models\GalleryPhoto::whereHas('barber.user', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->orderBy('sort_order', 'asc')
            ->limit(8)
            ->get();

        $barbers = \App\Models\Barber::whereHas('user', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->where('is_available', true)
            ->with('user')
            ->get();

        $ratings = \App\Models\Rating::with(['user', 'rateable'])
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->latest()
            ->take(15)
            ->get();

        return view('welcome', compact('slides', 'services', 'gallery', 'barbers', 'ratings'));
    }
}
