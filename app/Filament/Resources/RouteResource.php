<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RouteResource\Pages;
use App\Filament\Resources\RouteResource\RelationManagers;
use App\Models\Route;
use App\Models\Municipality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    
    protected static ?string $navigationGroup = 'Operaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Ruta')
                    ->schema([
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
                                            static::calculateDistanceAndCheckDuplicate($state, $get('destination_id'), $set);
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
                                            static::calculateDistanceAndCheckDuplicate($get('origin_id'), $state, $set);
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
                            ]),
                    ])
                    ->description('La distancia se calcula automáticamente usando Google Maps. Si la ruta ya existe, se mostrará la información existente.'),
                
                Forms\Components\Section::make('Información de Ruta Existente')
                    ->schema([
                        Forms\Components\Placeholder::make('existing_route_info')
                            ->label('')
                            ->content(function ($get) {
                                if ($get('origin_id') && $get('destination_id')) {
                                    $existingRoute = \App\Models\Route::findExistingRoute($get('origin_id'), $get('destination_id'));
                                    if ($existingRoute) {
                                        return "⚠️ Ya existe una ruta entre estos municipios: {$existingRoute->route_name} ({$existingRoute->distance_km} km)";
                                    }
                                }
                                return '';
                            })
                            ->visible(function ($get) {
                                if ($get('origin_id') && $get('destination_id')) {
                                    return \App\Models\Route::findExistingRoute($get('origin_id'), $get('destination_id')) !== null;
                                }
                                return false;
                            }),
                    ])
                    ->visible(function ($get) {
                        if ($get('origin_id') && $get('destination_id')) {
                            return \App\Models\Route::findExistingRoute($get('origin_id'), $get('destination_id')) !== null;
                        }
                        return false;
                    })
                    ->color('warning'),
            ]);
    }

    protected static function calculateDistanceAndCheckDuplicate($originId, $destinationId, $set)
    {
        if (!$originId || !$destinationId || $originId === $destinationId) {
            return;
        }

        // Buscar ruta existente
        $existingRoute = \App\Models\Route::findExistingRoute($originId, $destinationId);
        
        if ($existingRoute) {
            // Si existe, usar la distancia existente
            $set('distance_km', $existingRoute->distance_km);
            return;
        }

        // Si no existe, calcular nueva distancia
        try {
            $origin = \App\Models\Municipality::find($originId);
            $destination = \App\Models\Municipality::find($destinationId);
            
            if ($origin && $destination) {
                $googleMaps = app(\App\Services\GoogleMapsService::class);
                $distanceData = $googleMaps->calculateDistance(
                    $origin->name_municipality,
                    $destination->name_municipality
                );
                
                if ($distanceData) {
                    $set('distance_km', $distanceData['distance_km']);
                } else {
                    $set('distance_km', 0);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error calculating route distance', [
                'error' => $e->getMessage(),
                'origin_id' => $originId,
                'destination_id' => $destinationId
            ]);
            $set('distance_km', 0);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin.name_municipality')
                    ->label('Origen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('destination.name_municipality')
                    ->label('Destino')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Distancia')
                    ->numeric(2)
                    ->suffix(' km')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deliveries_count')
                    ->label('Entregas')
                    ->counts('deliveries')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('origin_id')
                    ->label('Origen')
                    ->relationship('origin', 'name_municipality')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('destination_id')
                    ->label('Destino')
                    ->relationship('destination', 'name_municipality')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Activa'),
            ])
            ->actions([
                Tables\Actions\Action::make('recalculate_distance')
                    ->label('Recalcular Distancia')
                    ->icon('heroicon-m-arrow-path')
                    ->color('info')
                    ->action(function (Route $record) {
                        try {
                            $googleMaps = app(\App\Services\GoogleMapsService::class);
                            $distanceData = $googleMaps->calculateDistance(
                                $record->origin->name_municipality,
                                $record->destination->name_municipality
                            );
                            
                            if ($distanceData) {
                                $oldDistance = $record->distance_km;
                                $record->update(['distance_km' => $distanceData['distance_km']]);
                                
                                Notification::make()
                                    ->title('Distancia Recalculada')
                                    ->body("Distancia actualizada de {$oldDistance} km a {$distanceData['distance_km']} km")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Error')
                                    ->body('No se pudo calcular la distancia. Verifique su conexión a internet.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Error al recalcular distancia: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Recalcular Distancia')
                    ->modalDescription('¿Está seguro que desea recalcular la distancia usando Google Maps?'),
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
            'index' => Pages\ListRoutes::route('/'),
            'create' => Pages\CreateRoute::route('/create'),
            'edit' => Pages\EditRoute::route('/{record}/edit'),
        ];
    }
}
