<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para API JSON
Route::get('/api/ventas-mes-actual', [ReporteController::class, 'ventasMesActual']);

// Ruta para la vista HTML
Route::get('/reporte/ventas-mes', [ReporteController::class, 'mostrarReporteVentas'])->name('reporte.ventas.mes');

// Ruta para reporte mensual (con lógica directa en la ruta)
Route::get('/reporte-mensual', function () {
    $totalVentas = \App\Models\Venta::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('monto');

    return view('venta_mes', compact('totalVentas'));
});
