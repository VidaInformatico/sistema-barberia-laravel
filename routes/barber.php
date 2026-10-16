<?php

use App\Http\Controllers\Barber\BookingController;
use App\Http\Controllers\Barber\CashRegisterController;
use App\Http\Controllers\Barber\DashboardController;
use App\Http\Controllers\Barber\ReviewController;
use App\Http\Controllers\Barber\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Módulo Reservas (Barbero)
Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');

Route::get('boxes', [CashRegisterController::class, 'index'])->name('cash.register');
Route::get('boxes/movements/{id}', [CashRegisterController::class, 'movements'])->name('cash.movements');

Route::get('expenses', [CashRegisterController::class, 'expense'])->name('cash.expenses');

Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::get('sales/credit', [SaleController::class, 'credit'])->name('sales.receivables');

Route::get('sales/payments', [SaleController::class, 'payments'])->name('sales.payments');
Route::get('sales/{id}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');
Route::get('/sales/payment/{id}/receipt', [SaleController::class, 'paymentReceipt'])
    ->name('sales.payment.receipt');
// Módulo Reseñas (Barbero)
Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
