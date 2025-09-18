<?php

namespace App\Http\Controllers;

use App\Http\Requests\RutaRequest;
use App\Models\Ruta;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

/**
 * Class RutaController
 *
 * Handles all route-related operations including CRUD operations,
 * travel time calculations, route optimization, and route analytics.
 *
 * @package App\Http\Controllers
 */
class RutaController extends Controller
{
    /**
     * RutaController constructor.
     * Apply middleware for authentication and authorization.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }

    /**
     * Display a paginated listing of routes with filtering and search capabilities.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Ruta::with(['viajes']);

            // Apply filters
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('origen')) {
                $query->porOrigen($request->origen);
            }

            if ($request->filled('destino')) {
                $query->porDestino($request->destino);
            }

            if ($request->filled('distancia_max')) {
                $query->distanciaMaxima($request->distancia_max);
            }

            if ($request->filled('tiempo_max')) {
                $query->tiempoMaximo($request->tiempo_max);
            }

            if ($request->filled('dificultad')) {
                // This would require a more complex query, for now we'll skip it
            }

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('origen', 'like', "%{$searchTerm}%")
                        ->orWhere('destino', 'like', "%{$searchTerm}%")
                        ->orWhere('descripcion', 'like', "%{$searchTerm}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'origen');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $rutas = $query->paginate($perPage)->appends($request->query());

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $rutas,
                    'message' => 'Rutas retrieved successfully'
                ]);
            }

            return view('rutas.index', compact('rutas'));

        } catch (Exception $e) {
            Log::error('Error in RutaController@index: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error retrieving routes'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'Error al cargar las rutas');
        }
    }

    /**
     * Show the form for creating a new route.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $estados = Ruta::ESTADOS;
            
            // Get existing cities for autocomplete
            $origenes = Ruta::distinct()->pluck('origen')->filter()->sort()->values();
            $destinos = Ruta::distinct()->pluck('destino')->filter()->sort()->values();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'estados' => $estados,
                        'origenes' => $origenes,
                        'destinos' => $destinos
                    ]
                ]);
            }

            return view('rutas.create', compact('estados', 'origenes', 'destinos'));

        } catch (Exception $e) {
            Log::error('Error in RutaController@create: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error loading create form data'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->route('rutas.index')->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Store a newly created route in storage.
     *
     * @param RutaRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(RutaRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            $data['estado'] = $data['estado'] ?? Ruta::ESTADO_ACTIVA;

            $ruta = Ruta::create($data);

            DB::commit();
            
            Log::info('Route created successfully', ['ruta_id' => $ruta->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $ruta,
                    'message' => 'Ruta creada exitosamente'
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('rutas.show', $ruta)
                ->with('success', 'Ruta creada exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in RutaController@store: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified route.
     *
     * @param Ruta $ruta
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Ruta $ruta, Request $request)
    {
        try {
            $ruta->load([
                'viajes' => function ($query) {
                    $query->with(['camion', 'piloto'])
                        ->orderBy('fecha_inicio', 'desc')
                        ->limit(10);
                }
            ]);

            // Calculate additional statistics
            $stats = [
                'total_viajes_completados' => $ruta->total_viajes_completados,
                'tiempo_promedio_real' => $ruta->tiempo_promedio_real,
                'eficiencia_ruta' => $ruta->eficiencia_ruta,
                'velocidad_promedio' => $ruta->velocidad_promedio,
                'consumo_estimado_combustible' => $ruta->consumo_estimado_combustible,
                'dificultad' => $ruta->dificultad,
                'es_ruta_larga' => $ruta->es_ruta_larga,
                'tiene_ruta_inversa' => $ruta->tiene_ruta_inversa,
                'ruta_inversa' => $ruta->ruta_inversa
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => array_merge($ruta->toArray(), ['stats' => $stats])
                ]);
            }

            return view('rutas.show', compact('ruta', 'stats'));

        } catch (Exception $e) {
            Log::error('Error in RutaController@show: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Route not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return redirect()->route('rutas.index')->with('error', 'Ruta no encontrada');
        }
    }

    /**
     * Show the form for editing the specified route.
     *
     * @param Ruta $ruta
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(Ruta $ruta, Request $request)
    {
        try {
            $estados = Ruta::ESTADOS;
            
            // Get existing cities for autocomplete
            $origenes = Ruta::distinct()->pluck('origen')->filter()->sort()->values();
            $destinos = Ruta::distinct()->pluck('destino')->filter()->sort()->values();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'ruta' => $ruta,
                        'estados' => $estados,
                        'origenes' => $origenes,
                        'destinos' => $destinos
                    ]
                ]);
            }

            return view('rutas.edit', compact('ruta', 'estados', 'origenes', 'destinos'));

        } catch (Exception $e) {
            Log::error('Error in RutaController@edit: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified route in storage.
     *
     * @param RutaRequest $request
     * @param Ruta $ruta
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(RutaRequest $request, Ruta $ruta)
    {
        DB::beginTransaction();
        
        try {
            // Check if route has active trips before changing certain fields
            $viajesActivos = $ruta->viajes()
                ->whereIn('estado', [Viaje::ESTADO_PROGRAMADO, Viaje::ESTADO_EN_CURSO])
                ->exists();

            if ($viajesActivos && $request->estado === Ruta::ESTADO_INACTIVA) {
                throw new Exception('No se puede desactivar una ruta con viajes programados o en curso');
            }

            $ruta->update($request->validated());

            DB::commit();
            
            Log::info('Route updated successfully', ['ruta_id' => $ruta->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $ruta,
                    'message' => 'Ruta actualizada exitosamente'
                ]);
            }

            return redirect()->route('rutas.show', $ruta)
                ->with('success', 'Ruta actualizada exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in RutaController@update: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified route from storage.
     *
     * @param Ruta $ruta
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Ruta $ruta, Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Check if route has any trips
            if ($ruta->viajes()->exists()) {
                throw new Exception('No se puede eliminar una ruta con viajes registrados');
            }

            $rutaId = $ruta->id;
            $ruta->delete();

            DB::commit();
            
            Log::info('Route deleted successfully', ['ruta_id' => $rutaId, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ruta eliminada exitosamente'
                ]);
            }

            return redirect()->route('rutas.index')
                ->with('success', 'Ruta eliminada exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in RutaController@destroy: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], Response::HTTP_BAD_REQUEST);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Calculate travel time for a route based on various factors.
     *
     * @param Ruta $ruta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularTiempoViaje(Ruta $ruta, Request $request)
    {
        try {
            $factores = $request->validate([
                'condiciones_clima' => 'nullable|in:buenas,lluvia,neblina,tormenta',
                'trafico' => 'nullable|in:libre,moderado,pesado,congestionado',
                'hora_salida' => 'nullable|date_format:H:i',
                'tipo_carga' => 'nullable|in:ligera,normal,pesada,peligrosa'
            ]);

            $tiempoBase = $ruta->tiempo_estimado_horas;
            $tiempoAjustado = $tiempoBase;

            // Ajustar por condiciones climáticas
            $multiplicadorClima = match($factores['condiciones_clima'] ?? 'buenas') {
                'lluvia' => 1.15,      // 15% más tiempo
                'neblina' => 1.25,     // 25% más tiempo
                'tormenta' => 1.40,    // 40% más tiempo
                default => 1.0
            };

            // Ajustar por tráfico
            $multiplicadorTrafico = match($factores['trafico'] ?? 'libre') {
                'moderado' => 1.10,        // 10% más tiempo
                'pesado' => 1.20,          // 20% más tiempo
                'congestionado' => 1.35,   // 35% más tiempo
                default => 1.0
            };

            // Ajustar por hora de salida
            $multiplicadorHora = 1.0;
            if (!empty($factores['hora_salida'])) {
                $hora = (int) substr($factores['hora_salida'], 0, 2);
                // Horas pico: 7-9 AM y 5-7 PM
                if (($hora >= 7 && $hora <= 9) || ($hora >= 17 && $hora <= 19)) {
                    $multiplicadorHora = 1.15; // 15% más tiempo en horas pico
                }
            }

            // Ajustar por tipo de carga
            $multiplicadorCarga = match($factores['tipo_carga'] ?? 'normal') {
                'ligera' => 0.95,      // 5% menos tiempo
                'pesada' => 1.10,      // 10% más tiempo
                'peligrosa' => 1.20,   // 20% más tiempo (más cuidado)
                default => 1.0
            };

            // Calcular tiempo ajustado
            $tiempoAjustado = $tiempoBase * $multiplicadorClima * $multiplicadorTrafico * 
                             $multiplicadorHora * $multiplicadorCarga;

            // Calcular hora estimada de llegada
            $horaLlegada = null;
            if (!empty($factores['hora_salida'])) {
                $horaSalida = Carbon::createFromFormat('H:i', $factores['hora_salida']);
                $horaLlegada = $horaSalida->copy()->addHours($tiempoAjustado);
            }

            $resultado = [
                'ruta' => [
                    'id' => $ruta->id,
                    'nombre' => $ruta->nombre_completo,
                    'distancia_km' => $ruta->distancia_km,
                ],
                'tiempo_estimado_original_horas' => $tiempoBase,
                'tiempo_estimado_ajustado_horas' => round($tiempoAjustado, 2),
                'diferencia_horas' => round($tiempoAjustado - $tiempoBase, 2),
                'factores_aplicados' => [
                    'clima' => [
                        'condicion' => $factores['condiciones_clima'] ?? 'buenas',
                        'multiplicador' => $multiplicadorClima
                    ],
                    'trafico' => [
                        'nivel' => $factores['trafico'] ?? 'libre',
                        'multiplicador' => $multiplicadorTrafico
                    ],
                    'hora' => [
                        'hora_salida' => $factores['hora_salida'] ?? null,
                        'multiplicador' => $multiplicadorHora
                    ],
                    'carga' => [
                        'tipo' => $factores['tipo_carga'] ?? 'normal',
                        'multiplicador' => $multiplicadorCarga
                    ]
                ],
                'hora_llegada_estimada' => $horaLlegada ? $horaLlegada->format('H:i') : null,
                'velocidad_promedio_ajustada' => round($ruta->distancia_km / $tiempoAjustado, 2)
            ];

            return response()->json([
                'status' => 'success',
                'data' => $resultado,
                'message' => 'Tiempo de viaje calculado exitosamente'
            ]);

        } catch (Exception $e) {
            Log::error('Error in RutaController@calcularTiempoViaje: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error calculating travel time'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Find similar routes based on origin, destination, or distance.
     *
     * @param Ruta $ruta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function encontrarRutasSimilares(Ruta $ruta, Request $request)
    {
        try {
            $criterio = $request->get('criterio', 'origen'); // origen, destino, distancia, tiempo
            
            $query = Ruta::where('id', '!=', $ruta->id)
                ->where('estado', Ruta::ESTADO_ACTIVA);

            switch ($criterio) {
                case 'origen':
                    $rutasSimilares = $query->where('origen', $ruta->origen)
                        ->orderBy('destino')
                        ->get();
                    break;
                
                case 'destino':
                    $rutasSimilares = $query->where('destino', $ruta->destino)
                        ->orderBy('origen')
                        ->get();
                    break;
                
                case 'distancia':
                    $tolerancia = $request->get('tolerancia', 50); // km
                    $rutasSimilares = $query->whereBetween('distancia_km', [
                        $ruta->distancia_km - $tolerancia,
                        $ruta->distancia_km + $tolerancia
                    ])
                    ->orderBy('distancia_km')
                    ->get();
                    break;
                
                case 'tiempo':
                    $tolerancia = $request->get('tolerancia', 2); // horas
                    $rutasSimilares = $query->whereBetween('tiempo_estimado_horas', [
                        $ruta->tiempo_estimado_horas - $tolerancia,
                        $ruta->tiempo_estimado_horas + $tolerancia
                    ])
                    ->orderBy('tiempo_estimado_horas')
                    ->get();
                    break;
                
                default:
                    $rutasSimilares = $query->where('origen', $ruta->origen)
                        ->orderBy('destino')
                        ->get();
            }

            $rutasFormateadas = $rutasSimilares->map(function ($rutaSimilar) use ($ruta, $criterio) {
                $similaridad = $this->calcularSimilaridad($ruta, $rutaSimilar, $criterio);
                
                return [
                    'id' => $rutaSimilar->id,
                    'nombre_completo' => $rutaSimilar->nombre_completo,
                    'origen' => $rutaSimilar->origen,
                    'destino' => $rutaSimilar->destino,
                    'distancia_km' => $rutaSimilar->distancia_km,
                    'tiempo_estimado_horas' => $rutaSimilar->tiempo_estimado_horas,
                    'velocidad_promedio' => $rutaSimilar->velocidad_promedio,
                    'total_viajes_completados' => $rutaSimilar->total_viajes_completados,
                    'eficiencia_ruta' => $rutaSimilar->eficiencia_ruta,
                    'similaridad' => $similaridad
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'ruta_base' => [
                        'id' => $ruta->id,
                        'nombre_completo' => $ruta->nombre_completo
                    ],
                    'criterio_busqueda' => $criterio,
                    'rutas_similares' => $rutasFormateadas,
                    'total_encontradas' => $rutasFormateadas->count()
                ],
                'message' => 'Rutas similares encontradas'
            ]);

        } catch (Exception $e) {
            Log::error('Error in RutaController@encontrarRutasSimilares: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding similar routes'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get route statistics and analytics.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadisticas(Request $request)
    {
        try {
            $stats = [
                'total_rutas' => Ruta::count(),
                'rutas_activas' => Ruta::activas()->count(),
                'rutas_inactivas' => Ruta::inactivas()->count(),
                'distancia_total_rutas' => Ruta::sum('distancia_km'),
                'distancia_promedio' => Ruta::avg('distancia_km'),
                'tiempo_promedio' => Ruta::avg('tiempo_estimado_horas'),
                'velocidad_promedio_flota' => Ruta::avg(DB::raw('distancia_km / tiempo_estimado_horas')),
                'rutas_por_origen' => Ruta::select('origen', DB::raw('count(*) as total'))
                    ->groupBy('origen')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get(),
                'rutas_por_destino' => Ruta::select('destino', DB::raw('count(*) as total'))
                    ->groupBy('destino')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get(),
                'rutas_mas_utilizadas' => Ruta::withCount(['viajes as viajes_count' => function ($q) {
                    $q->where('estado', 'Completado');
                }])
                ->having('viajes_count', '>', 0)
                ->orderBy('viajes_count', 'desc')
                ->limit(10)
                ->get(['id', 'origen', 'destino', 'distancia_km', 'tiempo_estimado_horas'])
                ->map(function ($ruta) {
                    return [
                        'id' => $ruta->id,
                        'nombre_completo' => $ruta->origen . ' → ' . $ruta->destino,
                        'distancia_km' => $ruta->distancia_km,
                        'tiempo_estimado_horas' => $ruta->tiempo_estimado_horas,
                        'total_viajes' => $ruta->viajes_count
                    ];
                }),
                'distribucion_distancias' => [
                    'cortas' => Ruta::where('distancia_km', '<', 100)->count(),
                    'medias' => Ruta::whereBetween('distancia_km', [100, 300])->count(),
                    'largas' => Ruta::where('distancia_km', '>', 300)->count()
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Error in RutaController@estadisticas: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving statistics'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get active routes for trip assignment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activas(Request $request)
    {
        try {
            $rutasActivas = Ruta::activas()
                ->orderBy('origen')
                ->orderBy('destino')
                ->get()
                ->map(function ($ruta) {
                    return [
                        'id' => $ruta->id,
                        'nombre_completo' => $ruta->nombre_completo,
                        'origen' => $ruta->origen,
                        'destino' => $ruta->destino,
                        'distancia_km' => $ruta->distancia_km,
                        'tiempo_estimado_horas' => $ruta->tiempo_estimado_horas,
                        'tiempo_estimado_formato' => $ruta->tiempo_estimado_formato,
                        'velocidad_promedio' => $ruta->velocidad_promedio,
                        'dificultad' => $ruta->dificultad,
                        'consumo_estimado_combustible' => $ruta->consumo_estimado_combustible
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $rutasActivas,
                'total' => $rutasActivas->count()
            ]);

        } catch (Exception $e) {
            Log::error('Error in RutaController@activas: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving active routes'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Calculate similarity between two routes based on criteria.
     *
     * @param Ruta $ruta1
     * @param Ruta $ruta2
     * @param string $criterio
     * @return float
     */
    private function calcularSimilaridad(Ruta $ruta1, Ruta $ruta2, string $criterio): float
    {
        switch ($criterio) {
            case 'distancia':
                $diferencia = abs($ruta1->distancia_km - $ruta2->distancia_km);
                $maxDistancia = max($ruta1->distancia_km, $ruta2->distancia_km);
                return $maxDistancia > 0 ? round((1 - $diferencia / $maxDistancia) * 100, 2) : 100;
            
            case 'tiempo':
                $diferencia = abs($ruta1->tiempo_estimado_horas - $ruta2->tiempo_estimado_horas);
                $maxTiempo = max($ruta1->tiempo_estimado_horas, $ruta2->tiempo_estimado_horas);
                return $maxTiempo > 0 ? round((1 - $diferencia / $maxTiempo) * 100, 2) : 100;
            
            case 'origen':
                return $ruta1->origen === $ruta2->origen ? 100 : 0;
            
            case 'destino':
                return $ruta1->destino === $ruta2->destino ? 100 : 0;
            
            default:
                return 50; // Valor por defecto
        }
    }
}