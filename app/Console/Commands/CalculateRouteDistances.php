<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Route;
use App\Services\GoogleMapsService;

class CalculateRouteDistances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:calculate-distances {--force : Recalcular todas las distancias, incluso las que ya tienen valor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula automáticamente las distancias de las rutas usando Google Maps API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗺️  Iniciando cálculo de distancias de rutas...');
        
        $googleMaps = app(GoogleMapsService::class);
        
        // Obtener rutas según si se fuerza el recálculo o no
        $routes = $this->option('force') 
            ? Route::with(['origin', 'destination'])->get()
            : Route::with(['origin', 'destination'])->where('distance_km', 0)->get();
        
        $total = $routes->count();
        $processed = 0;
        $errors = 0;

        if ($total === 0) {
            $this->info('✅ No hay rutas para procesar.');
            return 0;
        }

        $this->info("📊 Procesando {$total} rutas...");
        
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        foreach ($routes as $route) {
            try {
                $distanceData = $googleMaps->calculateDistance(
                    $route->origin->name_municipality,
                    $route->destination->name_municipality
                );

                if ($distanceData) {
                    $oldDistance = $route->distance_km;
                    $route->update(['distance_km' => $distanceData['distance_km']]);
                    
                    $this->newLine();
                    $this->line("✅ {$route->route_name}: {$oldDistance} km → {$distanceData['distance_km']} km");
                    $processed++;
                } else {
                    $this->newLine();
                    $this->error("❌ Error calculando: {$route->route_name}");
                    $errors++;
                }

                // Pequeña pausa para no saturar la API
                usleep(100000); // 0.1 segundos

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Error en {$route->route_name}: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        // Resumen
        $this->info("📈 Resumen de procesamiento:");
        $this->table(
            ['Métricas', 'Cantidad'],
            [
                ['Total de rutas', $total],
                ['Procesadas exitosamente', $processed],
                ['Errores', $errors],
                ['Tasa de éxito', round(($processed / $total) * 100, 2) . '%']
            ]
        );

        if ($errors > 0) {
            $this->warn("⚠️  Algunos cálculos fallaron. Verifique su API key de Google Maps y conexión a internet.");
        } else {
            $this->info("🎉 ¡Todas las distancias fueron calculadas exitosamente!");
        }

        return 0;
    }
}
