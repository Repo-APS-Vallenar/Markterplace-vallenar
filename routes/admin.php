<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::prefix('admin')
    ->middleware(['auth', 'checkrole:admin'])
    ->as('admin.')
    ->group(function () {
        // Dashboard de Admin
        Route::get('/', [AdminController::class, 'index'])->name('index');

        // Gestión de usuarios
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    });
