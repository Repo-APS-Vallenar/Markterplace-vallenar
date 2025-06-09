<?php
// Todas las rutas comentadas para evitar conflicto con web.php
// Route::middleware(['auth', 'checkrole:seller,admin'])
//     ->group(function () {
//         // ...
//     });

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerController;

Route::prefix('seller')
    ->middleware(['auth', 'checkrole:seller,admin'])
    ->as('seller.')
    ->group(function () {
        // Dashboard de Vendedor
        Route::get('/', [SellerController::class, 'index'])->name('index');

        // Gestión de productos

        // Pedidos recibidos
        Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
    });
