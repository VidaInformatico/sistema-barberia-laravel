<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return view('barber.reviews.index');
    }
}
