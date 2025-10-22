<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\DeliveryStatus;
use App\Models\ExpenseType;
use App\Models\AlertStatus;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Route;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class SanaLogisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeders de SanaLogistics v3.1...');

        // 1. Crear roles
        $this->createRoles();
        
        // 2. Crear usuario administrador
        $this->createAdminUser();
        
        // 3. Crear departamentos y municipios de Guatemala
        $this->createGeography();
        
        // 4. Crear estados de entrega
        $this->createDeliveryStatuses();
        
        // 5. Crear tipos de gastos
        $this->createExpenseTypes();
        
        // 6. Crear alertas de mantenimiento
        $this->createAlertStatuses();
        
        // 7. Crear vehículos de prueba
        $this->createVehicles();
        
        // 8. Crear conductores de prueba
        $this->createDrivers();
        
        // 9. Crear clientes de prueba
        $this->createCustomers();
        
        // 10. Crear rutas de prueba
        $this->createRoutes();
        
        // 11. Crear productos de prueba
        $this->createProducts();

        $this->command->info('✅ Seeders completados exitosamente!');
        $this->command->info('📧 Email admin: admin@sanalogistics.com');
        $this->command->info('🔑 Password admin: admin123');
    }

    private function createRoles()
    {
        $roles = [
            ['name_role' => 'Super Administrador', 'status' => true],
            ['name_role' => 'Administrador', 'status' => true],
            ['name_role' => 'Supervisor', 'status' => true],
            ['name_role' => 'Conductor', 'status' => true],
            ['name_role' => 'Operador', 'status' => true],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name_role' => $role['name_role']], $role);
        }
        $this->command->info('🔐 Roles creados');
    }

    private function createAdminUser()
    {
        $superAdminRole = Role::where('name_role', 'Super Administrador')->first();
        
        $adminUser = User::firstOrCreate([
            'email' => 'admin@sanalogistics.com'
        ], [
            'first_name' => 'Super',
            'last_name' => 'Administrador',
            'username' => 'superadmin',
            'password' => Hash::make('admin123'),
            'status' => true,
            'email_verified_at' => now(),
        ]);

        $adminUser->roles()->sync([$superAdminRole->id_role]);
        $this->command->info('👤 Usuario administrador creado');
    }

    private function createGeography()
    {
        // Departamentos principales de Guatemala
        $departments = [
            'Guatemala', 'Sacatepéquez', 'Chimaltenango', 'Escuintla',
            'Santa Rosa', 'Sololá', 'Totonicapán', 'Quetzaltenango',
            'Suchitepéquez', 'Retalhuleu', 'San Marcos', 'Huehuetenango',
            'Quiché', 'Baja Verapaz', 'Alta Verapaz', 'Petén',
            'Izabal', 'Zacapa', 'Chiquimula', 'Jalapa', 'Jutiapa', 'El Progreso'
        ];

        foreach ($departments as $deptName) {
            $dept = Department::firstOrCreate([
                'name_department' => $deptName,
                'status' => true
            ]);

            // Agregar algunos municipios principales
            if ($deptName === 'Guatemala') {
                $municipalities = ['Guatemala', 'Mixco', 'Villa Nueva', 'Petapa', 'San José Pinula'];
                foreach ($municipalities as $municName) {
                    Municipality::firstOrCreate([
                        'name_municipality' => $municName,
                        'department_id' => $dept->id_department,
                        'status' => true
                    ]);
                }
            }
        }
        $this->command->info('🌍 Geografía creada');
    }

    private function createDeliveryStatuses()
    {
        $statuses = [
            ['name_status' => 'Pendiente', 'description' => 'Entrega programada'],
            ['name_status' => 'En Ruta', 'description' => 'Vehículo en camino'],
            ['name_status' => 'Entregado', 'description' => 'Entrega completada'],
            ['name_status' => 'Cancelado', 'description' => 'Entrega cancelada'],
            ['name_status' => 'Reprogramado', 'description' => 'Nueva fecha asignada'],
        ];

        foreach ($statuses as $status) {
            DeliveryStatus::firstOrCreate(
                ['name_status' => $status['name_status']], 
                array_merge($status, ['status' => true])
            );
        }
        $this->command->info('📦 Estados de entrega creados');
    }

    private function createExpenseTypes()
    {
        $expenseTypes = [
            'Combustible',
            'Mantenimiento',
            'Reparaciones',
            'Seguros',
            'Peajes',
            'Multas',
            'Salarios',
            'Otros gastos operativos'
        ];

        foreach ($expenseTypes as $type) {
            ExpenseType::firstOrCreate([
                'name' => $type,
                'status' => true
            ]);
        }
        $this->command->info('💰 Tipos de gastos creados');
    }

    private function createAlertStatuses()
    {
        $alerts = [
            ['name_alert' => 'Mantenimiento 5,000 km', 'description' => 'Mantenimiento preventivo cada 5,000 km', 'threshold_km' => 5000.00],
            ['name_alert' => 'Mantenimiento 10,000 km', 'description' => 'Mantenimiento mayor cada 10,000 km', 'threshold_km' => 10000.00],
            ['name_alert' => 'Cambio de aceite', 'description' => 'Cambio de aceite cada 3,000 km', 'threshold_km' => 3000.00],
            ['name_alert' => 'Revisión general', 'description' => 'Revisión completa cada 15,000 km', 'threshold_km' => 15000.00],
        ];

        foreach ($alerts as $alert) {
            AlertStatus::firstOrCreate(['name_alert' => $alert['name_alert']], $alert);
        }
        $this->command->info('🔧 Alertas de mantenimiento creadas');
    }

    private function createVehicles()
    {
        $vehicles = [
            [
                'license_plate' => 'P-001-ABC',
                'capacity' => 3500, // kg
                'available' => true,
                'status' => true
            ],
            [
                'license_plate' => 'P-002-DEF',
                'capacity' => 5000, // kg
                'available' => true,
                'status' => true
            ],
            [
                'license_plate' => 'P-003-GHI',
                'capacity' => 2500, // kg
                'available' => false, // En mantenimiento
                'status' => true
            ],
            [
                'license_plate' => 'P-004-JKL',
                'capacity' => 4000, // kg
                'available' => true,
                'status' => true
            ]
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(['license_plate' => $vehicle['license_plate']], $vehicle);
        }
        $this->command->info('🚛 Vehículos de prueba creados');
    }

    private function createDrivers()
    {
        $drivers = [
            [
                'name' => 'Juan Carlos Pérez López',
                'license' => 'GT-001-2023',
                'status' => true
            ],
            [
                'name' => 'María Elena García Morales',
                'license' => 'GT-002-2023',
                'status' => true
            ],
            [
                'name' => 'Roberto Martínez Flores',
                'license' => 'GT-003-2022',
                'status' => true
            ],
            [
                'name' => 'Ana Lucía Rodríguez Castro',
                'license' => 'GT-004-2024',
                'status' => true
            ]
        ];

        foreach ($drivers as $driver) {
            Driver::firstOrCreate(['license' => $driver['license']], $driver);
        }
        $this->command->info('👨‍💼 Conductores de prueba creados');
    }

    private function createCustomers()
    {
        $guatemalaMunicipality = Municipality::where('name_municipality', 'Guatemala')->first();
        $mixcoMunicipality = Municipality::where('name_municipality', 'Mixco')->first();
        
        $customers = [
            [
                'name' => 'Supermercados La Torre',
                'nit' => '12345678-9',
                'phone' => '2234-5678',
                'email' => 'compras@latorre.com.gt',
                'address' => 'Zona 10, Ciudad de Guatemala',
                'municipality_id' => $guatemalaMunicipality?->id_municipality ?? 1,
                'status' => true
            ],
            [
                'name' => 'Distribuidora El Sol',
                'nit' => '87654321-0',
                'phone' => '2456-7890',
                'email' => 'distribuciones@elsol.com',
                'address' => 'Zona 11, Mixco',
                'municipality_id' => $mixcoMunicipality?->id_municipality ?? 1,
                'status' => true
            ],
            [
                'name' => 'Farmacia Central',
                'nit' => '11223344-5',
                'phone' => '2333-4444',
                'email' => 'gerencia@farmaciacentral.gt',
                'address' => 'Zona 1, Centro Histórico',
                'municipality_id' => $guatemalaMunicipality?->id_municipality ?? 1,
                'status' => true
            ],
            [
                'name' => 'Restaurante Los Arcos',
                'nit' => '55667788-9',
                'phone' => '2567-8901',
                'email' => 'administracion@losarcos.gt',
                'address' => 'Zona 14, Ciudad de Guatemala',
                'municipality_id' => $guatemalaMunicipality?->id_municipality ?? 1,
                'status' => true
            ]
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['email' => $customer['email']], $customer);
        }
        $this->command->info('🏢 Clientes de prueba creados');
    }

    private function createRoutes()
    {
        $guatemalaMunicipality = Municipality::where('name_municipality', 'Guatemala')->first();
        $mixcoMunicipality = Municipality::where('name_municipality', 'Mixco')->first();
        $antiguaMunicipality = Municipality::where('name_municipality', 'Antigua Guatemala')->first();
        
        $routes = [
            [
                'origin_id' => $guatemalaMunicipality?->id_municipality ?? 1,
                'destination_id' => $mixcoMunicipality?->id_municipality ?? 2,
                'distance_km' => 15.5,
                'status' => true
            ],
            [
                'origin_id' => $guatemalaMunicipality?->id_municipality ?? 1,
                'destination_id' => $antiguaMunicipality?->id_municipality ?? 3,
                'distance_km' => 45.2,
                'status' => true
            ],
            [
                'origin_id' => $mixcoMunicipality?->id_municipality ?? 2,
                'destination_id' => $antiguaMunicipality?->id_municipality ?? 3,
                'distance_km' => 35.8,
                'status' => true
            ]
        ];

        foreach ($routes as $route) {
            Route::firstOrCreate([
                'origin_id' => $route['origin_id'],
                'destination_id' => $route['destination_id']
            ], $route);
        }
        $this->command->info('🛣️ Rutas de prueba creadas');
    }

    private function createProducts()
    {
        $products = [
            [
                'name' => 'Medicamentos Básicos',
                'sku' => 'MED-001',
                'description' => 'Paquete de medicamentos básicos para farmacias',
                'unit_price' => 150.00,
                'weight_kg' => 5.5,
                'volume_m3' => 0.025,
                'status' => true
            ],
            [
                'name' => 'Productos de Limpieza',
                'sku' => 'CLEAN-001',
                'description' => 'Kit completo de productos de limpieza',
                'unit_price' => 85.50,
                'weight_kg' => 12.0,
                'volume_m3' => 0.040,
                'status' => true
            ],
            [
                'name' => 'Alimentos Enlatados',
                'sku' => 'FOOD-001',
                'description' => 'Variedad de alimentos enlatados para supermercados',
                'unit_price' => 75.25,
                'weight_kg' => 18.5,
                'volume_m3' => 0.030,
                'status' => true
            ],
            [
                'name' => 'Bebidas Refrescantes',
                'sku' => 'DRINK-001',
                'description' => 'Paquete de 24 bebidas gaseosas',
                'unit_price' => 120.00,
                'weight_kg' => 25.0,
                'volume_m3' => 0.055,
                'status' => true
            ]
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }
        $this->command->info('📦 Productos de prueba creados');
    }
}
