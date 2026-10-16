<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.company');
    }

    public function slide()
    {
        return view('admin.settings.slide');
    }

    public function users()
    {
        return view('admin.users.index');
    }

    public function branches()
    {
        return view('admin.branch');
    }

    /**
     * Muestra las reseñas.
     */
    public function reviews()
    {
        return view('admin.reviews.index');
    }

    /**
     * Muestra los métodos de pago.
     */
    public function paymentMethods()
    {
        return view('admin.boxes.method');
    }

}
