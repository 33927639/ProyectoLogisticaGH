<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Cemento Portland',
                'sku' => 'CEM-PORT-001',
                'description' => 'Cemento de alta calidad para construcción',
                'unit_price' => 89.50,
                'weight_kg' => 42.5,
                'volume_m3' => 0.03
            ],
            [
                'name' => 'Varilla de Hierro 3/8"',
                'sku' => 'VAR-HIE-038',
                'description' => 'Varilla corrugada de hierro para refuerzo estructural',
                'unit_price' => 45.75,
                'weight_kg' => 6.8,
                'volume_m3' => 0.001
            ],
            [
                'name' => 'Block de Concreto 8"',
                'sku' => 'BLO-CON-08',
                'description' => 'Block hueco de concreto para mampostería',
                'unit_price' => 3.25,
                'weight_kg' => 12.5,
                'volume_m3' => 0.008
            ],
            [
                'name' => 'Arena de Río (M³)',
                'sku' => 'ARE-RIO-M3',
                'description' => 'Arena lavada de río para mezclas de concreto',
                'unit_price' => 285.00,
                'weight_kg' => 1600.0,
                'volume_m3' => 1.0
            ],
            [
                'name' => 'Electrodoméstico - Refrigeradora',
                'sku' => 'ELE-REF-001',
                'description' => 'Refrigeradora 18 pies cúbicos marca premium',
                'unit_price' => 3850.00,
                'weight_kg' => 85.0,
                'volume_m3' => 1.2
            ]
        ];

        foreach ($products as $productData) {
            Product::create([
                'name' => $productData['name'],
                'sku' => $productData['sku'],
                'description' => $productData['description'],
                'unit_price' => $productData['unit_price'],
                'weight_kg' => $productData['weight_kg'],
                'volume_m3' => $productData['volume_m3'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Productos creados exitosamente: ' . count($products) . ' productos agregados.');
    }
}