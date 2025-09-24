<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function ventasMesActual()
    {
        $totalVentas = Venta::whereYear('fecha_venta', Carbon::now()->year)
            ->whereMonth('fecha_venta', Carbon::now()->month)
            ->sum('monto');

        return response()->json([
            'total_ventas_mes_actual' => $totalVentas,
            'mes' => Carbon::now()->month,
            'año' => Carbon::now()->year
        ]);
    }

    // Nueva función para la vista HTML
    public function mostrarReporteVentas()
    {
        $totalVentas = Venta::whereYear('fecha_venta', Carbon::now()->year)
            ->whereMonth('fecha_venta', Carbon::now()->month)
            ->sum('monto');

        return view('reporte_ventas', compact('totalVentas'));
    }
}
