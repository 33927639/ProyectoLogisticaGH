<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RouteResource\Pages;
use App\Filament\Resources\RouteResource\RelationManagers;
use App\Models\Route;
use App\Models\Municipality;
use App\Services\GoogleMapsService;
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
    protected static ?int $navigationSort = 2;

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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        static::calculateDistanceIfBothSelected($get, $set);
                                    }),
                                Forms\Components\Select::make('destination_id')
                                    ->label('Municipio Destino')
                                    ->relationship('destination', 'name_municipality')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        static::calculateDistanceIfBothSelected($get, $set);
                                    }),
                                Forms\Components\TextInput::make('distance_km')
                                    ->label('Distancia (km)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('km')
                                    ->helperText('Calculada automáticamente con Google Maps')
                                    ->required(),
                                Forms\Components\TextInput::make('estimated_duration')
                                    ->label('Tiempo Estimado (min)')
                                    ->numeric()
                                    ->suffix('min')
                                    ->helperText('Calculado automáticamente con Google Maps'),
                                Forms\Components\Toggle::make('status')
                                    ->label('Activa')
                                    ->default(true),
                            ]),
                    ]),
            ]);
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
                Tables\Columns\TextColumn::make('estimated_duration')
                    ->label('Tiempo Est.')
                    ->numeric(0)
                    ->suffix(' min')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('calculate_distance')
                    ->label('Calcular Distancia')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->action(function (Route $record) {
                        $googleMapsService = app(GoogleMapsService::class);
                        
                        if (!$record->origin || !$record->destination) {
                            Notification::make()
                                ->title('Error')
                                ->body('La ruta debe tener origen y destino definidos')
                                ->danger()
                                ->send();
                            return;
                        }

                        $origin = $record->origin->name_municipality . ', Guatemala';
                        $destination = $record->destination->name_municipality . ', Guatemala';
                        
                        $result = $googleMapsService->calculateDistance($origin, $destination);
                        
                        if ($result['status'] === 'OK') {
                            $record->update([
                                'distance_km' => $result['distance'],
                                'estimated_duration' => $result['duration']
                            ]);
                            
                            Notification::make()
                                ->title('Distancia Calculada')
                                ->body("Distancia: {$result['distance_text']}, Tiempo: {$result['duration_text']}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error al Calcular')
                                ->body('No se pudo calcular la distancia. Verifica la configuración de Google Maps.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('calculate_distances')
                        ->label('Calcular Distancias')
                        ->icon('heroicon-o-map-pin')
                        ->color('info')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $googleMapsService = app(GoogleMapsService::class);
                            $calculated = 0;
                            $errors = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->origin || !$record->destination) {
                                    $errors++;
                                    continue;
                                }

                                $origin = $record->origin->name_municipality . ', Guatemala';
                                $destination = $record->destination->name_municipality . ', Guatemala';
                                
                                $result = $googleMapsService->calculateDistance($origin, $destination);
                                
                                if ($result['status'] === 'OK') {
                                    $record->update([
                                        'distance_km' => $result['distance'],
                                        'estimated_duration' => $result['duration']
                                    ]);
                                    $calculated++;
                                } else {
                                    $errors++;
                                }
                                
                                // Pausa para no sobrecargar la API
                                usleep(200000); // 0.2 segundos
                            }
                            
                            Notification::make()
                                ->title('Cálculo Completado')
                                ->body("Calculadas: {$calculated}, Errores: {$errors}")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Calcular Distancias')
                        ->modalDescription('Esto calculará las distancias para todas las rutas seleccionadas usando Google Maps API.')
                        ->modalSubmitActionLabel('Calcular'),
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

    /**
     * Calculate distance automatically when both origin and destination are selected
     */
    protected static function calculateDistanceIfBothSelected($get, $set)
    {
        $originId = $get('origin_id');
        $destinationId = $get('destination_id');

        if ($originId && $destinationId && $originId != $destinationId) {
            try {
                $origin = Municipality::find($originId);
                $destination = Municipality::find($destinationId);
                
                if ($origin && $destination) {
                    $googleMapsService = app(GoogleMapsService::class);
                    $originAddress = $origin->name_municipality . ', Guatemala';
                    $destinationAddress = $destination->name_municipality . ', Guatemala';
                    
                    $result = $googleMapsService->calculateDistance($originAddress, $destinationAddress);
                    
                    if ($result['status'] === 'OK') {
                        $set('distance_km', round($result['distance'], 2));
                        $set('estimated_duration', round($result['duration']));
                        
                        // Enviar notificación de éxito
                        Notification::make()
                            ->title('Distancia Calculada Automáticamente')
                            ->body("Distancia: {$result['distance_text']}, Tiempo: {$result['duration_text']}")
                            ->success()
                            ->send();
                    } else {
                        // Limpiar campos si hay error
                        $set('distance_km', null);
                        $set('estimated_duration', null);
                        
                        if ($result['status'] === 'API_KEY_MISSING') {
                            Notification::make()
                                ->title('Google Maps no configurado')
                                ->body('La API key de Google Maps no está configurada')
                                ->warning()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error al calcular distancia')
                                ->body('No se pudo calcular la distancia automáticamente')
                                ->warning()
                                ->send();
                        }
                    }
                }
            } catch (\Exception $e) {
                // Silenciar errores en el formulario para evitar interrupciones
                \Log::error('Error calculating distance in form', [
                    'error' => $e->getMessage(),
                    'origin_id' => $originId,
                    'destination_id' => $destinationId
                ]);
            }
        }
    }
}
