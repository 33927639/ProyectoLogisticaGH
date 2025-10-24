<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder ejecuta todos los seeders de producción en el orden correcto
     * para tener un sistema completamente funcional desde el primer momento.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando configuración de producción para SanaLogistics v3.1');
        $this->command->info('================================================');

        // 1. Departamentos y Municipios (deben ir primero por las relaciones)
        $this->command->info('📍 Creando departamentos y municipios de Guatemala...');
        $this->call(DepartmentsAndMunicipalitiesSeeder::class);

        // 2. Roles del sistema
        $this->command->info('🔐 Creando roles del sistema...');
        $this->call(RolesSeeder::class);

        // 3. Usuarios
        $this->command->info('👥 Creando usuarios del sistema...');
        $this->call(UsersSeeder::class);

        // 4. Asignación de roles a usuarios
        $this->command->info('👤 Asignando roles a usuarios...');
        $this->call(RolesUsersSeeder::class);

        // 5. Productos
        $this->command->info('📦 Creando catálogo de productos...');
        $this->call(ProductsSeeder::class);

        // 6. Conductores
        $this->command->info('🚗 Creando conductores...');
        $this->call(DriversSeeder::class);

        // 7. Vehículos
        $this->command->info('🚛 Creando flota de vehículos...');
        $this->call(VehiclesSeeder::class);

        // 8. Clientes (después de departamentos y municipios)
        $this->command->info('🏢 Creando clientes base...');
        $this->call(CustomersSeeder::class);

        // 9. Estados de alertas
        $this->command->info('🚨 Creando estados de alertas de mantenimiento...');
        $this->call(AlertStatusesSeeder::class);

        $this->command->info('================================================');
        $this->command->info('✅ Configuración de producción completada exitosamente!');
        $this->command->info('');
        $this->command->info('🔐 CREDENCIALES DE ACCESO:');
        $this->command->info('Super Admin:');
        $this->command->info('  📧 Email: admin@sanalogistics.com');
        $this->command->info('  🔑 Password: AdminSana2024!');
        $this->command->info('');
        $this->command->info('Conductor:');
        $this->command->info('  📧 Email: conductor@sanalogistics.com');
        $this->command->info('  🔑 Password: Conductor2024!');
        $this->command->info('');
        $this->command->info('📊 DATOS CREADOS:');
        $this->command->info('  • 22 Departamentos con 340+ municipios');
        $this->command->info('  • 4 Roles de usuario');
        $this->command->info('  • 2 Usuarios con roles asignados');
        $this->command->info('  • 5 Productos');
        $this->command->info('  • 5 Conductores');
        $this->command->info('  • 5 Vehículos');
        $this->command->info('  • 5 Clientes');
        $this->command->info('  • 6 Estados de alertas de mantenimiento');
        $this->command->info('');
        $this->command->info('🌟 El sistema está listo para uso en producción');
        $this->command->info('================================================');
    }
}