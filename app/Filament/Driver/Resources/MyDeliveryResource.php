<?php

namespace App\Filament\Driver\Resources;

use App\Filament\Driver\Resources\MyDeliveryResource\Pages;
use App\Models\Delivery;
use App\Models\DeliveryAssignment;
use App\Models\Driver;
use App\Models\TblKilometer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class MyDeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Mis Entregas';
    
    protected static ?string $modelLabel = 'Entrega';
    
    protected static ?string $pluralModelLabel = 'Mis Entregas';
    
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $currentDriver = static::getCurrentDriver();
        
        if (!$currentDriver) {
            // Si no se puede identificar el conductor, devolver consulta vacía
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Solo mostrar entregas asignadas a este conductor
        return parent::getEloquentQuery()
            ->whereHas('deliveryAssignments', function ($query) use ($currentDriver) {
                $query->where('driver_id', $currentDriver->id_driver);
            })
            ->with(['route.origin', 'route.destination', 'deliveryStatus'])
            ->orderBy('delivery_date', 'desc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_delivery')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('route.origin.name_municipality')
                    ->label('Origen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('route.destination.name_municipality')
                    ->label('Destino')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_status')
                    ->label('Mi Estado')
                    ->getStateUsing(function ($record) {
                        $currentDriver = static::getCurrentDriver();
                        if (!$currentDriver) return 'N/A';
                        
                        $assignment = DeliveryAssignment::where('delivery_id', $record->id_delivery)
                            ->where('driver_id', $currentDriver->id_driver)
                            ->first();
                            
                        return $assignment ? ucfirst(str_replace('_', ' ', $assignment->driver_status)) : 'Sin asignar';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'pendiente' => 'gray',
                        'agarrado' => 'warning',
                        'en ruta' => 'info',
                        'completado' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_products')
                    ->label('Total')
                    ->getStateUsing(function ($record) {
                        return \DB::table('delivery_products')
                            ->where('delivery_id', $record->id_delivery)
                            ->sum('subtotal');
                    })
                    ->money('GTQ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Mi Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'agarrado' => 'Agarrado',
                        'en_ruta' => 'En Ruta',
                        'completado' => 'Completado',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || !$data['value']) {
                            return $query;
                        }
                        
                        $currentDriver = static::getCurrentDriver();
                        if (!$currentDriver) return $query;
                        
                        return $query->whereHas('deliveryAssignments', function ($q) use ($currentDriver, $data) {
                            $q->where('driver_id', $currentDriver->id_driver)
                              ->where('driver_status', $data['value']);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('accept')
                    ->label('Aceptar')
                    ->icon('heroicon-o-hand-raised')
                    ->color('warning')
                    ->visible(fn ($record): bool => static::getDriverStatus($record) === 'pendiente')
                    ->requiresConfirmation()
                    ->modalHeading('Aceptar Entrega')
                    ->modalDescription('¿Confirmas que aceptas esta entrega?')
                    ->action(function ($record) {
                        static::updateDriverStatus($record, 'agarrado');
                        Notification::make()
                            ->title('Entrega Aceptada')
                            ->body('Has aceptado la entrega exitosamente.')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('start_route')
                    ->label('Iniciar Ruta')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->visible(fn ($record): bool => static::getDriverStatus($record) === 'agarrado')
                    ->requiresConfirmation()
                    ->modalHeading('Iniciar Ruta')
                    ->modalDescription('¿Confirmas que inicias la ruta para esta entrega?')
                    ->action(function ($record) {
                        static::updateDriverStatus($record, 'en_ruta');
                        Notification::make()
                            ->title('Ruta Iniciada')
                            ->body('Has iniciado la ruta. ¡Buen viaje!')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record): bool => static::getDriverStatus($record) === 'en_ruta')
                    ->requiresConfirmation()
                    ->modalHeading('Completar Entrega')
                    ->modalDescription('¿Confirmas que has completado esta entrega? Esto generará automáticamente el ingreso correspondiente y registrará el kilometraje recorrido.')
                    ->action(function ($record) {
                        static::updateDriverStatus($record, 'completado');
                        
                        // También actualizar el estado de la entrega a "Entregado"
                        $entregadoStatus = \App\Models\DeliveryStatus::where('name_status', 'like', '%entregado%')
                                                                      ->orWhere('name_status', 'like', '%completado%')
                                                                      ->first();
                        if ($entregadoStatus) {
                            $record->update(['status_id' => $entregadoStatus->id_status]);
                        }
                        
                        // 🚗 REGISTRAR KILOMETRAJE AUTOMÁTICAMENTE
                        static::recordKilometers($record);
                        
                        Notification::make()
                            ->title('Entrega Completada')
                            ->body('¡Excelente! Has completado la entrega. Se ha generado el ingreso automáticamente y registrado el kilometraje.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                // Sin acciones masivas para conductores
            ])
            ->poll('10s') // Actualizar cada 10 segundos
            ->defaultSort('delivery_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyDeliveries::route('/'),
            'view' => Pages\ViewMyDelivery::route('/{record}'),
        ];
    }
    
    /**
     * Obtener el conductor actual
     */
    protected static function getCurrentDriver()
    {
        try {
            $user = Auth::guard('driver')->user();
            if (!$user) {
                return null;
            }

            // Buscar el conductor basado en el nombre del usuario
            $driver = Driver::where('name', 'LIKE', '%' . $user->first_name . '%')
                           ->orWhere('name', 'LIKE', '%' . $user->last_name . '%')
                           ->first();
            
            // Si no se encuentra un conductor, crear uno temporal o usar el ID del usuario
            if (!$driver) {
                // Para propósitos de desarrollo, vamos a usar el primer conductor disponible
                // o crear una lógica que relacione user_id con driver_id
                $driver = Driver::first();
                
                if (!$driver) {
                    // Si no hay conductores, crear uno temporal basado en el usuario
                    $driver = new Driver();
                    $driver->id_driver = $user->id; // Usar el ID del usuario como driver_id temporal
                    $driver->name = $user->first_name . ' ' . $user->last_name;
                }
            }
            
            return $driver;
        } catch (\Exception $e) {
            \Log::error('Error getting current driver: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener el estado del conductor para una entrega específica
     */
    protected static function getDriverStatus($record): string
    {
        $currentDriver = static::getCurrentDriver();
        if (!$currentDriver) return 'sin_asignar';
        
        $assignment = DeliveryAssignment::where('delivery_id', $record->id_delivery)
            ->where('driver_id', $currentDriver->id_driver)
            ->first();
            
        return $assignment ? $assignment->driver_status : 'sin_asignar';
    }
    
    /**
     * Actualizar el estado del conductor para una entrega
     */
    protected static function updateDriverStatus($record, string $newStatus): void
    {
        $currentDriver = static::getCurrentDriver();
        if (!$currentDriver) return;
        
        DeliveryAssignment::where('delivery_id', $record->id_delivery)
            ->where('driver_id', $currentDriver->id_driver)
            ->update(['driver_status' => $newStatus]);
    }
    
    /**
     * 🚗 REGISTRAR KILOMETRAJE AUTOMÁTICAMENTE
     */
    protected static function recordKilometers($record): void
    {
        try {
            $currentDriver = static::getCurrentDriver();
            if (!$currentDriver) {
                \Log::warning('No se pudo obtener el conductor actual para registrar kilometraje');
                return;
            }
            
            // Obtener la asignación
            $assignment = DeliveryAssignment::where('delivery_id', $record->id_delivery)
                ->where('driver_id', $currentDriver->id_driver)
                ->with(['vehicle', 'delivery.route'])
                ->first();
                
            if (!$assignment) {
                \Log::warning("No se encontró asignación para entrega {$record->id_delivery} y conductor {$currentDriver->id_driver}");
                return;
            }
            
            if (!$assignment->vehicle) {
                \Log::warning("No se encontró vehículo para la asignación de entrega {$record->id_delivery}");
                return;
            }
            
            $distance = 0;
            $notes = "Entrega #{$record->id_delivery} - {$record->customer_name}";
            
            // 🎯 PRIORIDAD 1: Usar distancia de ruta preestablecida
            if ($assignment->delivery && $assignment->delivery->route && $assignment->delivery->route->distance_km > 0) {
                $distance = $assignment->delivery->route->distance_km;
                $notes .= " (Ruta preestablecida: {$assignment->delivery->route->distance_km} km)";
                \Log::info("✅ Usando distancia de ruta preestablecida: {$distance} km para entrega {$record->id_delivery}");
            } 
            // 🎯 PRIORIDAD 2: Calcular con Google Maps como fallback
            else {
                \Log::info("📍 No hay ruta preestablecida, calculando con Google Maps...");
                
                $googleMapsService = app(\App\Services\GoogleMapsService::class);
                
                // Usar dirección de empresa como origen
                $origin = "Guatemala City, Guatemala"; // Configurar según tu empresa
                $destination = $record->delivery_address;
                
                \Log::info("Calculando distancia desde '{$origin}' hasta '{$destination}'");
                
                $distanceResult = $googleMapsService->calculateDistance($origin, $destination);
                
                // Extraer distancia del resultado
                if (is_array($distanceResult)) {
                    $distance = $distanceResult['distance'] ?? 0;
                    \Log::info("Resultado de Google Maps:", $distanceResult);
                } else {
                    $distance = $distanceResult;
                }
                
                $notes .= " (Calculado con Google Maps: {$distance} km)";
            }
            
            // Convertir a float y validar
            $distance = (float) $distance;
            
            if ($distance > 0 && $distance <= 1000) { // Validar que sea una distancia razonable
                // Registrar kilómetros en el vehículo
                $kilometerLog = $assignment->vehicle->addKilometers(
                    $distance,
                    $notes,
                    $currentDriver->id_driver
                );
                
                \Log::info("✅ Kilometraje registrado exitosamente: {$distance} km para vehículo {$assignment->vehicle->license_plate}");
                \Log::info("🚛 Total acumulado del vehículo: {$assignment->vehicle->fresh()->total_kilometers} km");
                
                // Verificar si necesita mantenimiento
                $vehicle = $assignment->vehicle->fresh();
                if ($vehicle->needsMaintenance()) {
                    \Log::warning("🔧 ALERTA: Vehículo {$vehicle->license_plate} necesita mantenimiento ({$vehicle->total_kilometers} km)");
                }
                
            } else {
                \Log::warning("Distancia inválida calculada: {$distance} km. No se registrará kilometraje.");
            }
            
        } catch (\Exception $e) {
            \Log::error('❌ Error registrando kilometraje: ' . $e->getMessage(), [
                'delivery_id' => $record->id_delivery ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}