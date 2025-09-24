<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// 👉 Rutas para exportar e imprimir productos
Route::get('/productos/export', [ProductController::class, 'export'])->name('products.export');
Route::get('/productos/print', [ProductController::class, 'print'])->name('products.print');
