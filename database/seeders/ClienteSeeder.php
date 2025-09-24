<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        DB::table('clientes')->insert([
            [
                'nombre' => 'Cliente General',
                'email' => 'cliente@ejemplo.com',
                'telefono' => '12345678',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juan@ejemplo.com',
                'telefono' => '87654321',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
