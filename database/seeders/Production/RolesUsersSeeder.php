<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TblRole;
use Illuminate\Support\Facades\DB;

class RolesUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios y roles
        $superAdmin = User::where('email', 'admin@sanalogistics.com')->first();
        $conductor = User::where('email', 'conductor@sanalogistics.com')->first();
        
        $adminRole = TblRole::where('name_role', 'Super Admin')->first();
        $conductorRole = TblRole::where('name_role', 'Conductor')->first();

        // Asignar roles
        $assignments = [];

        if ($superAdmin && $adminRole) {
            $assignments[] = [
                'user_id' => $superAdmin->id_user,
                'role_id' => $adminRole->id_role,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if ($conductor && $conductorRole) {
            $assignments[] = [
                'user_id' => $conductor->id_user,
                'role_id' => $conductorRole->id_role,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        foreach ($assignments as $assignment) {
            DB::table('roles_users')->insert($assignment);
        }

        $this->command->info('Asignaciones de roles creadas exitosamente: ' . count($assignments) . ' asignaciones.');
        
        if ($superAdmin && $adminRole) {
            $this->command->info("- Usuario '{$superAdmin->email}' asignado al rol 'Super Admin'");
        }
        
        if ($conductor && $conductorRole) {
            $this->command->info("- Usuario '{$conductor->email}' asignado al rol 'Conductor'");
        }
    }
}