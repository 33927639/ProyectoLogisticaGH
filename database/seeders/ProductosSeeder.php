<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\TblProduct::firstOrCreate([
            'name' => 'HP',
            'stock' => 20,
            'price' => 7000,
            'status' => 1,
            'descripcion' => 'Laptop HP de gama media, ideal para oficina y estudio',
        ]);

        \App\Models\TblProduct::firstOrCreate([
            'name' => 'Apple',
            'stock' => 5,
            'price' => 15000,
            'status' => 1,
            'descripcion' => 'MacBook Air M1 de alto rendimiento',
        ]);

        \App\Models\TblProduct::firstOrCreate([
            'name' => 'Lenovo',
            'stock' => 15,
            'price' => 8000,
            'status' => 1,
            'descripcion' => 'Laptop Lenovo con buena relación precio/rendimiento',
        ]);
    }
}
