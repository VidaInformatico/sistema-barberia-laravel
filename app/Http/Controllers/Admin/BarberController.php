<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        return view('admin.barbers.index');
    }

    public function schedules()
    {
        return view('admin.barbers.schedules');
    }

    public function services()
    {
        return view('admin.barbers.services');
    }

    public function blockedTimes()
    {
        return view('admin.barbers.blocked-times');
    }

    public function gallery()
    {
        return view('admin.barbers.gallery');
    }
}
