<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venta;
use Carbon\Carbon;

class VentaSeeder extends Seeder
{
    public function run()
    {
        Venta::create([
            'cliente_id' => 1,
            'monto' => 120.50,
            'fecha_venta' => Carbon::now()->subDays(2),
        ]);

        Venta::create([
            'cliente_id' => 1,
            'monto' => 250.00,
            'fecha_venta' => Carbon::now()->subDays(5),
        ]);

        Venta::create([
            'cliente_id' => 2,
            'monto' => 500.00,
            'fecha_venta' => Carbon::now()->subMonth(),
        ]);
    }
}
