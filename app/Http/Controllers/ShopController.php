<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('landing.shop.index');
    }

    public function show($slug)
    {
        $product = \App\Models\Product::with('branch')->whereIn('type', ['product', 'combo'])->with('category')->where('slug', $slug)->firstOrFail();

        return view('landing.shop.show', compact('product'));
    }
}
