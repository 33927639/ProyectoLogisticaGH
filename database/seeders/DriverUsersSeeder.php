<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;

class DriverUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios para los conductores existentes
        $drivers = [
            [
                'id_driver' => 1,
                'first_name' => 'Juan Carlos',
                'last_name' => 'Pérez López',
                'email' => 'juan.perez@sanalogistics.com'
            ],
            [
                'id_driver' => 2,
                'first_name' => 'María Elena',
                'last_name' => 'García Morales',
                'email' => 'maria.garcia@sanalogistics.com'
            ],
            [
                'id_driver' => 3,
                'first_name' => 'Roberto',
                'last_name' => 'Martínez Flores',
                'email' => 'roberto.martinez@sanalogistics.com'
            ],
            [
                'id_driver' => 4,
                'first_name' => 'Ana Lucía',
                'last_name' => 'Rodríguez Castro',
                'email' => 'ana.rodriguez@sanalogistics.com'
            ]
        ];

        foreach ($drivers as $driverData) {
            // Verificar si el usuario ya existe
            $existingUser = User::where('email', $driverData['email'])->first();
            
            if (!$existingUser) {
                User::create([
                    'first_name' => $driverData['first_name'],
                    'last_name' => $driverData['last_name'],
                    'email' => $driverData['email'],
                    'password' => Hash::make('conductor123'), // Contraseña estándar para testing
                    'role_id' => 1 // Asumiendo que role_id 1 es un rol válido
                ]);
                
                echo "Usuario creado: {$driverData['email']}\n";
            } else {
                echo "Usuario ya existe: {$driverData['email']}\n";
            }
        }

        echo "¡Usuarios conductores creados exitosamente!\n";
        echo "Credenciales de acceso:\n";
        echo "URL: http://localhost/conductor\n";
        echo "Email: juan.perez@sanalogistics.com\n";
        echo "Password: conductor123\n";
    }
}