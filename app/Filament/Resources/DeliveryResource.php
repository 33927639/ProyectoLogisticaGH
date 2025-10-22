<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
use App\Models\Route;
use App\Models\Municipality;
use App\Models\DeliveryStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationGroup = 'Operaciones';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Entrega')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('delivery_date')
                                    ->label('Fecha de Entrega')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\Select::make('route_id')
                                    ->label('Ruta')
                                    ->relationship('route', 'id_route')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->origin->name_municipality} → {$record->destination->name_municipality} ({$record->distance_km} km)")
                                    ->searchable(['origin.name_municipality', 'destination.name_municipality'])
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $route = Route::find($state);
                                            if ($route) {
                                                $set('route_info', "Distancia: {$route->distance_km} km - {$route->origin->name_municipality} → {$route->destination->name_municipality}");
                                            }
                                        }
                                    })
                                    ->hint('Las rutas se crean automáticamente con distancias de Google Maps')
                                    ->hintColor('info')
                                    ->createOptionForm([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('origin_id')
                                                    ->label('Municipio Origen')
                                                    ->relationship('origin', 'name_municipality')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        if ($state && $get('destination_id')) {
                                                            \App\Filament\Resources\RouteResource::calculateDistanceAndCheckDuplicate($state, $get('destination_id'), $set);
                                                        }
                                                    }),
                                                Forms\Components\Select::make('destination_id')
                                                    ->label('Municipio Destino')
                                                    ->relationship('destination', 'name_municipality')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        if ($state && $get('origin_id')) {
                                                            \App\Filament\Resources\RouteResource::calculateDistanceAndCheckDuplicate($get('origin_id'), $state, $set);
                                                        }
                                                    }),
                                                Forms\Components\TextInput::make('distance_km')
                                                    ->label('Distancia (km)')
                                                    ->numeric()
                                                    ->step(0.01)
                                                    ->suffix('km')
                                                    ->required()
                                                    ->readOnly(),
                                                Forms\Components\Toggle::make('status')
                                                    ->label('Activa')
                                                    ->default(true),
                                            ])
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        // Verificar si ya existe la ruta
                                        $existingRoute = Route::findExistingRoute($data['origin_id'], $data['destination_id']);
                                        if ($existingRoute) {
                                            return $existingRoute->id_route;
                                        }
                                        
                                        // Crear nueva ruta
                                        $route = Route::create($data);
                                        return $route->id_route;
                                    }),
                                Forms\Components\Select::make('status_id')
                                    ->label('Estado de Entrega')
                                    ->options(\App\Models\DeliveryStatus::where('status', true)->pluck('name_status', 'id_status'))
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Toggle::make('status')
                                    ->label('Activa')
                                    ->default(true),
                            ]),
                    ]),
                Forms\Components\Section::make('Información de Ruta Seleccionada')
                    ->schema([
                        Forms\Components\Placeholder::make('route_info')
                            ->label('Detalles de la Ruta')
                            ->content(function ($get) {
                                if ($get('route_id')) {
                                    $route = Route::find($get('route_id'));
                                    if ($route) {
                                        return "📍 {$route->origin->name_municipality} → {$route->destination->name_municipality}\n📏 Distancia: {$route->distance_km} km\n🏢 Departamento Origen: {$route->origin->department->name_department}\n🏢 Departamento Destino: {$route->destination->department->name_department}";
                                    }
                                }
                                return 'Seleccione una ruta para ver los detalles';
                            }),
                    ])
                    ->visible(fn ($get) => $get('route_id'))
                    ->collapsible(),
            ]);
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
                Tables\Columns\TextColumn::make('route.distance_km')
                    ->label('Distancia')
                    ->suffix(' km')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_status.name_status')
                        ->label('Estado')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Pendiente' => 'warning',
                            'En Ruta' => 'info',
                            'Entregado' => 'success',
                            'Cancelado' => 'danger',
                            default => 'gray',
                        }),
                Tables\Columns\TextColumn::make('assignment_status')
                    ->label('Asignación')
                    ->getStateUsing(function ($record) {
                        $assignment = $record->deliveryAssignments()->first();
                        if (!$assignment) {
                            return 'Sin asignar';
                        }
                        return ucfirst($assignment->status) . ' - ' . $assignment->driver->first_name;
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'Sin asignar') => 'gray',
                        str_contains($state, 'Pendiente') => 'warning',
                        str_contains($state, 'Aceptado') => 'info',
                        str_contains($state, 'En_ruta') => 'primary',
                        str_contains($state, 'Completado') => 'success',
                        str_contains($state, 'Rechazado') => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Estado')
                    ->options(\App\Models\DeliveryStatus::where('status', true)->pluck('name_status', 'id_status')),
                Tables\Filters\Filter::make('delivery_date')
                    ->form([
                        Forms\Components\DatePicker::make('delivery_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('delivery_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['delivery_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '>=', $date),
                            )
                            ->when(
                                $data['delivery_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '<=', $date),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Activa'),
            ])
            ->actions([
                Tables\Actions\Action::make('assign_driver')
                    ->label('Asignar Conductor')
                    ->icon('heroicon-m-user-plus')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('driver_id')
                            ->label('Conductor')
                            ->relationship('', 'first_name')
                            ->options(\App\Models\Driver::where('status', true)->get()->pluck('full_name', 'id_driver'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehículo')
                            ->relationship('', 'plate')
                            ->options(\App\Models\Vehicle::where('status', true)->get()->pluck('plate', 'id_vehicle'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('assignment_date')
                            ->label('Fecha de Asignación')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        \App\Models\DeliveryAssignment::create([
                            'delivery_id' => $record->id_delivery,
                            'driver_id' => $data['driver_id'],
                            'vehicle_id' => $data['vehicle_id'],
                            'assignment_date' => $data['assignment_date'],
                            'status' => 'pendiente',
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Conductor Asignado')
                            ->body('El conductor ha sido asignado exitosamente a esta entrega.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => !$record->deliveryAssignments()->exists())
                    ->modalHeading('Asignar Conductor y Vehículo')
                    ->modalDescription('Seleccione el conductor y vehículo para esta entrega.'),
                    
                Tables\Actions\Action::make('view_assignments')
                    ->label('Ver Asignaciones')
                    ->icon('heroicon-m-users')
                    ->color('gray')
                    ->url(fn ($record) => \App\Filament\Resources\DeliveryAssignmentResource::getUrl('index', ['delivery_id' => $record->id_delivery]))
                    ->visible(fn ($record) => $record->deliveryAssignments()->exists()),
                    
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
