<?php

namespace App\Console\Commands;

use App\Services\GoogleMapsService;
use Illuminate\Console\Command;

class TestGoogleMaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:google-maps {origin=Guatemala} {destination=Mixco}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la conexión con Google Maps API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗺️  Probando Google Maps API...');
        
        $origin = $this->argument('origin');
        $destination = $this->argument('destination');
        
        $this->info("📍 Origen: {$origin}");
        $this->info("📍 Destino: {$destination}");
        
        $googleMapsService = app(GoogleMapsService::class);
        
        // Formatear direcciones para Guatemala
        $originFormatted = $origin . ', Guatemala';
        $destinationFormatted = $destination . ', Guatemala';
        
        $this->info("🔍 Calculando distancia de {$originFormatted} a {$destinationFormatted}...");
        
        $result = $googleMapsService->calculateDistance($originFormatted, $destinationFormatted);
        
        $this->newLine();
        $this->info('📊 RESULTADO:');
        $this->info("Status: {$result['status']}");
        
        if ($result['status'] === 'OK') {
            $this->info("✅ Distancia: {$result['distance_text']}");
            $this->info("⏱️  Tiempo: {$result['duration_text']}");
            $this->info("📏 Distancia (km): " . number_format($result['distance'], 2));
            $this->info("🕒 Duración (min): " . number_format($result['duration'], 0));
        } else {
            $this->error("❌ Error: {$result['status']}");
            if (isset($result['error_message'])) {
                $this->error("Mensaje: {$result['error_message']}");
            }
        }
        
        return 0;
    }
}
