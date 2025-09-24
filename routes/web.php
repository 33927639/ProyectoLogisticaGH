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
