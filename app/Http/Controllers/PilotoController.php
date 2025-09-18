<?php

namespace App\Http\Controllers;

use App\Http\Requests\PilotoRequest;
use App\Models\Piloto;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

/**
 * Class PilotoController
 *
 * Handles all driver-related operations including CRUD operations,
 * availability management, performance tracking, and status changes.
 *
 * @package App\Http\Controllers
 */
class PilotoController extends Controller
{
    /**
     * PilotoController constructor.
     * Apply middleware for authentication and authorization.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }

    /**
     * Display a paginated listing of drivers with filtering and search capabilities.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Piloto::with(['viajes']);

            // Apply filters
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('disponibilidad')) {
                if ($request->disponibilidad === 'disponibles') {
                    $query->disponibles();
                } elseif ($request->disponibilidad === 'ocupados') {
                    $query->whereHas('viajes', function ($q) {
                        $q->where('estado', 'En Curso');
                    });
                }
            }

            if ($request->filled('experiencia')) {
                // Filter by experience level based on completed trips
                $minTrips = match($request->experiencia) {
                    'nuevo' => 0,
                    'principiante' => 5,
                    'intermedio' => 20,
                    'avanzado' => 50,
                    'experto' => 100,
                    default => 0
                };
                
                $maxTrips = match($request->experiencia) {
                    'nuevo' => 4,
                    'principiante' => 19,
                    'intermedio' => 49,
                    'avanzado' => 99,
                    'experto' => PHP_INT_MAX,
                    default => PHP_INT_MAX
                };

                $query->withCount(['viajes as viajes_completados_count' => function ($q) {
                    $q->where('estado', 'Completado');
                }])
                ->having('viajes_completados_count', '>=', $minTrips)
                ->having('viajes_completados_count', '<=', $maxTrips);
            }

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nombre', 'like', "%{$searchTerm}%")
                        ->orWhere('apellido', 'like', "%{$searchTerm}%")
                        ->orWhere('licencia', 'like', "%{$searchTerm}%")
                        ->orWhere('telefono', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'nombre');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $pilotos = $query->paginate($perPage)->appends($request->query());

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $pilotos,
                    'message' => 'Pilotos retrieved successfully'
                ]);
            }

            return view('pilotos.index', compact('pilotos'));

        } catch (Exception $e) {
            Log::error('Error in PilotoController@index: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error retrieving drivers'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'Error al cargar los pilotos');
        }
    }

    /**
     * Show the form for creating a new driver.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $estados = Piloto::ESTADOS;
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'estados' => $estados
                    ]
                ]);
            }

            return view('pilotos.create', compact('estados'));

        } catch (Exception $e) {
            Log::error('Error in PilotoController@create: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error loading create form data'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->route('pilotos.index')->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Store a newly created driver in storage.
     *
     * @param PilotoRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(PilotoRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            $data['estado'] = $data['estado'] ?? Piloto::ESTADO_ACTIVO;

            $piloto = Piloto::create($data);

            DB::commit();
            
            Log::info('Driver created successfully', ['piloto_id' => $piloto->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $piloto,
                    'message' => 'Piloto creado exitosamente'
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('pilotos.show', $piloto)
                ->with('success', 'Piloto creado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in PilotoController@store: ' . $e->getMessage());
            
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
     * Display the specified driver.
     *
     * @param Piloto $piloto
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Piloto $piloto, Request $request)
    {
        try {
            $piloto->load([
                'viajes' => function ($query) {
                    $query->with(['ruta', 'camion'])
                        ->orderBy('fecha_inicio', 'desc')
                        ->limit(10);
                }
            ]);

            // Calculate additional statistics
            $stats = [
                'total_viajes_completados' => $piloto->total_viajes_completados,
                'total_kilometros' => $piloto->total_kilometros_recorridos,
                'nivel_experiencia' => $piloto->nivel_experiencia,
                'viaje_actual' => $piloto->viaje_actual,
                'esta_disponible' => $piloto->esta_disponible,
                'estado_display' => $piloto->estado_display
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => array_merge($piloto->toArray(), ['stats' => $stats])
                ]);
            }

            return view('pilotos.show', compact('piloto', 'stats'));

        } catch (Exception $e) {
            Log::error('Error in PilotoController@show: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Driver not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return redirect()->route('pilotos.index')->with('error', 'Piloto no encontrado');
        }
    }

    /**
     * Show the form for editing the specified driver.
     *
     * @param Piloto $piloto
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(Piloto $piloto, Request $request)
    {
        try {
            $estados = Piloto::ESTADOS;

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'piloto' => $piloto,
                        'estados' => $estados
                    ]
                ]);
            }

            return view('pilotos.edit', compact('piloto', 'estados'));

        } catch (Exception $e) {
            Log::error('Error in PilotoController@edit: ' . $e->getMessage());
            
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
     * Update the specified driver in storage.
     *
     * @param PilotoRequest $request
     * @param Piloto $piloto
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(PilotoRequest $request, Piloto $piloto)
    {
        DB::beginTransaction();
        
        try {
            // Check if driver has active trips before changing certain fields
            if ($piloto->viaje_actual && 
                in_array($request->estado, [Piloto::ESTADO_INACTIVO, Piloto::ESTADO_SUSPENDIDO])) {
                throw new Exception('No se puede cambiar el estado del piloto con un viaje activo');
            }

            $piloto->update($request->validated());

            DB::commit();
            
            Log::info('Driver updated successfully', ['piloto_id' => $piloto->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $piloto,
                    'message' => 'Piloto actualizado exitosamente'
                ]);
            }

            return redirect()->route('pilotos.show', $piloto)
                ->with('success', 'Piloto actualizado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in PilotoController@update: ' . $e->getMessage());
            
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
     * Remove the specified driver from storage.
     *
     * @param Piloto $piloto
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Piloto $piloto, Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Check if driver has active trips
            if ($piloto->viaje_actual) {
                throw new Exception('No se puede eliminar un piloto con viajes activos');
            }

            $pilotoId = $piloto->id;
            $piloto->delete();

            DB::commit();
            
            Log::info('Driver deleted successfully', ['piloto_id' => $pilotoId, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Piloto eliminado exitosamente'
                ]);
            }

            return redirect()->route('pilotos.index')
                ->with('success', 'Piloto eliminado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in PilotoController@destroy: ' . $e->getMessage());
            
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
     * Check driver availability for trip assignment.
     *
     * @param Piloto $piloto
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarDisponibilidad(Piloto $piloto, Request $request)
    {
        try {
            $disponible = $piloto->esta_disponible;
            $viajeActual = $piloto->viaje_actual;
            
            $data = [
                'disponible' => $disponible,
                'estado' => $piloto->estado,
                'estado_display' => $piloto->estado_display,
                'viaje_actual' => $viajeActual ? [
                    'id' => $viajeActual->id,
                    'ruta' => $viajeActual->ruta->nombre_completo,
                    'camion' => $viajeActual->camion->placa,
                    'fecha_inicio' => $viajeActual->fecha_inicio->format('d/m/Y H:i'),
                    'estado' => $viajeActual->estado
                ] : null,
                'motivo_no_disponible' => !$disponible ? $this->getMotivoNoDisponible($piloto) : null
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'message' => $disponible ? 'Piloto disponible' : 'Piloto no disponible'
            ]);

        } catch (Exception $e) {
            Log::error('Error in PilotoController@verificarDisponibilidad: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking driver availability'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change driver status.
     *
     * @param Piloto $piloto
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function cambiarEstado(Piloto $piloto, Request $request)
    {
        // Validate request
        $request->validate([
            'nuevo_estado' => 'required|in:' . implode(',', Piloto::ESTADOS),
            'motivo' => 'nullable|string|max:500'
        ], [
            'nuevo_estado.required' => 'Debe especificar el nuevo estado',
            'nuevo_estado.in' => 'El estado seleccionado no es válido'
        ]);

        DB::beginTransaction();
        
        try {
            $estadoAnterior = $piloto->estado;

            // Validate business rules
            if ($piloto->viaje_actual && 
                in_array($request->nuevo_estado, [Piloto::ESTADO_INACTIVO, Piloto::ESTADO_SUSPENDIDO])) {
                throw new Exception('No se puede cambiar el estado del piloto con un viaje activo');
            }

            $piloto->estado = $request->nuevo_estado;
            $piloto->save();

            DB::commit();
            
            Log::info('Driver status changed', [
                'piloto_id' => $piloto->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $request->nuevo_estado,
                'motivo' => $request->motivo,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'piloto' => $piloto->fresh(),
                        'estado_anterior' => $estadoAnterior
                    ],
                    'message' => 'Estado del piloto actualizado exitosamente'
                ]);
            }

            return redirect()->back()->with('success', 'Estado del piloto actualizado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in PilotoController@cambiarEstado: ' . $e->getMessage());
            
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
     * Get driver performance analytics.
     *
     * @param Piloto $piloto
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rendimiento(Piloto $piloto, Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(3));
            $fechaFin = $request->get('fecha_fin', Carbon::now());

            $viajes = $piloto->viajes()
                ->where('estado', Viaje::ESTADO_COMPLETADO)
                ->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->with(['ruta', 'camion'])
                ->get();

            $rendimiento = [
                'periodo' => [
                    'inicio' => $fechaInicio,
                    'fin' => $fechaFin
                ],
                'total_viajes' => $viajes->count(),
                'total_kilometros' => $viajes->sum(function ($viaje) {
                    return $viaje->kilometros_recorridos ?? 0;
                }),
                'promedio_eficiencia' => $viajes->where('eficiencia', '>', 0)->avg('eficiencia'),
                'viajes_a_tiempo' => $viajes->where('eficiencia', '>=', 95)->count(),
                'viajes_retrasados' => $viajes->where('eficiencia', '<', 95)->count(),
                'rutas_mas_frecuentes' => $viajes->groupBy('ruta_id')
                    ->map(function ($viajesRuta) {
                        $ruta = $viajesRuta->first()->ruta;
                        return [
                            'ruta' => $ruta->nombre_completo,
                            'viajes' => $viajesRuta->count(),
                            'kilometros_totales' => $viajesRuta->sum(function ($viaje) {
                                return $viaje->kilometros_recorridos ?? 0;
                            })
                        ];
                    })
                    ->sortByDesc('viajes')
                    ->take(5)
                    ->values(),
                'eficiencia_mensual' => $viajes->groupBy(function ($viaje) {
                    return $viaje->fecha_inicio->format('Y-m');
                })->map(function ($viajesMes) {
                    return [
                        'viajes' => $viajesMes->count(),
                        'eficiencia_promedio' => $viajesMes->where('eficiencia', '>', 0)->avg('eficiencia')
                    ];
                })
            ];

            return response()->json([
                'status' => 'success',
                'data' => $rendimiento
            ]);

        } catch (Exception $e) {
            Log::error('Error in PilotoController@rendimiento: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving performance data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get driver statistics and analytics.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadisticas(Request $request)
    {
        try {
            $stats = [
                'total_pilotos' => Piloto::count(),
                'pilotos_activos' => Piloto::activos()->count(),
                'pilotos_disponibles' => Piloto::disponibles()->count(),
                'pilotos_inactivos' => Piloto::inactivos()->count(),
                'pilotos_suspendidos' => Piloto::suspendidos()->count(),
                'pilotos_en_viaje' => Piloto::whereHas('viajes', function ($q) {
                    $q->where('estado', 'En Curso');
                })->count(),
                'distribucion_experiencia' => [
                    'nuevo' => Piloto::withCount(['viajes as viajes_count' => function ($q) {
                        $q->where('estado', 'Completado');
                    }])->having('viajes_count', '<', 5)->count(),
                    'principiante' => Piloto::withCount(['viajes as viajes_count' => function ($q) {
                        $q->where('estado', 'Completado');
                    }])->having('viajes_count', '>=', 5)->having('viajes_count', '<', 20)->count(),
                    'intermedio' => Piloto::withCount(['viajes as viajes_count' => function ($q) {
                        $q->where('estado', 'Completado');
                    }])->having('viajes_count', '>=', 20)->having('viajes_count', '<', 50)->count(),
                    'avanzado' => Piloto::withCount(['viajes as viajes_count' => function ($q) {
                        $q->where('estado', 'Completado');
                    }])->having('viajes_count', '>=', 50)->having('viajes_count', '<', 100)->count(),
                    'experto' => Piloto::withCount(['viajes as viajes_count' => function ($q) {
                        $q->where('estado', 'Completado');
                    }])->having('viajes_count', '>=', 100)->count(),
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Error in PilotoController@estadisticas: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving statistics'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available drivers for trip assignment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disponibles(Request $request)
    {
        try {
            $fecha = $request->get('fecha', Carbon::now()->format('Y-m-d'));
            
            $pilotosDisponibles = Piloto::disponibles()
                ->whereDoesntHave('viajes', function ($query) use ($fecha) {
                    $query->where('estado', 'Programado')
                        ->whereDate('fecha_inicio', $fecha);
                })
                ->orderBy('nombre')
                ->get()
                ->map(function ($piloto) {
                    return [
                        'id' => $piloto->id,
                        'nombre_completo' => $piloto->nombre_completo,
                        'licencia' => $piloto->licencia,
                        'telefono_formateado' => $piloto->telefono_formateado,
                        'nivel_experiencia' => $piloto->nivel_experiencia,
                        'total_viajes_completados' => $piloto->total_viajes_completados,
                        'esta_disponible' => $piloto->esta_disponible
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $pilotosDisponibles,
                'total' => $pilotosDisponibles->count(),
                'fecha_consulta' => $fecha
            ]);

        } catch (Exception $e) {
            Log::error('Error in PilotoController@disponibles: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving available drivers'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get reason why driver is not available.
     *
     * @param Piloto $piloto
     * @return string
     */
    private function getMotivoNoDisponible(Piloto $piloto): string
    {
        if ($piloto->estado !== Piloto::ESTADO_ACTIVO) {
            return 'Estado: ' . $piloto->estado;
        }

        if ($piloto->viaje_actual) {
            return 'En viaje activo desde ' . $piloto->viaje_actual->fecha_inicio->format('d/m/Y H:i');
        }

        return 'Motivo no determinado';
    }
}