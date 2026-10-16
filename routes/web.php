<?php

use App\Http\Controllers\Admin\SaleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/profile/{slug}', [BarberController::class, 'show'])->name('barber.show');

Route::get('/policies', [SettingController::class, 'policies'])->name('policies');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/reservation/{slug}/{barber?}', [ReservationController::class, 'index'])->name('reservation');

Route::get('printer/{id}/sale', [SaleController::class, 'ticket'])->name('printer.ticket.sale');
Route::get('printer/{id}/booking', [BookingController::class, 'ticket'])->name('printer.ticket.booking');

Route::middleware(['web', 'auth', 'role:client'])->group(function () {
    Route::get('/confirmation', [ReservationController::class, 'confirmation'])->name('confirmation');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

    Route::get('payment/success', [CheckoutController::class, 'success'])->name('payment.success');
    Route::get('payment/failure', [CheckoutController::class, 'failure'])->name('payment.failure');
    Route::get('payment/pending', [CheckoutController::class, 'pending'])->name('payment.pending');

    Route::post('paypal/create', [PaypalController::class, 'create'])->name('paypal.create');
    Route::post('paypal/capture', [PaypalController::class, 'capture'])->name('paypal.capture');
    Route::get('cash', [CheckoutController::class, 'cash'])->name('cash.process');

    Route::get('/payment/{booking_number}', [ReservationController::class, 'payment'])->name('payment.index');
});

Route::middleware(['web', 'auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Módulo Reservas
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');

    // Módulo Reseñas
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

    // Módulo Barberos
    Route::get('barber', [BarberController::class, 'index'])->name('barbers.index');

    // Módulo Órdenes
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
});

Route::post('/update-branch', [SettingController::class, 'updateBranch'])->name('admin.update-branch');

Route::get('/profile', [ProfileController::class, 'edit'])->middleware(['auth'])->name('profile');

require __DIR__ . '/auth.php';
