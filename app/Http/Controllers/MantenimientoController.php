<?php

namespace App\Http\Controllers;

use App\Http\Requests\MantenimientoRequest;
use App\Models\Mantenimiento;
use App\Models\Camion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

/**
 * Class MantenimientoController
 *
 * Handles all maintenance-related operations including CRUD operations,
 * maintenance scheduling, completion tracking, and overdue maintenance monitoring.
 *
 * @package App\Http\Controllers
 */
class MantenimientoController extends Controller
{
    /**
     * MantenimientoController constructor.
     * Apply middleware for authentication and authorization.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }

    /**
     * Display a paginated listing of maintenance records with filtering and search capabilities.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Mantenimiento::with(['camion']);

            // Apply filters
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('camion_id')) {
                $query->where('camion_id', $request->camion_id);
            }

            if ($request->filled('tipo_mantenimiento')) {
                $query->porTipo($request->tipo_mantenimiento);
            }

            if ($request->filled('fecha_inicio')) {
                $query->whereDate('fecha_programada', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $query->whereDate('fecha_programada', '<=', $request->fecha_fin);
            }

            if ($request->filled('vencidos') && $request->vencidos === '1') {
                $query->vencidos();
            }

            if ($request->filled('proximos') && $request->proximos === '1') {
                $dias = $request->get('dias_proximos', 7);
                $query->proximos($dias);
            }

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('tipo_mantenimiento', 'like', "%{$searchTerm}%")
                        ->orWhere('descripcion', 'like', "%{$searchTerm}%")
                        ->orWhereHas('camion', function ($camionQuery) use ($searchTerm) {
                            $camionQuery->where('placa', 'like', "%{$searchTerm}%")
                                ->orWhere('marca', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'fecha_programada');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $mantenimientos = $query->paginate($perPage)->appends($request->query());

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $mantenimientos,
                    'message' => 'Mantenimientos retrieved successfully'
                ]);
            }

            // Get filter data for view
            $camiones = Camion::orderBy('placa')->get();
            $tiposMantenimiento = Mantenimiento::TIPOS_MANTENIMIENTO;

            return view('mantenimientos.index', compact('mantenimientos', 'camiones', 'tiposMantenimiento'));

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@index: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error retrieving maintenance records'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'Error al cargar los mantenimientos');
        }
    }

    /**
     * Show the form for creating a new maintenance record.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $camiones = Camion::orderBy('placa')->get();
            $tiposMantenimiento = Mantenimiento::TIPOS_MANTENIMIENTO;
            $estados = Mantenimiento::ESTADOS;
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'camiones' => $camiones,
                        'tipos_mantenimiento' => $tiposMantenimiento,
                        'estados' => $estados
                    ]
                ]);
            }

            return view('mantenimientos.create', compact('camiones', 'tiposMantenimiento', 'estados'));

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@create: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error loading create form data'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->route('mantenimientos.index')->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Store a newly created maintenance record in storage.
     *
     * @param MantenimientoRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(MantenimientoRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            $data['estado'] = $data['estado'] ?? Mantenimiento::ESTADO_PROGRAMADO;

            $mantenimiento = Mantenimiento::create($data);

            // If maintenance is scheduled or in progress, update truck status
            if (in_array($mantenimiento->estado, [Mantenimiento::ESTADO_PROGRAMADO, Mantenimiento::ESTADO_EN_PROCESO])) {
                $camion = $mantenimiento->camion;
                if ($camion->estado === Camion::ESTADO_ACTIVO) {
                    $camion->update(['estado' => Camion::ESTADO_EN_TALLER]);
                }
            }

            DB::commit();
            
            Log::info('Maintenance record created successfully', ['mantenimiento_id' => $mantenimiento->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $mantenimiento->load('camion'),
                    'message' => 'Mantenimiento creado exitosamente'
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento creado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in MantenimientoController@store: ' . $e->getMessage());
            
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
     * Display the specified maintenance record.
     *
     * @param Mantenimiento $mantenimiento
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Mantenimiento $mantenimiento, Request $request)
    {
        try {
            $mantenimiento->load(['camion']);

            // Calculate additional statistics
            $stats = [
                'esta_vencido' => $mantenimiento->esta_vencido,
                'esta_proximo' => $mantenimiento->esta_proximo,
                'dias_hasta_vencimiento' => $mantenimiento->dias_hasta_vencimiento,
                'prioridad' => $mantenimiento->prioridad,
                'estado_display' => $mantenimiento->estado_display,
                'costo_formateado' => $mantenimiento->costo_formateado,
                'dias_desde_completado' => $mantenimiento->dias_desde_completado,
                'es_preventivo' => $mantenimiento->es_preventivo,
                'es_correctivo' => $mantenimiento->es_correctivo,
                'duracion_dias' => $mantenimiento->duracion_dias
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => array_merge($mantenimiento->toArray(), ['stats' => $stats])
                ]);
            }

            return view('mantenimientos.show', compact('mantenimiento', 'stats'));

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@show: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maintenance record not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return redirect()->route('mantenimientos.index')->with('error', 'Mantenimiento no encontrado');
        }
    }

    /**
     * Show the form for editing the specified maintenance record.
     *
     * @param Mantenimiento $mantenimiento
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(Mantenimiento $mantenimiento, Request $request)
    {
        try {
            // Only allow editing if not completed
            if ($mantenimiento->estado === Mantenimiento::ESTADO_COMPLETADO) {
                throw new Exception('No se pueden editar mantenimientos completados');
            }

            $camiones = Camion::orderBy('placa')->get();
            $tiposMantenimiento = Mantenimiento::TIPOS_MANTENIMIENTO;
            $estados = Mantenimiento::ESTADOS;

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'mantenimiento' => $mantenimiento->load('camion'),
                        'camiones' => $camiones,
                        'tipos_mantenimiento' => $tiposMantenimiento,
                        'estados' => $estados
                    ]
                ]);
            }

            return view('mantenimientos.edit', compact('mantenimiento', 'camiones', 'tiposMantenimiento', 'estados'));

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@edit: ' . $e->getMessage());
            
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
     * Update the specified maintenance record in storage.
     *
     * @param MantenimientoRequest $request
     * @param Mantenimiento $mantenimiento
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(MantenimientoRequest $request, Mantenimiento $mantenimiento)
    {
        DB::beginTransaction();
        
        try {
            // Only allow updating if not completed
            if ($mantenimiento->estado === Mantenimiento::ESTADO_COMPLETADO) {
                throw new Exception('No se pueden editar mantenimientos completados');
            }

            $mantenimiento->update($request->validated());

            DB::commit();
            
            Log::info('Maintenance record updated successfully', ['mantenimiento_id' => $mantenimiento->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $mantenimiento->load('camion'),
                    'message' => 'Mantenimiento actualizado exitosamente'
                ]);
            }

            return redirect()->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento actualizado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in MantenimientoController@update: ' . $e->getMessage());
            
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
     * Remove the specified maintenance record from storage.
     *
     * @param Mantenimiento $mantenimiento
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Mantenimiento $mantenimiento, Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Only allow deletion if not completed
            if ($mantenimiento->estado === Mantenimiento::ESTADO_COMPLETADO) {
                throw new Exception('No se pueden eliminar mantenimientos completados');
            }

            $mantenimientoId = $mantenimiento->id;
            $camionId = $mantenimiento->camion_id;
            
            $mantenimiento->delete();

            // Check if truck should be set back to active status
            $otrosMantenimientosPendientes = Mantenimiento::where('camion_id', $camionId)
                ->whereIn('estado', [Mantenimiento::ESTADO_PROGRAMADO, Mantenimiento::ESTADO_EN_PROCESO])
                ->exists();

            if (!$otrosMantenimientosPendientes) {
                $camion = Camion::find($camionId);
                if ($camion && $camion->estado === Camion::ESTADO_EN_TALLER) {
                    $camion->update(['estado' => Camion::ESTADO_ACTIVO]);
                }
            }

            DB::commit();
            
            Log::info('Maintenance record deleted successfully', ['mantenimiento_id' => $mantenimientoId, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Mantenimiento eliminado exitosamente'
                ]);
            }

            return redirect()->route('mantenimientos.index')
                ->with('success', 'Mantenimiento eliminado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in MantenimientoController@destroy: ' . $e->getMessage());
            
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
     * Schedule maintenance for a truck.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function programarMantenimiento(Request $request)
    {
        // Validate request
        $request->validate([
            'camion_id' => 'required|exists:camiones,id',
            'tipo_mantenimiento' => 'required|string|max:255',
            'fecha_programada' => 'required|date|after_or_equal:today',
            'descripcion' => 'nullable|string|max:1000',
            'costo_estimado' => 'nullable|numeric|min:0'
        ], [
            'camion_id.required' => 'Debe seleccionar un camión',
            'camion_id.exists' => 'El camión seleccionado no existe',
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'fecha_programada.required' => 'La fecha programada es obligatoria',
            'fecha_programada.after_or_equal' => 'La fecha no puede ser anterior a hoy'
        ]);

        DB::beginTransaction();
        
        try {
            $camion = Camion::findOrFail($request->camion_id);

            // Create maintenance record
            $mantenimiento = Mantenimiento::create([
                'camion_id' => $request->camion_id,
                'tipo_mantenimiento' => $request->tipo_mantenimiento,
                'descripcion' => $request->descripcion,
                'fecha_programada' => $request->fecha_programada,
                'costo' => $request->costo_estimado,
                'estado' => Mantenimiento::ESTADO_PROGRAMADO
            ]);

            // Update truck status if needed
            if ($camion->estado === Camion::ESTADO_ACTIVO) {
                $camion->update(['estado' => Camion::ESTADO_EN_TALLER]);
            }

            DB::commit();
            
            Log::info('Maintenance scheduled successfully', [
                'mantenimiento_id' => $mantenimiento->id,
                'camion_id' => $camion->id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $mantenimiento->load('camion'),
                    'message' => 'Mantenimiento programado exitosamente'
                ]);
            }

            return redirect()->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento programado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in MantenimientoController@programarMantenimiento: ' . $e->getMessage());
            
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
     * Complete a maintenance record.
     *
     * @param Mantenimiento $mantenimiento
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function completarMantenimiento(Mantenimiento $mantenimiento, Request $request)
    {
        // Validate request
        $request->validate([
            'costo_real' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000'
        ], [
            'costo_real.numeric' => 'El costo debe ser un número',
            'costo_real.min' => 'El costo no puede ser negativo'
        ]);

        DB::beginTransaction();
        
        try {
            if (!in_array($mantenimiento->estado, [Mantenimiento::ESTADO_PROGRAMADO, Mantenimiento::ESTADO_EN_PROCESO])) {
                throw new Exception('Solo se pueden completar mantenimientos programados o en proceso');
            }

            $success = $mantenimiento->completar(
                $request->costo_real,
                $request->observaciones
            );

            if (!$success) {
                throw new Exception('No se pudo completar el mantenimiento');
            }

            DB::commit();
            
            Log::info('Maintenance completed successfully', [
                'mantenimiento_id' => $mantenimiento->id,
                'costo_real' => $request->costo_real,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $mantenimiento->load('camion'),
                    'message' => 'Mantenimiento completado exitosamente'
                ]);
            }

            return redirect()->back()->with('success', 'Mantenimiento completado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in MantenimientoController@completarMantenimiento: ' . $e->getMessage());
            
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
     * Check overdue maintenance records.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function verificarVencidos(Request $request)
    {
        try {
            $mantenimientosVencidos = Mantenimiento::vencidos()
                ->with(['camion'])
                ->orderBy('fecha_programada')
                ->get();

            $data = $mantenimientosVencidos->map(function ($mantenimiento) {
                return [
                    'id' => $mantenimiento->id,
                    'camion' => [
                        'id' => $mantenimiento->camion->id,
                        'placa' => $mantenimiento->camion->placa,
                        'marca' => $mantenimiento->camion->marca,
                        'modelo' => $mantenimiento->camion->modelo,
                    ],
                    'tipo_mantenimiento' => $mantenimiento->tipo_mantenimiento,
                    'fecha_programada' => $mantenimiento->fecha_programada->format('d/m/Y'),
                    'dias_vencido' => abs($mantenimiento->dias_hasta_vencimiento),
                    'prioridad' => $mantenimiento->prioridad,
                    'costo_formateado' => $mantenimiento->costo_formateado,
                    'resumen' => $mantenimiento->resumen
                ];
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'total_vencidos' => $data->count(),
                    'message' => 'Overdue maintenance check completed'
                ]);
            }

            return view('mantenimientos.vencidos', ['mantenimientos' => $data]);

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@verificarVencidos: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error checking overdue maintenance'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'Error al verificar mantenimientos vencidos');
        }
    }

    /**
     * Get maintenance statistics and analytics.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadisticas(Request $request)
    {
        try {
            $stats = [
                'total_mantenimientos' => Mantenimiento::count(),
                'mantenimientos_programados' => Mantenimiento::programados()->count(),
                'mantenimientos_en_proceso' => Mantenimiento::enProceso()->count(),
                'mantenimientos_completados' => Mantenimiento::completados()->count(),
                'mantenimientos_cancelados' => Mantenimiento::cancelados()->count(),
                'mantenimientos_vencidos' => Mantenimiento::vencidos()->count(),
                'mantenimientos_proximos' => Mantenimiento::proximos(7)->count(),
                'costo_total_mantenimientos' => Mantenimiento::completados()
                    ->whereNotNull('costo')
                    ->sum('costo'),
                'costo_promedio_mantenimiento' => Mantenimiento::completados()
                    ->whereNotNull('costo')
                    ->avg('costo'),
                'tipos_mantenimiento_mas_frecuentes' => Mantenimiento::select('tipo_mantenimiento', DB::raw('count(*) as total'))
                    ->groupBy('tipo_mantenimiento')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get(),
                'mantenimientos_por_mes' => Mantenimiento::completados()
                    ->whereYear('fecha_realizada', Carbon::now()->year)
                    ->selectRaw('MONTH(fecha_realizada) as mes, COUNT(*) as total, AVG(costo) as costo_promedio')
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get(),
                'camiones_con_mas_mantenimientos' => Mantenimiento::select('camion_id', DB::raw('count(*) as total_mantenimientos'))
                    ->with(['camion:id,placa,marca,modelo'])
                    ->groupBy('camion_id')
                    ->orderBy('total_mantenimientos', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'camion' => $item->camion->placa . ' - ' . $item->camion->marca . ' ' . $item->camion->modelo,
                            'total_mantenimientos' => $item->total_mantenimientos
                        ];
                    })
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@estadisticas: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving statistics'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get maintenance calendar view.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calendario(Request $request)
    {
        try {
            $year = $request->get('year', Carbon::now()->year);
            $month = $request->get('month', Carbon::now()->month);

            $mantenimientos = Mantenimiento::with(['camion'])
                ->whereYear('fecha_programada', $year)
                ->whereMonth('fecha_programada', $month)
                ->orderBy('fecha_programada')
                ->get();

            $eventos = $mantenimientos->map(function ($mantenimiento) {
                $color = match($mantenimiento->estado) {
                    Mantenimiento::ESTADO_PROGRAMADO => $mantenimiento->esta_vencido ? '#dc3545' : '#007bff', // Red if overdue, blue if on time
                    Mantenimiento::ESTADO_EN_PROCESO => '#ffc107', // Yellow
                    Mantenimiento::ESTADO_COMPLETADO => '#28a745', // Green
                    Mantenimiento::ESTADO_CANCELADO => '#6c757d', // Gray
                    default => '#007bff'
                };

                return [
                    'id' => $mantenimiento->id,
                    'title' => $mantenimiento->camion->placa . ' - ' . $mantenimiento->tipo_mantenimiento,
                    'start' => $mantenimiento->fecha_programada->format('Y-m-d'),
                    'color' => $color,
                    'description' => $mantenimiento->descripcion,
                    'estado' => $mantenimiento->estado,
                    'prioridad' => $mantenimiento->prioridad,
                    'url' => route('mantenimientos.show', $mantenimiento->id)
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'eventos' => $eventos,
                    'year' => $year,
                    'month' => $month,
                    'total_eventos' => $eventos->count()
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error in MantenimientoController@calendario: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving calendar data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}