<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Muestra las reservas asignadas al barbero.
     */
    public function index()
    {
        return view('barber.bookings.index');
    }

    /**
     * Muestra el formulario para agendar una nueva cita.
     */
    public function create()
    {
        return view('barber.bookings.create');
    }
}
