<?php

use App\Http\Controllers\Admin\BarberController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CashRegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;

// Dashboard Principal
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('company', [SettingController::class, 'index'])->name('company.index');
Route::get('slides', [SettingController::class, 'slide'])->name('slides.index');
Route::get('users', [SettingController::class, 'users'])->name('users.index');
Route::get('branches', [SettingController::class, 'branches'])->name('branches.index');
Route::get('reviews', [SettingController::class, 'reviews'])->name('reviews.index');
Route::get('payment-method', [SettingController::class, 'paymentMethods'])->name('payment-method.index');

// Modulo Barberos, Horarios y Bloqueos
Route::get('barbers', [BarberController::class, 'index'])->name('barbers.index');
Route::get('barbers/schedules', [BarberController::class, 'schedules'])->name('barbers.schedules');
Route::get('barbers/services', [BarberController::class, 'services'])->name('barbers.services');
Route::get('barbers/blocked-times', [BarberController::class, 'blockedTimes'])->name('barbers.blocked-times');
Route::get('barbers/gallery', [BarberController::class, 'gallery'])->name('gallery.index');

// Modulo Reservas e Historiales
Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::get('bookings/history', [BookingController::class, 'history'])->name('bookings.history');
Route::get('bookings/policies', [BookingController::class, 'policies'])->name('bookings.policies');
Route::get('bookings/export/{type}', [BookingController::class, 'export'])->name('bookings.export');
// Modulo Servicios
Route::get('services', [ServiceController::class, 'index'])->name('services.index');
Route::get('services/categories', [ServiceController::class, 'categories'])->name('services.categories');

// Módulo Productos e Inventario
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/categories', [ProductController::class, 'categories'])->name('products.categories');
Route::get('products/brands', [ProductController::class, 'brands'])->name('products.brands');
Route::get('products/stock-movements', [ProductController::class, 'stockMovements'])->name('products.stock-movements');
Route::get('products/export/{type}', [ProductController::class, 'export'])->name('products.export');
Route::get('stock-movements/export/{type}', [ProductController::class, 'exportMovement'])->name('stock-movements.export');

Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('orders/status', [OrderController::class, 'states'])->name('orders.states');
Route::get('orders/export/{type}', [OrderController::class, 'export'])->name('orders.export');

// Modulo Ventas y Caja (POS)
Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::get('sales/credit', [SaleController::class, 'credit'])->name('sales.credit');

Route::get('sales/payments', [SaleController::class, 'payments'])->name('sales.payments');

Route::get('/sales/export', [SaleController::class, 'export'])
    ->name('sales.export');

Route::get('sales/{id}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');
Route::get('sales/{id}/edit', [SaleController::class, 'edit'])->name('sales.edit');

Route::get('/sales/credits/export', [SaleController::class, 'exportCredit'])
    ->name('sales.credits.export');

Route::get('/sales/payments/export', [SaleController::class, 'exportPayment'])
    ->name('sales.payments.export');

Route::get('/sales/payment/{id}/receipt', [SaleController::class, 'paymentReceipt'])
    ->name('sales.payment.receipt');
//COMPRAS
Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
Route::get('purchases/credit', [PurchaseController::class, 'credit'])->name('purchases.credit');

Route::get('purchases/payments', [PurchaseController::class, 'payments'])->name('purchases.payments');

Route::get('/purchases/export', [PurchaseController::class, 'export'])
    ->name('purchases.export');

Route::get('purchases/{id}/ticket', [PurchaseController::class, 'ticket'])->name('purchases.ticket');
Route::get('purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');

Route::get('/purchases/credits/export', [PurchaseController::class, 'exportCredit'])
    ->name('purchases.credits.export');

Route::get('/purchases/payments/export', [PurchaseController::class, 'exportPayment'])
    ->name('purchases.payments.export');

Route::get('/purchases/payment/{id}/receipt', [PurchaseController::class, 'paymentReceipt'])
    ->name('purchases.payment.receipt');

Route::get('boxes', [CashRegisterController::class, 'index'])->name('boxes.index');
Route::get('boxes/movements/{id}', [CashRegisterController::class, 'movements'])->name('boxes.movements');

Route::get('expenses', [BillController::class, 'index'])->name('expenses.index');
Route::get('/expenses/export', [BillController::class, 'export'])->name('expenses.export');

Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
