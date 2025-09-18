<?php

namespace App\Http\Controllers;

use App\Http\Requests\CamionRequest;
use App\Models\Camion;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

/**
 * Class CamionController
 *
 * Handles all truck-related operations including CRUD operations,
 * maintenance scheduling, mileage tracking, and status management.
 *
 * @package App\Http\Controllers
 */
class CamionController extends Controller
{
    /**
     * CamionController constructor.
     * Apply middleware for authentication and authorization.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
    }

    /**
     * Display a paginated listing of trucks with filtering and search capabilities.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Camion::with(['viajes', 'mantenimientos']);

            // Apply filters
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('marca')) {
                $query->where('marca', 'like', '%' . $request->marca . '%');
            }

            if ($request->filled('year_min')) {
                $query->where('year', '>=', $request->year_min);
            }

            if ($request->filled('year_max')) {
                $query->where('year', '<=', $request->year_max);
            }

            if ($request->filled('necesita_mantenimiento')) {
                if ($request->necesita_mantenimiento === '1') {
                    $query->necesitanMantenimiento();
                }
            }

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('placa', 'like', "%{$searchTerm}%")
                        ->orWhere('marca', 'like', "%{$searchTerm}%")
                        ->orWhere('modelo', 'like', "%{$searchTerm}%")
                        ->orWhere('numero_motor', 'like', "%{$searchTerm}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'placa');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $camiones = $query->paginate($perPage)->appends($request->query());

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $camiones,
                    'message' => 'Camiones retrieved successfully'
                ]);
            }

            return view('camiones.index', compact('camiones'));

        } catch (Exception $e) {
            Log::error('Error in CamionController@index: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error retrieving trucks'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'Error al cargar los camiones');
        }
    }

    /**
     * Show the form for creating a new truck.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $estados = Camion::ESTADOS;
            $currentYear = Carbon::now()->year;
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'estados' => $estados,
                        'current_year' => $currentYear
                    ]
                ]);
            }

            return view('camiones.create', compact('estados', 'currentYear'));

        } catch (Exception $e) {
            Log::error('Error in CamionController@create: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error loading create form data'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->route('camiones.index')->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Store a newly created truck in storage.
     *
     * @param CamionRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(CamionRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            $data['estado'] = $data['estado'] ?? Camion::ESTADO_ACTIVO;

            $camion = Camion::create($data);

            DB::commit();
            
            Log::info('Truck created successfully', ['camion_id' => $camion->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $camion,
                    'message' => 'Camión creado exitosamente'
                ], Response::HTTP_CREATED);
            }

            return redirect()->route('camiones.show', $camion)
                ->with('success', 'Camión creado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in CamionController@store: ' . $e->getMessage());
            
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
     * Display the specified truck.
     *
     * @param Camion $camion
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Camion $camion, Request $request)
    {
        try {
            $camion->load([
                'viajes' => function ($query) {
                    $query->orderBy('fecha_inicio', 'desc')->limit(10);
                },
                'mantenimientos' => function ($query) {
                    $query->orderBy('fecha_programada', 'desc')->limit(10);
                }
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $camion
                ]);
            }

            return view('camiones.show', compact('camion'));

        } catch (Exception $e) {
            Log::error('Error in CamionController@show: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Truck not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return redirect()->route('camiones.index')->with('error', 'Camión no encontrado');
        }
    }

    /**
     * Show the form for editing the specified truck.
     *
     * @param Camion $camion
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(Camion $camion, Request $request)
    {
        try {
            $estados = Camion::ESTADOS;
            $currentYear = Carbon::now()->year;

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'camion' => $camion,
                        'estados' => $estados,
                        'current_year' => $currentYear
                    ]
                ]);
            }

            return view('camiones.edit', compact('camion', 'estados', 'currentYear'));

        } catch (Exception $e) {
            Log::error('Error in CamionController@edit: ' . $e->getMessage());
            
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
     * Update the specified truck in storage.
     *
     * @param CamionRequest $request
     * @param Camion $camion
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(CamionRequest $request, Camion $camion)
    {
        DB::beginTransaction();
        
        try {
            // Check if truck has active trips before changing certain fields
            if ($camion->viaje_actual && 
                in_array($request->estado, [Camion::ESTADO_INACTIVO, Camion::ESTADO_EN_TALLER])) {
                throw new Exception('No se puede cambiar el estado del camión con un viaje activo');
            }

            $camion->update($request->validated());

            DB::commit();
            
            Log::info('Truck updated successfully', ['camion_id' => $camion->id, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $camion,
                    'message' => 'Camión actualizado exitosamente'
                ]);
            }

            return redirect()->route('camiones.show', $camion)
                ->with('success', 'Camión actualizado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in CamionController@update: ' . $e->getMessage());
            
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
     * Remove the specified truck from storage.
     *
     * @param Camion $camion
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Camion $camion, Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Check if truck has active trips or pending maintenance
            if ($camion->viaje_actual) {
                throw new Exception('No se puede eliminar un camión con viajes activos');
            }

            $pendingMaintenance = $camion->mantenimientos()
                ->whereIn('estado', [Mantenimiento::ESTADO_PROGRAMADO, Mantenimiento::ESTADO_EN_PROCESO])
                ->exists();

            if ($pendingMaintenance) {
                throw new Exception('No se puede eliminar un camión con mantenimientos pendientes');
            }

            $camionId = $camion->id;
            $camion->delete();

            DB::commit();
            
            Log::info('Truck deleted successfully', ['camion_id' => $camionId, 'user_id' => Auth::id()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Camión eliminado exitosamente'
                ]);
            }

            return redirect()->route('camiones.index')
                ->with('success', 'Camión eliminado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in CamionController@destroy: ' . $e->getMessage());
            
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
     * Check which trucks need maintenance.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function verificarMantenimiento(Request $request)
    {
        try {
            $camionesMantenimiento = Camion::necesitanMantenimiento()
                ->with(['mantenimientos' => function ($query) {
                    $query->where('estado', 'Completado')
                        ->orderBy('fecha_realizada', 'desc')
                        ->limit(1);
                }])
                ->get();

            $data = $camionesMantenimiento->map(function ($camion) {
                return [
                    'id' => $camion->id,
                    'placa' => $camion->placa,
                    'marca' => $camion->marca,
                    'modelo' => $camion->modelo,
                    'kilometraje_actual' => $camion->kilometraje_actual,
                    'kilometros_hasta_mantenimiento' => $camion->kilometros_hasta_mantenimiento,
                    'ultimo_mantenimiento' => $camion->ultimo_mantenimiento,
                    'necesita_mantenimiento' => $camion->necesita_mantenimiento,
                    'prioridad' => $camion->kilometros_hasta_mantenimiento < 0 ? 'Urgente' : 'Normal'
                ];
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'total' => $data->count(),
                    'message' => 'Maintenance check completed'
                ]);
            }

            return view('camiones.mantenimiento', ['camiones' => $data]);

        } catch (Exception $e) {
            Log::error('Error in CamionController@verificarMantenimiento: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error checking maintenance requirements'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('error', 'Error al verificar mantenimientos');
        }
    }

    /**
     * Update truck mileage.
     *
     * @param Camion $camion
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function actualizarKilometraje(Camion $camion, Request $request)
    {
        // Validate request
        $request->validate([
            'nuevo_kilometraje' => 'required|numeric|min:' . $camion->kilometraje_actual,
            'observaciones' => 'nullable|string|max:500'
        ], [
            'nuevo_kilometraje.required' => 'El nuevo kilometraje es obligatorio',
            'nuevo_kilometraje.numeric' => 'El kilometraje debe ser un número',
            'nuevo_kilometraje.min' => 'El nuevo kilometraje no puede ser menor al actual'
        ]);

        DB::beginTransaction();
        
        try {
            $kilometrajeAnterior = $camion->kilometraje_actual;
            $success = $camion->actualizarKilometraje($request->nuevo_kilometraje);

            if (!$success) {
                throw new Exception('No se pudo actualizar el kilometraje');
            }

            DB::commit();
            
            Log::info('Truck mileage updated', [
                'camion_id' => $camion->id,
                'kilometraje_anterior' => $kilometrajeAnterior,
                'kilometraje_nuevo' => $request->nuevo_kilometraje,
                'diferencia' => $request->nuevo_kilometraje - $kilometrajeAnterior,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'camion' => $camion->fresh(),
                        'kilometraje_anterior' => $kilometrajeAnterior,
                        'diferencia' => $request->nuevo_kilometraje - $kilometrajeAnterior
                    ],
                    'message' => 'Kilometraje actualizado exitosamente'
                ]);
            }

            return redirect()->back()->with('success', 'Kilometraje actualizado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in CamionController@actualizarKilometraje: ' . $e->getMessage());
            
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
     * Change truck status.
     *
     * @param Camion $camion
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function cambiarEstado(Camion $camion, Request $request)
    {
        // Validate request
        $request->validate([
            'nuevo_estado' => 'required|in:' . implode(',', Camion::ESTADOS),
            'motivo' => 'nullable|string|max:500'
        ], [
            'nuevo_estado.required' => 'Debe especificar el nuevo estado',
            'nuevo_estado.in' => 'El estado seleccionado no es válido'
        ]);

        DB::beginTransaction();
        
        try {
            $estadoAnterior = $camion->estado;

            // Validate business rules
            if ($camion->viaje_actual && 
                in_array($request->nuevo_estado, [Camion::ESTADO_INACTIVO, Camion::ESTADO_EN_TALLER])) {
                throw new Exception('No se puede cambiar el estado del camión con un viaje activo');
            }

            $camion->estado = $request->nuevo_estado;
            $camion->save();

            DB::commit();
            
            Log::info('Truck status changed', [
                'camion_id' => $camion->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $request->nuevo_estado,
                'motivo' => $request->motivo,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'camion' => $camion->fresh(),
                        'estado_anterior' => $estadoAnterior
                    ],
                    'message' => 'Estado del camión actualizado exitosamente'
                ]);
            }

            return redirect()->back()->with('success', 'Estado del camión actualizado exitosamente');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in CamionController@cambiarEstado: ' . $e->getMessage());
            
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
     * Get truck statistics and analytics.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadisticas(Request $request)
    {
        try {
            $stats = [
                'total_camiones' => Camion::count(),
                'camiones_activos' => Camion::activos()->count(),
                'camiones_en_taller' => Camion::enTaller()->count(),
                'camiones_inactivos' => Camion::inactivos()->count(),
                'camiones_necesitan_mantenimiento' => Camion::necesitanMantenimiento()->count(),
                'kilometraje_total_flota' => Camion::sum('kilometraje_actual'),
                'promedio_kilometraje' => Camion::avg('kilometraje_actual'),
                'camiones_por_marca' => Camion::select('marca', DB::raw('count(*) as total'))
                    ->groupBy('marca')
                    ->orderBy('total', 'desc')
                    ->get(),
                'antiguedad_promedio' => Carbon::now()->year - Camion::avg('year')
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Error in CamionController@estadisticas: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving statistics'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available trucks for trip assignment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disponibles(Request $request)
    {
        try {
            $camionesDisponibles = Camion::activos()
                ->whereDoesntHave('viajes', function ($query) {
                    $query->where('estado', 'En Curso');
                })
                ->orderBy('placa')
                ->get()
                ->map(function ($camion) {
                    return [
                        'id' => $camion->id,
                        'placa' => $camion->placa,
                        'nombre_completo' => $camion->nombre_completo,
                        'kilometraje_actual' => $camion->kilometraje_actual,
                        'necesita_mantenimiento' => $camion->necesita_mantenimiento,
                        'kilometros_hasta_mantenimiento' => $camion->kilometros_hasta_mantenimiento
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $camionesDisponibles,
                'total' => $camionesDisponibles->count()
            ]);

        } catch (Exception $e) {
            Log::error('Error in CamionController@disponibles: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving available trucks'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}