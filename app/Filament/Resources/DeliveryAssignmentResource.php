<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryAssignmentResource\Pages;
use App\Models\DeliveryAssignment;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeliveryAssignmentResource extends Resource
{
    protected static ?string $model = DeliveryAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Asignaciones';
    
    protected static ?string $modelLabel = 'Asignación';
    
    protected static ?string $pluralModelLabel = 'Asignaciones';
    
    protected static ?string $navigationGroup = 'Gestión de Entregas';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('delivery_id')
                    ->label('Entrega')
                    ->options(Delivery::all()->mapWithKeys(function ($delivery) {
                        return [$delivery->id_delivery => 'Entrega #' . $delivery->id_delivery . ' - ' . $delivery->delivery_date];
                    }))
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('driver_id')
                    ->label('Conductor')
                    ->options(Driver::all()->mapWithKeys(function ($driver) {
                        return [$driver->id_driver => $driver->name];
                    }))
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('vehicle_id')
                    ->label('Vehículo')
                    ->options(Vehicle::all()->mapWithKeys(function ($vehicle) {
                        return [$vehicle->id_vehicle => $vehicle->license_plate];
                    }))
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('driver_status')
                    ->label('Estado del Conductor')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'agarrado' => 'Agarrado',
                        'en_ruta' => 'En Ruta',
                        'completado' => 'Completado',
                    ])
                    ->default('pendiente')
                    ->required(),
                    
                Forms\Components\DateTimePicker::make('assigned_at')
                    ->label('Fecha de Asignación')
                    ->default(now())
                    ->required(),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('delivery_info')
                    ->label('Entrega')
                    ->getStateUsing(function (DeliveryAssignment $record) {
                        $delivery = Delivery::find($record->delivery_id);
                        return $delivery ? 'Entrega #' . $delivery->id_delivery . ' - ' . $delivery->delivery_date : 'N/A';
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('driver_name')
                    ->label('Conductor')
                    ->getStateUsing(function (DeliveryAssignment $record) {
                        $driver = Driver::find($record->driver_id);
                        return $driver ? $driver->name : 'N/A';
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Vehículo')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('driver_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'agarrado' => 'warning',
                        'en_ruta' => 'primary',
                        'completado' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pendiente' => 'heroicon-o-clock',
                        'agarrado' => 'heroicon-o-hand-raised',
                        'en_ruta' => 'heroicon-o-truck',
                        'completado' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                    
                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Fecha Asignación')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('delivery.delivery_date')
                    ->label('Fecha Entrega')
                    ->date()
                    ->sortable()
                    ->getStateUsing(function (DeliveryAssignment $record) {
                        $delivery = Delivery::find($record->delivery_id);
                        return $delivery ? $delivery->delivery_date : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('driver_status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'agarrado' => 'Agarrado',
                        'en_ruta' => 'En Ruta',
                        'completado' => 'Completado',
                    ]),
                    
                Tables\Filters\Filter::make('assigned_today')
                    ->label('Asignadas Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('assigned_at', today())),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('change_status')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('Nuevo Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'agarrado' => 'Agarrado',
                                'en_ruta' => 'En Ruta',
                                'completado' => 'Completado',
                            ])
                            ->required(),
                    ])
                    ->action(function (DeliveryAssignment $record, array $data): void {
                        $record->update(['driver_status' => $data['new_status']]);
                    })
                    ->successNotificationTitle('Estado actualizado correctamente'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('bulk_status_change')
                        ->label('Cambiar Estado Masivo')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Nuevo Estado')
                                ->options([
                                    'pendiente' => 'Pendiente',
                                    'agarrado' => 'Agarrado',
                                    'en_ruta' => 'En Ruta',
                                    'completado' => 'Completado',
                                ])
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $records->each(function (DeliveryAssignment $record) use ($data) {
                                $record->update(['driver_status' => $data['status']]);
                            });
                        })
                        ->successNotificationTitle('Estados actualizados correctamente'),
                ]),
            ])
            ->defaultSort('assigned_at', 'desc');
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
            'index' => Pages\ListDeliveryAssignments::route('/'),
            'create' => Pages\CreateDeliveryAssignment::route('/create'),
        ];
    }
}
