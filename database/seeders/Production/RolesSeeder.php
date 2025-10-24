<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\TblRole;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name_role' => 'Super Admin',
                'status' => 1
            ],
            [
                'name_role' => 'Conductor',
                'status' => 1
            ],
            [
                'name_role' => 'Operador',
                'status' => 1
            ],
            [
                'name_role' => 'Cliente',
                'status' => 1
            ]
        ];

        foreach ($roles as $roleData) {
            TblRole::create(array_merge($roleData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('Roles creados exitosamente: ' . count($roles) . ' roles agregados.');
        $this->command->info('Roles disponibles:');
        foreach ($roles as $role) {
            $this->command->info("- {$role['name_role']}");
        }
    }
}