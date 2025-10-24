<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\TblCustomer;
use App\Models\TblMunicipality;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos municipios para los clientes
        $guatemalaCity = TblMunicipality::where('name_municipality', 'Guatemala')->first();
        $mixco = TblMunicipality::where('name_municipality', 'Mixco')->first();
        $villaNueva = TblMunicipality::where('name_municipality', 'Villa Nueva')->first();

        $customers = [
            [
                'name' => 'Constructora San Miguel S.A.',
                'nit' => '12345678-9',
                'phone' => '+502 2334-5678',
                'email' => 'ventas@constructorasanmiguel.com',
                'address' => '15 Calle 8-45 Zona 10, Edificio Empresarial Torre IV, Oficina 502',
                'municipality_id' => $guatemalaCity ? $guatemalaCity->id_municipality : 1,
                'status' => 1
            ],
            [
                'name' => 'Ferretería El Progreso',
                'nit' => '23456789-0',
                'phone' => '+502 2445-7890',
                'email' => 'compras@ferreteriaelprogreso.com',
                'address' => '8va Avenida 12-30 Zona 1, Local 15',
                'municipality_id' => $guatemalaCity ? $guatemalaCity->id_municipality : 1,
                'status' => 1
            ],
            [
                'name' => 'Hogar Moderno Guatemala',
                'nit' => '34567890-1',
                'phone' => '+502 2556-1234',
                'email' => 'gerencia@hogarmoderno.gt',
                'address' => 'Centro Comercial Miraflores, Local 125-130',
                'municipality_id' => $mixco ? $mixco->id_municipality : 1,
                'status' => 1
            ],
            [
                'name' => 'María Elena Rodríguez',
                'nit' => '45678901-2',
                'phone' => '+502 5678-9012',
                'email' => 'merodriguez@gmail.com',
                'address' => 'Residenciales San Cristóbal, Casa #25',
                'municipality_id' => $mixco ? $mixco->id_municipality : 1,
                'status' => 1
            ],
            [
                'name' => 'Servicios Logísticos Centroamérica',
                'nit' => '56789012-3',
                'phone' => '+502 2667-3456',
                'email' => 'operaciones@slcentroamerica.com',
                'address' => 'Km 15.5 Carretera a El Salvador, Bodega Industrial #8',
                'municipality_id' => $villaNueva ? $villaNueva->id_municipality : 1,
                'status' => 1
            ]
        ];

        foreach ($customers as $customerData) {
            TblCustomer::create(array_merge($customerData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info('Clientes creados exitosamente: ' . count($customers) . ' clientes agregados.');
    }
}