<?php

namespace App\Console\Commands;

use App\Models\Route;
use App\Models\Delivery;
use App\Services\GoogleMapsService;
use Illuminate\Console\Command;

class CalculateRouteDistances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maps:calculate-distances {--route-id= : ID específico de ruta} {--all : Calcular todas las rutas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula distancias y tiempos de rutas usando Google Maps API';

    protected $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        parent::__construct();
        $this->googleMapsService = $googleMapsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗺️  Iniciando cálculo de distancias con Google Maps...');

        if (!config('services.google_maps.api_key')) {
            $this->error('❌ Google Maps API key no configurada. Agrega GOOGLE_MAPS_API_KEY en tu archivo .env');
            return 1;
        }

        $routeId = $this->option('route-id');
        $all = $this->option('all');

        if ($routeId) {
            $this->calculateSingleRoute($routeId);
        } elseif ($all) {
            $this->calculateAllRoutes();
        } else {
            $this->info('Selecciona una opción:');
            $this->info('--route-id=ID  : Calcular ruta específica');
            $this->info('--all          : Calcular todas las rutas');
        }

        return 0;
    }

    protected function calculateSingleRoute($routeId)
    {
        $route = Route::find($routeId);
        
        if (!$route) {
            $this->error("❌ Ruta con ID {$routeId} no encontrada");
            return;
        }

        $this->info("📍 Calculando ruta: {$route->route_name}");
        $this->processRoute($route);
    }

    protected function calculateAllRoutes()
    {
        $routes = Route::all();
        
        if ($routes->isEmpty()) {
            $this->warn('⚠️  No hay rutas para procesar');
            return;
        }

        $this->info("📊 Procesando {$routes->count()} rutas...");
        
        $progressBar = $this->output->createProgressBar($routes->count());
        $progressBar->start();

        foreach ($routes as $route) {
            $this->processRoute($route);
            $progressBar->advance();
            
            // Pequeña pausa para no sobrecargar la API
            usleep(200000); // 0.2 segundos
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('✅ Cálculo de todas las rutas completado');
    }

    protected function processRoute($route)
    {
        // Obtener entregas de esta ruta
        $deliveries = Delivery::where('route_id', $route->id_route)->get();
        
        if ($deliveries->isEmpty()) {
            $this->warn("⚠️  Ruta {$route->route_name} no tiene entregas");
            return;
        }

        $origin = "Guatemala City, Guatemala"; // Punto de origen por defecto
        $destinations = [];
        
        // Recopilar destinos de las entregas
        foreach ($deliveries as $delivery) {
            if (isset($delivery->address)) {
                $destinations[] = $delivery->address;
            }
        }

        if (empty($destinations)) {
            $this->warn("⚠️  Ruta {$route->route_name} no tiene direcciones válidas");
            return;
        }

        // Calcular distancias múltiples
        $result = $this->googleMapsService->calculateMultipleStops($origin, $destinations);

        if (!empty($result['stops'])) {
            // Actualizar información de la ruta
            $route->update([
                'total_distance' => $result['total_distance'],
                'estimated_duration' => $result['total_duration'],
                'updated_at' => now()
            ]);

            $this->info("✅ Ruta {$route->route_name}:");
            $this->info("   📏 Distancia total: {$result['total_distance_text']}");
            $this->info("   ⏱️  Tiempo estimado: {$result['total_duration_text']}");
            $this->info("   📦 Entregas: " . count($destinations));
        } else {
            $this->error("❌ Error calculando ruta {$route->route_name}");
        }
    }
}
