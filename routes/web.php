<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Buyer\ProductController as BuyerProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Admin\AdminUserController;

// Ruta de bienvenida
Route::view('/', 'auth.login')->name('login');

// Autenticación (Laravel Breeze / Fortify)
require __DIR__ . '/auth.php';

// Dashboard genérico
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/dashboard_prueba', fn() => view('dashboard_prueba'))->name('dashboard_prueba');
});

// Perfil de usuario autenticado
Route::prefix('profile')->middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas generales de pedidos (con políticas dentro del controlador)
Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});

// Panel administrador
Route::prefix('admin')
    ->middleware(['auth'])
    ->as('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        // Gestión de usuarios (clásica)
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'showUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/users/{user}/delete-confirm', [AdminController::class, 'deleteConfirm'])->name('users.delete-confirm');
        Route::get('/users/{user}/edit-modal', [AdminController::class, 'editModal'])->name('users.edit-modal');

        // Supervisión de vendedores
        Route::get('/supervise-sellers', [AdminUserController::class, 'index'])->name('supervise_sellers');

        // Supervisión de compradores
        Route::get('/buyers', [AdminUserController::class, 'buyers'])->name('buyers.index');
    });

// Panel vendedor
Route::prefix('seller')
    ->middleware(['auth'])
    ->as('seller.')
    ->group(function () {
        Route::get('/', [SellerController::class, 'index'])->name('index');

        // Gestión de productos del vendedor
        Route::get('/products/create', [App\Http\Controllers\Seller\ProductController::class, 'create'])->name('products.create');
        Route::get('/products/prueba', function() {
            return 'Funciona!';
        });
        Route::get('/products', [SellerProductController::class, 'index'])->name('products.index');
        Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [SellerProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [SellerProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [SellerProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/{product}/delete-modal', [App\Http\Controllers\Seller\ProductController::class, 'deleteModal'])->name('products.delete-modal');

        // Pedidos asociados a productos del vendedor
        Route::get('/orders', [SellerController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}/modal', [App\Http\Controllers\Seller\SellerController::class, 'showOrderModal'])->name('orders.show-modal');
    });
Route::get('/products/{userId}', [BuyerProductController::class, 'showProducts'])
    ->middleware(['auth'])
    ->name('buyer.products.by_user');

// Panel comprador
Route::prefix('buyer')
    ->middleware(['auth'])
    ->as('buyer.')
    ->group(function () {
        Route::get('/', [BuyerController::class, 'index'])->name('index');

        // Ver pedidos del comprador
        Route::get('/orders', [BuyerController::class, 'orders'])->name('orders.index');

        // Ver lista de emprendedores con productos
        Route::get('/emprendedores', [BuyerController::class, 'sellersWithProducts'])->name('buyer.sellers');

        // Ver productos de un vendedor específico
        Route::get('/products/by-user/{userId}', [BuyerProductController::class, 'showProducts'])->name('products.by_user');

        // Crear y gestionar pedido desde un producto
        Route::get('/products/{product}/order', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/products/{product}/order', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/products/{product}/order/success', [OrderController::class, 'success'])->name('orders.success');
        Route::get('/products/{product}/order/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/products/{product}/order/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/products/{product}/order', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/products/{product}/order', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

// Ver todos los productos (comprador)
Route::get('/buyer/products', [App\Http\Controllers\Buyer\ProductController::class, 'index'])->name('buyer.products.index');

// Carrito de compras (funcional para todos los usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
});

// Ruta de prueba fuera de grupo
Route::get('/prueba-fuera', function() {
    return 'Ruta fuera de grupo';
});
