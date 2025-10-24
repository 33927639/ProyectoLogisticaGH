<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    /**
     * Calculate distance and duration between two addresses
     * Using fallback method with approximate calculations
     */
    public function calculateDistance($origin, $destination)
    {
        if (!$this->apiKey) {
            Log::warning('Google Maps API key not configured');
            return [
                'distance' => 0,
                'duration' => 0,
                'distance_text' => 'No calculado',
                'duration_text' => 'No calculado',
                'status' => 'API_KEY_MISSING'
            ];
        }

        // Primero intentar con las APIs nuevas, luego fallback a cálculo aproximado
        try {
            // Intentar Routes API
            $result = $this->tryRoutesAPI($origin, $destination);
            if ($result['status'] === 'OK') {
                return $result;
            }

            // Intentar Distance Matrix API
            $result = $this->tryDistanceMatrixAPI($origin, $destination);
            if ($result['status'] === 'OK') {
                return $result;
            }

            // Fallback: Cálculo aproximado usando distancias conocidas de Guatemala
            return $this->calculateApproximateDistance($origin, $destination);

        } catch (\Exception $e) {
            Log::error('Error calculating distance', [
                'error' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination
            ]);

            return $this->calculateApproximateDistance($origin, $destination);
        }
    }

    /**
     * Calculate approximate distance using known Guatemala city distances
     */
    private function calculateApproximateDistance($origin, $destination)
    {
        // Mapeo de distancias aproximadas conocidas en Guatemala (en km)
        $knownDistances = [
            // Guatemala City conexiones principales
            'guatemala-mixco' => ['distance' => 15.5, 'duration' => 35],
            'mixco-guatemala' => ['distance' => 15.5, 'duration' => 35],
            'guatemala-villa nueva' => ['distance' => 22.8, 'duration' => 40],
            'villa nueva-guatemala' => ['distance' => 22.8, 'duration' => 40],
            'guatemala-san josé pinula' => ['distance' => 25.0, 'duration' => 35],
            'san josé pinula-guatemala' => ['distance' => 25.0, 'duration' => 35],
            'guatemala-antigua guatemala' => ['distance' => 45.0, 'duration' => 60],
            'antigua guatemala-guatemala' => ['distance' => 45.0, 'duration' => 60],
            'guatemala-amatitlán' => ['distance' => 27.0, 'duration' => 45],
            'amatitlán-guatemala' => ['distance' => 27.0, 'duration' => 45],
            'guatemala-san miguel petapa' => ['distance' => 18.5, 'duration' => 30],
            'san miguel petapa-guatemala' => ['distance' => 18.5, 'duration' => 30],
            'guatemala-santa catarina pinula' => ['distance' => 14.2, 'duration' => 25],
            'santa catarina pinula-guatemala' => ['distance' => 14.2, 'duration' => 25],
            'guatemala-chinautla' => ['distance' => 12.8, 'duration' => 25],
            'chinautla-guatemala' => ['distance' => 12.8, 'duration' => 25],
            
            // Mixco conexiones
            'mixco-villa nueva' => ['distance' => 35.8, 'duration' => 50],
            'villa nueva-mixco' => ['distance' => 35.8, 'duration' => 50],
            'mixco-san josé pinula' => ['distance' => 38.0, 'duration' => 55],
            'san josé pinula-mixco' => ['distance' => 38.0, 'duration' => 55],
            'mixco-antigua guatemala' => ['distance' => 52.0, 'duration' => 75],
            'antigua guatemala-mixco' => ['distance' => 52.0, 'duration' => 75],
            'mixco-amatitlán' => ['distance' => 41.5, 'duration' => 60],
            'amatitlán-mixco' => ['distance' => 41.5, 'duration' => 60],
            'mixco-chinautla' => ['distance' => 28.0, 'duration' => 45],
            'chinautla-mixco' => ['distance' => 28.0, 'duration' => 45],
            
            // Villa Nueva conexiones
            'villa nueva-amatitlán' => ['distance' => 8.5, 'duration' => 15],
            'amatitlán-villa nueva' => ['distance' => 8.5, 'duration' => 15],
            'villa nueva-san miguel petapa' => ['distance' => 12.0, 'duration' => 20],
            'san miguel petapa-villa nueva' => ['distance' => 12.0, 'duration' => 20],
            'villa nueva-antigua guatemala' => ['distance' => 65.0, 'duration' => 90],
            'antigua guatemala-villa nueva' => ['distance' => 65.0, 'duration' => 90],
            
            // Departamentales importantes
            'guatemala-escuintla' => ['distance' => 55.0, 'duration' => 70],
            'escuintla-guatemala' => ['distance' => 55.0, 'duration' => 70],
            'guatemala-chimaltenango' => ['distance' => 54.0, 'duration' => 75],
            'chimaltenango-guatemala' => ['distance' => 54.0, 'duration' => 75],
            'guatemala-sacatepéquez' => ['distance' => 45.0, 'duration' => 60],
            'sacatepéquez-guatemala' => ['distance' => 45.0, 'duration' => 60],
            'guatemala-jalapa' => ['distance' => 97.0, 'duration' => 130],
            'jalapa-guatemala' => ['distance' => 97.0, 'duration' => 130],
            'guatemala-jutiapa' => ['distance' => 124.0, 'duration' => 160],
            'jutiapa-guatemala' => ['distance' => 124.0, 'duration' => 160],
            'guatemala-santa rosa' => ['distance' => 64.0, 'duration' => 85],
            'santa rosa-guatemala' => ['distance' => 64.0, 'duration' => 85],
            
            // Rutas interdepartamentales comunes
            'chimaltenango-escuintla' => ['distance' => 75.0, 'duration' => 95],
            'escuintla-chimaltenango' => ['distance' => 75.0, 'duration' => 95],
            'antigua guatemala-escuintla' => ['distance' => 85.0, 'duration' => 110],
            'escuintla-antigua guatemala' => ['distance' => 85.0, 'duration' => 110],
            'mixco-escuintla' => ['distance' => 68.0, 'duration' => 90],
            'escuintla-mixco' => ['distance' => 68.0, 'duration' => 90],
        ];

        // Normalizar nombres de ciudades para búsqueda
        $originKey = $this->normalizeLocationName($origin);
        $destinationKey = $this->normalizeLocationName($destination);
        $routeKey = $originKey . '-' . $destinationKey;

        if (isset($knownDistances[$routeKey])) {
            $data = $knownDistances[$routeKey];
            return [
                'distance' => $data['distance'],
                'duration' => $data['duration'],
                'distance_text' => number_format($data['distance'], 1) . ' km',
                'duration_text' => $data['duration'] . ' min',
                'status' => 'OK'
            ];
        }

        // Si no se encuentra una distancia conocida, calcular aproximación básica
        $approximateDistance = $this->estimateDistanceByRegion($originKey, $destinationKey);
        $approximateDuration = round($approximateDistance * 1.5); // ~1.5 min por km promedio

        return [
            'distance' => $approximateDistance,
            'duration' => $approximateDuration,
            'distance_text' => $approximateDistance . ' km (est.)',
            'duration_text' => $approximateDuration . ' min (est.)',
            'status' => 'OK'
        ];
    }

    /**
     * Normalize location names for consistent matching
     */
    private function normalizeLocationName($location)
    {
        // Remover sufijos comunes
        $normalized = strtolower(trim($location));
        $normalized = str_replace([', guatemala', ' guatemala', 'municipio de ', 'departamento de '], '', $normalized);
        
        // Normalizar nombres específicos conocidos
        $aliases = [
            'guatemala city' => 'guatemala',
            'ciudad de guatemala' => 'guatemala',
            'guate' => 'guatemala',
            'san josé pinula' => 'san josé pinula',
            'san jose pinula' => 'san josé pinula',
            'villa nueva' => 'villa nueva',
            'antigua' => 'antigua guatemala',
            'santa catarina pinula' => 'santa catarina pinula',
            'san miguel petapa' => 'san miguel petapa',
        ];

        return $aliases[$normalized] ?? $normalized;
    }

    /**
     * Estimate distance based on geographic regions
     */
    private function estimateDistanceByRegion($origin, $destination)
    {
        // Definir regiones y sus centros aproximados
        $metropolitanArea = ['guatemala', 'mixco', 'villa nueva', 'san josé pinula', 'santa catarina pinula', 'chinautla', 'san miguel petapa', 'amatitlán'];
        $nearbyDepartments = ['antigua guatemala', 'chimaltenango', 'escuintla', 'sacatepéquez'];
        $distantDepartments = ['jalapa', 'jutiapa', 'santa rosa', 'chiquimula', 'zacapa'];

        $originRegion = $this->getRegion($origin, $metropolitanArea, $nearbyDepartments, $distantDepartments);
        $destRegion = $this->getRegion($destination, $metropolitanArea, $nearbyDepartments, $distantDepartments);

        // Matriz de distancias aproximadas entre regiones
        $regionDistances = [
            'metropolitan-metropolitan' => 25,
            'metropolitan-nearby' => 55,
            'metropolitan-distant' => 120,
            'nearby-nearby' => 45,
            'nearby-distant' => 140,
            'distant-distant' => 180,
        ];

        $key = $originRegion . '-' . $destRegion;
        return $regionDistances[$key] ?? 80; // Default 80km
    }

    /**
     * Determine which region a location belongs to
     */
    private function getRegion($location, $metropolitan, $nearby, $distant)
    {
        if (in_array($location, $metropolitan)) {
            return 'metropolitan';
        } elseif (in_array($location, $nearby)) {
            return 'nearby';
        } elseif (in_array($location, $distant)) {
            return 'distant';
        }
        return 'nearby'; // Default to nearby
    }

    /**
     * Try Routes API (New)
     */
    private function tryRoutesAPI($origin, $destination)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $this->apiKey,
                    'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.staticDuration'
                ])
                ->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
                    'origin' => ['address' => $origin],
                    'destination' => ['address' => $destination],
                    'travelMode' => 'DRIVE',
                    'routingPreference' => 'TRAFFIC_AWARE',
                    'departureTime' => now()->toISOString(),
                    'languageCode' => 'es-GT',
                    'regionCode' => 'GT'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['routes'][0])) {
                    $route = $data['routes'][0];
                    $distanceKm = ($route['distanceMeters'] ?? 0) / 1000;
                    $durationMin = (int) str_replace('s', '', $route['duration'] ?? '0s') / 60;
                    
                    return [
                        'distance' => $distanceKm,
                        'duration' => $durationMin,
                        'distance_text' => number_format($distanceKm, 2) . ' km',
                        'duration_text' => number_format($durationMin, 0) . ' min',
                        'status' => 'OK'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::info('Routes API failed, trying fallback');
        }

        return ['status' => 'ERROR'];
    }

    /**
     * Try Distance Matrix API (Legacy)
     */
    private function tryDistanceMatrixAPI($origin, $destination)
    {
        try {
            $response = Http::timeout(30)->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $origin,
                'destinations' => $destination,
                'units' => 'metric',
                'language' => 'es',
                'region' => 'GT',
                'departure_time' => 'now',
                'traffic_model' => 'best_guess',
                'key' => $this->apiKey
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && isset($data['rows'][0]['elements'][0])) {
                $element = $data['rows'][0]['elements'][0];
                if ($element['status'] === 'OK') {
                    return [
                        'distance' => $element['distance']['value'] / 1000,
                        'duration' => $element['duration']['value'] / 60,
                        'distance_text' => $element['distance']['text'],
                        'duration_text' => $element['duration']['text'],
                        'status' => 'OK'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::info('Distance Matrix API failed, using fallback');
        }

        return ['status' => 'ERROR'];
    }

    /**
     * Geocode an address to get coordinates
     */
    public function geocodeAddress($address)
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'language' => 'es',
                'key' => $this->apiKey
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && isset($data['results'][0])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'lat' => $location['lat'],
                    'lng' => $location['lng'],
                    'formatted_address' => $data['results'][0]['formatted_address']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error geocoding address', [
                'error' => $e->getMessage(),
                'address' => $address
            ]);
            return null;
        }
    }

    /**
     * Calculate route with multiple waypoints
     */
    public function calculateRoute($origin, $destination, $waypoints = [])
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $params = [
                'origin' => $origin,
                'destination' => $destination,
                'language' => 'es',
                'units' => 'metric',
                'key' => $this->apiKey
            ];

            if (!empty($waypoints)) {
                $params['waypoints'] = implode('|', $waypoints);
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', $params);

            $data = $response->json();

            if ($data['status'] === 'OK' && isset($data['routes'][0])) {
                $route = $data['routes'][0];
                $leg = $route['legs'][0];

                return [
                    'distance' => $leg['distance']['value'] / 1000, // kilometers
                    'duration' => $leg['duration']['value'] / 60, // minutes
                    'distance_text' => $leg['distance']['text'],
                    'duration_text' => $leg['duration']['text'],
                    'polyline' => $route['overview_polyline']['points'],
                    'steps' => $leg['steps'],
                    'status' => 'OK'
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error calculating route', [
                'error' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination
            ]);
            return null;
        }
    }

    /**
     * Get multiple addresses and calculate total route distance
     */
    public function calculateMultipleStops($origin, $destinations)
    {
        if (!$this->apiKey || empty($destinations)) {
            return [];
        }

        $results = [];
        $totalDistance = 0;
        $totalDuration = 0;

        foreach ($destinations as $index => $destination) {
            $result = $this->calculateDistance($origin, $destination);
            $result['stop_number'] = $index + 1;
            $result['destination'] = $destination;
            
            $totalDistance += $result['distance'];
            $totalDuration += $result['duration'];
            
            $results[] = $result;
        }

        return [
            'stops' => $results,
            'total_distance' => $totalDistance,
            'total_duration' => $totalDuration,
            'total_distance_text' => number_format($totalDistance, 2) . ' km',
            'total_duration_text' => number_format($totalDuration, 0) . ' min'
        ];
    }
}
