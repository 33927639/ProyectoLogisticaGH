<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Si quieres conservar el usuario de prueba, lo dejas.
        // Si no, puedes borrarlo.

        // Llamar a tu seeder de Admin
        $this->call(AdminUserSeeder::class);
    }
}


