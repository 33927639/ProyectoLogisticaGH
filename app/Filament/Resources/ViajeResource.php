<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ViajeResource\Pages;
use App\Filament\Resources\ViajeResource\RelationManagers;
use App\Models\Viaje;
use App\Models\Camion;
use App\Models\Piloto;
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
use Carbon\Carbon;
use Exception;

class ViajeResource extends Resource
{
    protected static ?string $model = Viaje::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Viajes';
    
    protected static ?string $modelLabel = 'Viaje';
    
    protected static ?string $pluralModelLabel = 'Viajes';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Operaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asignación de Recursos')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('camion_id')
                                    ->label('Camión')
                                    ->relationship(
                                        'camion',
                                        'placa',
                                        fn (Builder $query) => $query->where('estado', Camion::ESTADO_ACTIVO)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Camion $record) => 
                                        "{$record->placa} - {$record->marca} {$record->modelo}"
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                        if ($state) {
                                            $camion = Camion::find($state);
                                            if ($camion) {
                                                $set('kilometraje_inicial', $camion->kilometraje_actual);
                                            }
                                        }
                                    }),
                                
                                Forms\Components\Select::make('piloto_id')
                                    ->label('Piloto')
                                    ->relationship(
                                        'piloto',
                                        'nombre',
                                        fn (Builder $query) => $query->where('estado', Piloto::ESTADO_ACTIVO)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Piloto $record) => 
                                        "{$record->nombre_completo} ({$record->licencia})"
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                
                                Forms\Components\Select::make('ruta_id')
                                    ->label('Ruta')
                                    ->relationship(
                                        'ruta',
                                        'origen',
                                        fn (Builder $query) => $query->where('estado', Ruta::ESTADO_ACTIVA)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Ruta $record) => 
                                        "{$record->nombre_completo} ({$record->distancia_km} km)"
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Detalles del Viaje')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('kilometraje_inicial')
                                    ->label('Kilometraje Inicial')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('km')
                                    ->helperText('Se actualizará automáticamente al seleccionar el camión'),
                                
                                Forms\Components\TextInput::make('kilometraje_final')
                                    ->label('Kilometraje Final')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('km')
                                    ->hidden(fn (Forms\Get $get) => !in_array($get('estado'), [Viaje::ESTADO_COMPLETADO]))
                                    ->helperText('Solo requerido al completar el viaje'),
                            ]),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('fecha_inicio')
                                    ->label('Fecha y Hora de Inicio')
                                    ->required()
                                    ->default(now())
                                    ->minDate(now()->subDays(1))
                                    ->maxDate(now()->addMonths(3))
                                    ->seconds(false)
                                    ->live(),
                                
                                Forms\Components\DateTimePicker::make('fecha_fin')
                                    ->label('Fecha y Hora de Fin')
                                    ->minDate(fn (Forms\Get $get) => $get('fecha_inicio'))
                                    ->seconds(false)
                                    ->hidden(fn (Forms\Get $get) => !in_array($get('estado'), [Viaje::ESTADO_COMPLETADO]))
                                    ->helperText('Se establece automáticamente al completar'),
                            ]),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado del Viaje')
                            ->required()
                            ->options([
                                Viaje::ESTADO_PROGRAMADO => 'Programado',
                                Viaje::ESTADO_EN_CURSO => 'En Curso',
                                Viaje::ESTADO_COMPLETADO => 'Completado',
                                Viaje::ESTADO_CANCELADO => 'Cancelado',
                            ])
                            ->default(Viaje::ESTADO_PROGRAMADO)
                            ->native(false)
                            ->live()
                            ->helperText('El estado determina qué campos son requeridos'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('ruta.nombre_completo')
                    ->label('Ruta')
                    ->searchable(['ruta.origen', 'ruta.destino'])
                    ->weight(FontWeight::Bold)
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('camion.placa')
                    ->label('Camión')
                    ->searchable()
                    ->description(fn (?Viaje $record): ?string => 
                        $record && $record->camion ? "{$record->camion->marca} {$record->camion->modelo}" : null
                    )
                    ->weight(FontWeight::Medium),
                
                Tables\Columns\TextColumn::make('piloto.nombre_completo')
                    ->label('Piloto')
                    ->searchable(['piloto.nombre', 'piloto.apellido'])
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(function (?Viaje $record): ?string {
                        try {
                            return $record && $record->esta_en_curso && $record->tiempo_restante 
                                ? $record->tiempo_restante 
                                : null;
                        } catch (Exception $e) {
                            return null;
                        }
                    }),
                
                Tables\Columns\TextColumn::make('porcentaje_progreso')
                    ->label('Progreso')
                    ->formatStateUsing(fn (?float $state): string => 
                        $state !== null ? number_format($state, 1) . '%' : 'N/D'
                    )
                    ->color(fn (?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 100 => 'success',
                        $state >= 75 => 'info',
                        $state >= 50 => 'warning',
                        default => 'gray',
                    })
                    ->alignEnd()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('kilometros_recorridos')
                    ->label('Km Recorridos')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' km')
                    ->placeholder('Pendiente')
                    ->alignEnd()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('duracion_formato')
                    ->label('Duración')
                    ->placeholder('En progreso')
                    ->alignCenter()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Programado' => 'gray',
                        'En Curso' => 'info',
                        'Completado' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('esta_retrasado')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->tooltip(function (?Viaje $record) {
                        try {
                            return $record && $record->esta_retrasado ? 'Viaje retrasado' : 'En tiempo';
                        } catch (Exception $e) {
                            return 'Estado desconocido';
                        }
                    })
                    ->visible(function (?Viaje $record) {
                        try {
                            return $record && $record->esta_en_curso;
                        } catch (Exception $e) {
                            return false;
                        }
                    }),
                
                Tables\Columns\TextColumn::make('eficiencia')
                    ->label('Eficiencia')
                    ->formatStateUsing(fn (?float $state): string => $state ? number_format($state, 1) . '%' : 'N/D')
                    ->color(fn (?float $state) => match (true) {
                        $state === null => 'gray',
                        $state >= 90 => 'success',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    })
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        Viaje::ESTADO_PROGRAMADO => 'Programado',
                        Viaje::ESTADO_EN_CURSO => 'En Curso',
                        Viaje::ESTADO_COMPLETADO => 'Completado',
                        Viaje::ESTADO_CANCELADO => 'Cancelado',
                    ])
                    ->placeholder('Todos los estados'),
                
                SelectFilter::make('camion_id')
                    ->label('Camión')
                    ->relationship('camion', 'placa')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los camiones'),
                
                SelectFilter::make('piloto_id')
                    ->label('Piloto')
                    ->relationship('piloto', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Piloto $record) => $record->nombre_completo)
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los pilotos'),
                
                SelectFilter::make('ruta_id')
                    ->label('Ruta')
                    ->relationship('ruta', 'origen')
                    ->getOptionLabelFromRecordUsing(fn (Ruta $record) => $record->nombre_completo)
                    ->searchable()
                    ->preload()
                    ->placeholder('Todas las rutas'),
                
                Filter::make('en_curso')
                    ->label('En Curso')
                    ->query(fn (Builder $query): Builder => $query->where('estado', Viaje::ESTADO_EN_CURSO))
                    ->toggle(),
                
                Filter::make('retrasados')
                    ->label('Retrasados')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('estado', Viaje::ESTADO_EN_CURSO)
                              ->whereRaw('NOW() > DATE_ADD(fecha_inicio, INTERVAL (SELECT tiempo_estimado_horas FROM rutas WHERE id = viajes.ruta_id) HOUR)')
                    )
                    ->toggle(),
                
                Filter::make('hoy')
                    ->label('Viajes de Hoy')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereDate('fecha_inicio', Carbon::today())
                    )
                    ->toggle(),
                
                Filter::make('esta_semana')
                    ->label('Esta Semana')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereBetween('fecha_inicio', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek()
                        ])
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->visible(fn (?Viaje $record) => $record && !$record->esta_completado),
                    
                    Tables\Actions\Action::make('iniciar')
                        ->label('Iniciar Viaje')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn (?Viaje $record) => $record && $record->estado === Viaje::ESTADO_PROGRAMADO)
                        ->requiresConfirmation()
                        ->modalHeading('Iniciar Viaje')
                        ->modalDescription(fn (?Viaje $record) => $record ?
                            "¿Iniciar el viaje " . ($record->ruta ? $record->ruta->nombre_completo : 'sin ruta') . 
                            " con " . ($record->piloto ? $record->piloto->nombre_completo : 'sin piloto') . "?" 
                            : 'Sin información del viaje'
                        )
                        ->action(function (?Viaje $record): void {
                            if ($record && $record->iniciar()) {
                                Notification::make()
                                    ->title('Viaje iniciado')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('No se pudo iniciar el viaje')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('completar')
                        ->label('Completar')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (?Viaje $record) => $record && $record->estado === Viaje::ESTADO_EN_CURSO)
                        ->form([
                            Forms\Components\TextInput::make('kilometraje_final')
                                ->label('Kilometraje Final')
                                ->required()
                                ->numeric()
                                ->minValue(fn (?Viaje $record) => $record ? $record->kilometraje_inicial : 0)
                                ->suffix('km')
                                ->helperText(fn (?Viaje $record) => $record ?
                                    "Kilometraje inicial: {$record->kilometraje_inicial} km" : ''
                                ),
                        ])
                        ->action(function (?Viaje $record, array $data): void {
                            if ($record && $record->completar($data['kilometraje_final'])) {
                                Notification::make()
                                    ->title('Viaje completado')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('No se pudo completar el viaje')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('cancelar')
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->visible(fn (?Viaje $record) => $record && !$record->esta_completado)
                        ->requiresConfirmation()
                        ->modalHeading('Cancelar Viaje')
                        ->modalDescription('Esta acción no se puede deshacer.')
                        ->action(function (?Viaje $record): void {
                            if ($record && $record->cancelar()) {
                                Notification::make()
                                    ->title('Viaje cancelado')
                                    ->success()
                                    ->send();
                            }
                        }),
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
                                    Viaje::ESTADO_PROGRAMADO => 'Programado',
                                    Viaje::ESTADO_CANCELADO => 'Cancelado',
                                ])
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (?Viaje $record) use ($data) {
                                if ($record) {
                                    if ($data['estado'] === Viaje::ESTADO_CANCELADO) {
                                        $record->cancelar();
                                    } else {
                                        $record->update(['estado' => $data['estado']]);
                                    }
                                }
                            });
                            
                            Notification::make()
                                ->title('Estados actualizados')
                                ->body("Se actualizaron {$records->count()} viajes")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('fecha_inicio', 'desc')
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
        return static::getModel()::where('estado', Viaje::ESTADO_EN_CURSO)->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $enCurso = static::getModel()::where('estado', Viaje::ESTADO_EN_CURSO)->count();
        $retrasados = static::getModel()::where('estado', Viaje::ESTADO_EN_CURSO)
            ->whereRaw('NOW() > DATE_ADD(fecha_inicio, INTERVAL (SELECT tiempo_estimado_horas FROM rutas WHERE id = viajes.ruta_id) HOUR)')
            ->count();
        
        if ($retrasados > 0) {
            return 'danger';
        } elseif ($enCurso > 10) {
            return 'warning';
        } elseif ($enCurso > 0) {
            return 'info';
        }
        
        return 'gray';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListViajes::route('/'),
            'create' => Pages\CreateViaje::route('/create'),
            'edit' => Pages\EditViaje::route('/{record}/edit'),
        ];
    }
}
