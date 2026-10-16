<?php

use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/mercadopago', [CheckoutController::class, 'handleWebhook'])->name('webhook.mercadopago');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
