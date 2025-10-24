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
        $this->command->info('🌱 Iniciando seeders de SanaLogistics v3.1');

        // Ejecutar el seeder principal que incluye todo
        $this->call(SanaLogisticsSeeder::class);

        $this->command->info('✅ Base de datos poblada correctamente!');
    }
}


