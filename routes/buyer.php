<?php
// Todas las rutas comentadas para evitar conflicto con web.php
// Route::middleware(['auth', 'checkrole:buyer,admin'])
//     ->group(function () {
//         // ...
//     });

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\OrderController;

Route::prefix('buyer')
    ->middleware(['auth', 'checkrole:buyer,admin'])
    ->as('buyer.')
    ->group(function () {
        // Dashboard de Comprador
        Route::get('/', [BuyerController::class, 'index'])->name('index');

        // Historial de pedidos
        Route::get('/orders', [BuyerController::class, 'orders'])->name('orders');

        // Crear pedido desde un producto
        Route::get('/products/{product}/order', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/products/{product}/order', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/products/{product}/order/success', [OrderController::class, 'success'])->name('orders.success');
        Route::get('/products/{product}/order/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/products/{product}/order/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/products/{product}/order', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/products/{product}/order', [OrderController::class, 'destroy'])->name('orders.destroy');
    });