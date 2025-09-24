<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clientes/export/pdf', [CustomerExportController::class, 'exportPdf'])->name('customers.export.pdf');
