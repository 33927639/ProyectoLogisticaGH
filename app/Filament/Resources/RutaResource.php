<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutaResource\Pages;
use App\Filament\Resources\RutaResource\RelationManagers;
use App\Filament\Resources\ViajeResource;
use App\Models\Ruta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RutaResource extends Resource
{
    protected static ?string $model = Ruta::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    
    protected static ?string $navigationLabel = 'Rutas';
    
    protected static ?string $modelLabel = 'Ruta';
    
    protected static ?string $pluralModelLabel = 'Rutas';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $navigationGroup = 'Operaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ubicaciones')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('origen')
                                    ->label('Ciudad de Origen')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Ciudad de Guatemala')
                                    ->suffixIcon('heroicon-m-map-pin'),
                                Forms\Components\TextInput::make('destino')
                                    ->label('Ciudad de Destino')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Quetzaltenango')
                                    ->suffixIcon('heroicon-m-flag'),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Detalles de la Ruta')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('distancia_km')
                                    ->label('Distancia')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.1)
                                    ->maxValue(9999.99)
                                    ->step(0.01)
                                    ->suffix('km')
                                    ->placeholder('0.00'),
                                Forms\Components\TextInput::make('tiempo_estimado_horas')
                                    ->label('Tiempo Estimado')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.1)
                                    ->maxValue(99.99)
                                    ->step(0.1)
                                    ->suffix('horas')
                                    ->placeholder('0.0')
                                    ->helperText('Tiempo estimado de viaje en horas'),
                            ]),
                        
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Descripción opcional de la ruta, puntos de interés, etc.')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado de la Ruta')
                            ->required()
                            ->options([
                                Ruta::ESTADO_ACTIVA => 'Activa',
                                Ruta::ESTADO_INACTIVA => 'Inactiva',
                            ])
                            ->default(Ruta::ESTADO_ACTIVA)
                            ->native(false)
                            ->helperText('Solo las rutas activas estarán disponibles para programar viajes'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Ruta')
                    ->searchable(['origen', 'destino'])
                    ->sortable(['origen'])
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Ruta copiada'),
                
                Tables\Columns\TextColumn::make('distancia_formateada')
                    ->label('Distancia')
                    ->alignEnd()
                    ->sortable('distancia_km')
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('tiempo_estimado_formato')
                    ->label('Tiempo Est.')
                    ->alignCenter()
                    ->sortable('tiempo_estimado_horas')
                    ->tooltip('Tiempo estimado de viaje'),
                
                Tables\Columns\TextColumn::make('velocidad_promedio')
                    ->label('Velocidad Prom.')
                    ->alignEnd()
                    ->suffix(' km/h')
                    ->numeric(decimalPlaces: 0)
                    ->color(fn (Ruta $record) => match (true) {
                        $record->velocidad_promedio >= 80 => 'success',
                        $record->velocidad_promedio >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->tooltip('Velocidad promedio calculada'),
                
                Tables\Columns\TextColumn::make('dificultad')
                    ->label('Dificultad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Fácil' => 'success',
                        'Moderada' => 'info',
                        'Difícil' => 'warning',
                        'Muy Difícil' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('total_viajes_completados')
                    ->label('Viajes')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip('Total de viajes completados en esta ruta'),
                
                Tables\Columns\TextColumn::make('tiempo_promedio_real')
                    ->label('Tiempo Real Prom.')
                    ->alignCenter()
                    ->formatStateUsing(fn (?float $state): string => $state ? number_format($state, 1) . 'h' : 'N/D')
                    ->tooltip('Tiempo promedio real de viajes completados')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('eficiencia_ruta')
                    ->label('Eficiencia')
                    ->alignEnd()
                    ->formatStateUsing(fn (?float $state): string => $state ? number_format($state, 1) . '%' : 'N/D')
                    ->color(fn (?float $state) => match (true) {
                        $state === null => 'gray',
                        $state >= 90 => 'success',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    })
                    ->tooltip('Eficiencia: tiempo estimado vs real')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activa' => 'success',
                        'Inactiva' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('tiene_ruta_inversa')
                    ->label('Ida/Vuelta')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-arrow-right')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (Ruta $record) => $record->tiene_ruta_inversa ? 'Tiene ruta de regreso' : 'Solo ida'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        Ruta::ESTADO_ACTIVA => 'Activa',
                        Ruta::ESTADO_INACTIVA => 'Inactiva',
                    ])
                    ->placeholder('Todos los estados'),
                
                SelectFilter::make('dificultad')
                    ->label('Dificultad')
                    ->options([
                        'Fácil' => 'Fácil',
                        'Moderada' => 'Moderada',
                        'Difícil' => 'Difícil',
                        'Muy Difícil' => 'Muy Difícil',
                    ])
                    ->placeholder('Todas las dificultades'),
                
                Filter::make('rutas_largas')
                    ->label('Rutas Largas (+200 km)')
                    ->query(fn (Builder $query): Builder => $query->where('distancia_km', '>', 200))
                    ->toggle(),
                
                Filter::make('con_ruta_inversa')
                    ->label('Con Ruta de Regreso')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereExists(function ($query) {
                            $query->from('rutas as r2')
                                  ->whereColumn('r2.origen', 'rutas.destino')
                                  ->whereColumn('r2.destino', 'rutas.origen')
                                  ->where('r2.estado', Ruta::ESTADO_ACTIVA);
                        })
                    )
                    ->toggle(),
                
                SelectFilter::make('origen')
                    ->label('Origen')
                    ->options(fn (): array => 
                        Ruta::query()
                            ->distinct()
                            ->pluck('origen', 'origen')
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Todas las ciudades'),
                
                SelectFilter::make('destino')
                    ->label('Destino')
                    ->options(fn (): array => 
                        Ruta::query()
                            ->distinct()
                            ->pluck('destino', 'destino')
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Todas las ciudades'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    
                    Tables\Actions\Action::make('programar_viaje')
                        ->label('Programar Viaje')
                        ->icon('heroicon-o-plus')
                        ->color('success')
                        ->visible(fn (Ruta $record) => $record->estado === Ruta::ESTADO_ACTIVA)
                        ->url(fn (Ruta $record): string => ViajeResource::getUrl('create', ['ruta_id' => $record->id])),
                    
                    Tables\Actions\Action::make('crear_ruta_inversa')
                        ->label('Crear Ruta de Regreso')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (Ruta $record) => !$record->tiene_ruta_inversa && $record->estado === Ruta::ESTADO_ACTIVA)
                        ->requiresConfirmation()
                        ->modalHeading('Crear Ruta de Regreso')
                        ->modalDescription(fn (Ruta $record) => "¿Crear ruta de regreso de {$record->destino} a {$record->origen}?")
                        ->action(function (Ruta $record): void {
                            Ruta::create([
                                'origen' => $record->destino,
                                'destino' => $record->origen,
                                'distancia_km' => $record->distancia_km,
                                'tiempo_estimado_horas' => $record->tiempo_estimado_horas,
                                'descripcion' => "Ruta de regreso de {$record->destino} a {$record->origen}",
                                'estado' => Ruta::ESTADO_ACTIVA,
                            ]);
                            
                            Notification::make()
                                ->title('Ruta de regreso creada')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('ver_estadisticas')
                        ->label('Estadísticas')
                        ->icon('heroicon-o-chart-bar')
                        ->color('warning')
                        ->modalHeading(fn (Ruta $record) => "Estadísticas de {$record->nombre_completo}")
                        ->modalContent(fn (Ruta $record) => view('filament.pages.ruta-stats', compact('record')))
                        ->modalWidth('4xl'),
                ])->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cambiar_estado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo Estado')
                                ->required()
                                ->options([
                                    Ruta::ESTADO_ACTIVA => 'Activa',
                                    Ruta::ESTADO_INACTIVA => 'Inactiva',
                                ])
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Ruta $record) use ($data) {
                                $record->update(['estado' => $data['estado']]);
                            });
                            
                            Notification::make()
                                ->title('Estados actualizados')
                                ->body("Se actualizaron {$records->count()} rutas")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // Will be added after creating the relation managers
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', Ruta::ESTADO_ACTIVA)->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $activas = static::getModel()::where('estado', Ruta::ESTADO_ACTIVA)->count();
        
        if ($activas > 20) {
            return 'success';
        } elseif ($activas > 10) {
            return 'warning';
        }
        
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRutas::route('/'),
            'create' => Pages\CreateRuta::route('/create'),
            'edit' => Pages\EditRuta::route('/{record}/edit'),
        ];
    }
}
