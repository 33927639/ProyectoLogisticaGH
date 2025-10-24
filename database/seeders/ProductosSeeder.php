<?php

namespace Database\Seeders;

use App\Models\TblProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TblProduct::firstOrCreate([
            'name' => 'HP',
            'stock' => 20,
            'price' => 7000,
            'status' => 1,
        ]);
        TblProduct::firstOrCreate([
            'name' => 'Apple',
            'stock' => 5,
            'price' => 15000,
            'status' => 1,
        ]);
        TblProduct::firstOrCreate([
            'name' => 'Lenovo',
            'stock' => 15,
            'price' => 8000,
            'status' => 1,
        ]);
        TblProduct::firstOrCreate([
            'name' => 'Asus',
            'stock' => 15,
            'price' => 10000,
            'status' => 1,
        ]);
        TblProduct::firstOrCreate([
            'name' => 'Acer',
            'stock' => 9,
            'price' => 5000,
            'status' => 1,
        ]);
    }
}
