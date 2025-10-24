<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Super Admin
        $superAdmin = User::create([
            'name' => 'Super',
            'first_name' => 'Admin',
            'last_name' => 'Sistema',
            'username' => 'superadmin',
            'email' => 'admin@sanalogistics.com',
            'email_verified_at' => now(),
            'password' => Hash::make('AdminSana2024!'),
            'remember_token' => Str::random(10),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear Conductor
        $driver = User::create([
            'name' => 'Juan Carlos',
            'first_name' => 'Juan',
            'last_name' => 'López Morales',
            'username' => 'conductor01',
            'email' => 'conductor@sanalogistics.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Conductor2024!'),
            'remember_token' => Str::random(10),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('Usuarios creados exitosamente:');
        $this->command->info('Super Admin - Email: admin@sanalogistics.com | Password: AdminSana2024!');
        $this->command->info('Conductor - Email: conductor@sanalogistics.com | Password: Conductor2024!');
    }
}