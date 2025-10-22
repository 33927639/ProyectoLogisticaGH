<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class MultiRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario que puede acceder a ambos paneles (admin y conductor)
        $superUser = User::firstOrCreate([
            'email' => 'super@sanalogistics.com'
        ], [
            'first_name' => 'Super',
            'last_name' => 'Usuario',
            'username' => 'superusuario',
            'password' => bcrypt('password123'),
            'status' => true,
            'email_verified_at' => now(),
        ]);

        // Asignar roles de Super Administrador y Conductor
        $superAdminRole = Role::where('name_role', 'Super Administrador')->first();
        $conductorRole = Role::where('name_role', 'Conductor')->first();

        if ($superAdminRole && $conductorRole) {
            $superUser->roles()->syncWithoutDetaching([$superAdminRole->id_role, $conductorRole->id_role]);
            $this->command->info("Usuario super@sanalogistics.com creado con roles de Super Administrador y Conductor");
        }

        // Verificar que los usuarios existentes estén correctamente configurados
        $this->command->info("Verificando usuarios existentes...");
        
        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $this->command->info("Usuario: {$user->email} - Roles: " . implode(', ', $user->getRoleNames()));
        }
    }
}
