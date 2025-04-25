<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Auth\LoginController;

// Rutas públicas
Route::view('/', 'welcome');
//Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard_prueba', fn() => view('dashboard_prueba'))->middleware(['auth', 'verified'])->name('dashboard_prueba');

Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de productos
Route::get('/products', [ProductController::class, 'index'])->name('seller.products.index');
Route::post('/products', [ProductController::class, 'store'])->name('seller.products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('seller.products.show'); // Para el modal de ver
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('seller.products.edit'); // Para el modal de editar
Route::put('/products/{product}', [ProductController::class, 'update'])->name('seller.products.update'); // Para guardar cambios en editar
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('seller.products.destroy');


// En routes/web.php
Route::get('/seller/products/{product}/edit', [ProductController::class, 'edit'])->name('seller.products.edit');
Route::put('/seller/products/{product}', [ProductController::class, 'update'])->name('seller.products.update');


// Rutas de órdenes
Route::get('/products/{product}/order', [OrderController::class, 'create'])->name('seller.products.order.create');
Route::post('/products/{product}/order', [OrderController::class, 'store'])->name('seller.products.order.store');
Route::get('/products/{product}/order/success', [OrderController::class, 'success'])->name('seller.products.order.success');
Route::get('/products/{product}/order/cancel', [OrderController::class, 'cancel'])->name('seller.products.order.cancel');
Route::get('/products/{product}/order', [OrderController::class, 'show'])->name('seller.products.order.show');
Route::get('/products/{product}/order/edit', [OrderController::class, 'edit'])->name('seller.products.order.edit');
Route::put('/products/{product}/order', [OrderController::class, 'update'])->name('seller.products.order.update');
Route::delete('/products/{product}/order', [OrderController::class, 'destroy'])->name('seller.products.order.destroy');

// Rutas para administradores, vendedores y compradores
Route::get('/orders', [OrderController::class])->name('orders.index');
Route::get('/admin/index', [AdminController::class, 'index']);
Route::get('/seller', [SellerController::class, 'index'])->name('seller.index');
Route::get('/buyer', [BuyerController::class, 'index'])->name('buyer.index');

// Auth scaffolding
require __DIR__ . '/auth.php';
