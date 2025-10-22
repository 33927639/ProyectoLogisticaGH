<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol de administrador si no existe
        $adminRole = Role::firstOrCreate([
            'name_role' => 'Administrador'
        ], [
            'status' => true
        ]);

        // Crear rol de usuario si no existe
        $userRole = Role::firstOrCreate([
            'name_role' => 'Usuario'
        ], [
            'status' => true
        ]);

        // Crear rol de conductor si no existe
        $driverRole = Role::firstOrCreate([
            'name_role' => 'Conductor'
        ], [
            'status' => true
        ]);

        // Crear usuario administrador
        $adminUser = User::firstOrCreate([
            'email' => 'admin@sanalogistics.com'
        ], [
            'first_name' => 'Super',
            'last_name' => 'Administrador',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'status' => true
        ]);

        // Asignar rol de administrador al usuario
        $adminUser->roles()->sync([$adminRole->id_role]);

        $this->command->info('Usuario administrador creado exitosamente');
        $this->command->info('Email: admin@sanalogistics.com');
        $this->command->info('Password: admin123');
    }
}


