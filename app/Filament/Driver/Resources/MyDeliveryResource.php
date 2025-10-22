<?php

namespace App\Filament\Driver\Resources;

use App\Filament\Driver\Resources\MyDeliveryResource\Pages;
use App\Models\Delivery;
use App\Models\DeliveryAssignment;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class MyDeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Mis Entregas';
    
    protected static ?string $modelLabel = 'Entrega';
    
    protected static ?string $pluralModelLabel = 'Mis Entregas';

    /**
     * Helper para obtener el driver del usuario actual
     */
    protected static function getCurrentDriver()
    {
        $user = auth('driver')->user();
        if (!$user) {
            return null;
        }
        $fullName = $user->first_name . ' ' . $user->last_name;
        return Driver::where('name', $fullName)->first();
    }

    public static function getEloquentQuery(): Builder
    {
        $driver = static::getCurrentDriver();
        
        if (!$driver) {
            return parent::getEloquentQuery()->whereRaw('1 = 0'); // No mostrar nada si no es conductor
        }

        // Mostrar solo entregas asignadas a este conductor
        return parent::getEloquentQuery()
            ->whereHas('deliveryAssignments', function (Builder $query) use ($driver) {
                $query->where('driver_id', $driver->id_driver);
            })
            ->with(['route.origin', 'route.destination', 'deliveryStatus', 'deliveryAssignments']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Entrega')
                    ->schema([
                        Forms\Components\TextInput::make('id_delivery')
                            ->label('Número de Entrega')
                            ->disabled(),
                        Forms\Components\DatePicker::make('delivery_date')
                            ->label('Fecha de Entrega')
                            ->disabled(),
                        Forms\Components\TextInput::make('route.route_name')
                            ->label('Ruta')
                            ->disabled(),
                        Forms\Components\TextInput::make('deliveryStatus.name_status')
                            ->label('Estado Actual')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_delivery')
                    ->label('# Entrega')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('route.origin.name_municipality')
                    ->label('Origen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('route.destination.name_municipality')
                    ->label('Destino')
                    ->searchable(),
                Tables\Columns\TextColumn::make('route.distance_km')
                    ->label('Distancia')
                    ->suffix(' km')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('deliveryStatus.name_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Aceptado' => 'info',
                        'En Ruta' => 'primary',
                        'Entregado' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignment_status')
                    ->label('Mi Estado')
                    ->getStateUsing(function ($record) {
                        $driver = static::getCurrentDriver();
                        
                        if (!$driver) return 'Sin asignar';
                        
                        $assignment = $record->deliveryAssignments()
                            ->where('driver_id', $driver->id_driver)
                            ->first();
                            
                        return $assignment ? ucfirst(str_replace('_', ' ', $assignment->driver_status)) : 'Sin asignar';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Agarrado' => 'info',
                        'En ruta' => 'primary',
                        'Completado' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Estado de Entrega')
                    ->relationship('deliveryStatus', 'name_status'),
                Tables\Filters\Filter::make('assignment_status')
                    ->label('Mi Estado')
                    ->form([
                        Forms\Components\Select::make('assignment_status')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'agarrado' => 'Agarrado',
                                'en_ruta' => 'En Ruta',
                                'completado' => 'Completado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->placeholder('Todos los estados'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['assignment_status']) {
                            return $query;
                        }
                        
                        $driver = static::getCurrentDriver();
                        
                        if (!$driver) {
                            return $query->whereRaw('1 = 0');
                        }
                        
                        return $query->whereHas('deliveryAssignments', function (Builder $q) use ($driver, $data) {
                            $q->where('driver_id', $driver->id_driver)
                              ->where('driver_status', $data['assignment_status']);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Agarrar')
                    ->icon('heroicon-m-hand-raised')
                    ->color('info')
                    ->action(function ($record) {
                        static::updateDeliveryStatus($record, 'agarrado');
                    })
                    ->visible(fn ($record) => static::canChangeStatus($record, 'pendiente')),
                    
                Tables\Actions\Action::make('start_route')
                    ->label('Iniciar Ruta')
                    ->icon('heroicon-m-play-circle')
                    ->color('primary')
                    ->action(function ($record) {
                        static::updateDeliveryStatus($record, 'en_ruta');
                    })
                    ->visible(fn ($record) => static::canChangeStatus($record, 'agarrado')),
                    
                Tables\Actions\Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->action(function ($record) {
                        static::updateDeliveryStatus($record, 'completado');
                    })
                    ->visible(fn ($record) => static::canChangeStatus($record, 'en_ruta'))
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Entrega Completada')
                    ->modalDescription('¿Está seguro que ha completado esta entrega exitosamente?'),
                    
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        static::updateDeliveryStatus($record, 'cancelado');
                    })
                    ->visible(fn ($record) => in_array(static::getCurrentStatus($record), ['pendiente', 'agarrado']))
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Entrega')
                    ->modalDescription('¿Está seguro que desea cancelar esta entrega?'),
                    
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('Sin entregas asignadas')
            ->emptyStateDescription('No tienes entregas asignadas en este momento.')
            ->emptyStateIcon('heroicon-o-truck');
    }

    protected static function updateDeliveryStatus($record, $newStatus): void
    {
        $driver = static::getCurrentDriver();
        
        if (!$driver) {
            Notification::make()
                ->title('Error')
                ->body('No se encontró información del conductor.')
                ->danger()
                ->send();
            return;
        }

        // Actualizar usando where específico en lugar de find()
        $updated = DeliveryAssignment::where('delivery_id', $record->id_delivery)
            ->where('driver_id', $driver->id_driver)
            ->update(['driver_status' => $newStatus]);
            
        if (!$updated) {
            Notification::make()
                ->title('Error')
                ->body('No se encontró la asignación de esta entrega.')
                ->danger()
                ->send();
            return;
        }

        // Actualizar el estado general de la entrega si es necesario
        if ($newStatus === 'completado') {
            $completedStatus = \App\Models\DeliveryStatus::where('name_status', 'Entregado')->first();
            if ($completedStatus) {
                $record->update(['status_id' => $completedStatus->id_status]);
            }
        } elseif ($newStatus === 'en_ruta') {
            $inRouteStatus = \App\Models\DeliveryStatus::where('name_status', 'En Ruta')->first();
            if ($inRouteStatus) {
                $record->update(['status_id' => $inRouteStatus->id_status]);
            }
        }

        $statusMessages = [
            'agarrado' => 'Entrega agarrada exitosamente',
            'en_ruta' => 'Ruta iniciada exitosamente',
            'completado' => 'Entrega completada exitosamente',
            'cancelado' => 'Entrega cancelada',
        ];

        Notification::make()
            ->title('Estado Actualizado')
            ->body($statusMessages[$newStatus] ?? 'Estado actualizado')
            ->success()
            ->send();
    }

    protected static function canChangeStatus($record, $requiredCurrentStatus): bool
    {
        $driver = static::getCurrentDriver();
        
        if (!$driver) return false;
        
        $assignment = $record->deliveryAssignments()
            ->where('driver_id', $driver->id_driver)
            ->first();
            
        return $assignment && $assignment->driver_status === $requiredCurrentStatus;
    }

    protected static function getCurrentStatus($record): ?string
    {
        $driver = static::getCurrentDriver();
        
        if (!$driver) return null;
        
        $assignment = $record->deliveryAssignments()
            ->where('driver_id', $driver->id_driver)
            ->first();
            
        return $assignment ? $assignment->driver_status : null;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyDeliveries::route('/'),
            'view' => Pages\ViewMyDelivery::route('/{record}'),
        ];
    }
}