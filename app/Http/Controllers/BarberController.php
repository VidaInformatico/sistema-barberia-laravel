<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        return view('clients.barbers.index');
    }
    
    public function show($slug)
    {
        $barber = \App\Models\Barber::with(['user', 'services', 'galleryPhotos'])
            ->whereHas('user', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->firstOrFail();

        return view('landing.barber.show', compact('barber'));
    }
}
