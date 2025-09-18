<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MantenimientoResource\Pages;
use App\Filament\Resources\MantenimientoResource\RelationManagers;
use App\Models\Mantenimiento;
use App\Models\Camion;
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

class MantenimientoResource extends Resource
{
    protected static ?string $model = Mantenimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    
    protected static ?string $navigationLabel = 'Mantenimiento';
    
    protected static ?string $modelLabel = 'Mantenimiento';
    
    protected static ?string $pluralModelLabel = 'Mantenimientos';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationGroup = 'Gestión de Flota';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Mantenimiento')
                    ->schema([
                        Forms\Components\Select::make('camion_id')
                            ->label('Camión')
                            ->relationship('camion', 'placa')
                            ->getOptionLabelFromRecordUsing(fn (Camion $record) => 
                                "{$record->placa} - {$record->marca} {$record->modelo}"
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tipo_mantenimiento')
                                    ->label('Tipo de Mantenimiento')
                                    ->required()
                                    ->options([
                                        Mantenimiento::TIPO_PREVENTIVO => 'Preventivo',
                                        Mantenimiento::TIPO_CORRECTIVO => 'Correctivo',
                                        Mantenimiento::TIPO_CAMBIO_ACEITE => 'Cambio de Aceite',
                                        Mantenimiento::TIPO_CAMBIO_FILTROS => 'Cambio de Filtros',
                                        Mantenimiento::TIPO_REVISION_FRENOS => 'Revisión de Frenos',
                                        Mantenimiento::TIPO_CAMBIO_LLANTAS => 'Cambio de Llantas',
                                        Mantenimiento::TIPO_REVISION_MOTOR => 'Revisión de Motor',
                                        Mantenimiento::TIPO_MANTENIMIENTO_GENERAL => 'Mantenimiento General',
                                    ])
                                    ->searchable()
                                    ->native(false),
                                
                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->required()
                                    ->options([
                                        Mantenimiento::ESTADO_PROGRAMADO => 'Programado',
                                        Mantenimiento::ESTADO_EN_PROCESO => 'En Proceso',
                                        Mantenimiento::ESTADO_COMPLETADO => 'Completado',
                                        Mantenimiento::ESTADO_CANCELADO => 'Cancelado',
                                    ])
                                    ->default(Mantenimiento::ESTADO_PROGRAMADO)
                                    ->native(false)
                                    ->live(),
                            ]),
                        
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Detalles del mantenimiento, repuestos necesarios, etc.')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Fechas')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_programada')
                                    ->label('Fecha Programada')
                                    ->required()
                                    ->minDate(today())
                                    ->default(today()->addDays(1)),
                                
                                Forms\Components\DatePicker::make('fecha_realizada')
                                    ->label('Fecha Realizada')
                                    ->minDate(fn (Forms\Get $get) => $get('fecha_programada'))
                                    ->visible(fn (Forms\Get $get) => 
                                        in_array($get('estado'), [
                                            Mantenimiento::ESTADO_COMPLETADO
                                        ])
                                    )
                                    ->required(fn (Forms\Get $get) => 
                                        $get('estado') === Mantenimiento::ESTADO_COMPLETADO
                                    ),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Costo')
                    ->schema([
                        Forms\Components\TextInput::make('costo')
                            ->label('Costo Total')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(999999.99)
                            ->step(0.01)
                            ->prefix('Q.')
                            ->placeholder('0.00')
                            ->helperText('Incluye mano de obra y repuestos'),
                    ])
                    ->visible(fn (Forms\Get $get) => 
                        in_array($get('estado'), [
                            Mantenimiento::ESTADO_EN_PROCESO,
                            Mantenimiento::ESTADO_COMPLETADO
                        ])
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('camion.placa')
                    ->label('Camión')
                    ->searchable()
                    ->description(fn (Mantenimiento $record): string => 
                        "{$record->camion->marca} {$record->camion->modelo}"
                    )
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('tipo_mantenimiento')
                    ->label('Tipo')
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('fecha_programada')
                    ->label('F. Programada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Mantenimiento $record) => match (true) {
                        $record->esta_vencido => 'danger',
                        $record->esta_proximo => 'warning',
                        default => 'gray',
                    })
                    ->weight(fn (Mantenimiento $record) => 
                        $record->esta_vencido ? FontWeight::Bold : FontWeight::Medium
                    ),
                
                Tables\Columns\TextColumn::make('fecha_realizada')
                    ->label('F. Realizada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Pendiente'),
                
                Tables\Columns\TextColumn::make('dias_hasta_vencimiento')
                    ->label('Días')
                    ->alignCenter()
                    ->formatStateUsing(fn (?int $state): string => match (true) {
                        $state === null => 'N/A',
                        $state < 0 => 'Vencido ' . abs($state) . 'd',
                        $state === 0 => 'Hoy',
                        default => $state . ' días',
                    })
                    ->color(fn (?int $state) => match (true) {
                        $state === null => 'gray',
                        $state < 0 => 'danger',
                        $state <= 3 => 'warning',
                        $state <= 7 => 'info',
                        default => 'gray',
                    })
                    ->tooltip('Días hasta la fecha programada'),
                
                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Urgente' => 'danger',
                        'Alta' => 'warning',
                        'Media' => 'info',
                        'Baja' => 'gray',
                        'N/A' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('costo_formateado')
                    ->label('Costo')
                    ->alignEnd()
                    ->sortable('costo')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Programado' => 'gray',
                        'En Proceso' => 'warning',
                        'Completado' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('es_preventivo')
                    ->label('Tipo')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-wrench')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn (Mantenimiento $record) => 
                        $record->es_preventivo ? 'Preventivo' : 'Correctivo'
                    )
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('duracion_dias')
                    ->label('Duración')
                    ->alignCenter()
                    ->formatStateUsing(fn (?int $state): string => 
                        $state !== null ? $state . ' días' : 'N/D'
                    )
                    ->tooltip('Días desde programado hasta realizado')
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
                        Mantenimiento::ESTADO_PROGRAMADO => 'Programado',
                        Mantenimiento::ESTADO_EN_PROCESO => 'En Proceso',
                        Mantenimiento::ESTADO_COMPLETADO => 'Completado',
                        Mantenimiento::ESTADO_CANCELADO => 'Cancelado',
                    ])
                    ->placeholder('Todos los estados'),
                
                SelectFilter::make('camion_id')
                    ->label('Camión')
                    ->relationship('camion', 'placa')
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los camiones'),
                
                SelectFilter::make('tipo_mantenimiento')
                    ->label('Tipo')
                    ->options([
                        Mantenimiento::TIPO_PREVENTIVO => 'Preventivo',
                        Mantenimiento::TIPO_CORRECTIVO => 'Correctivo',
                        Mantenimiento::TIPO_CAMBIO_ACEITE => 'Cambio de Aceite',
                        Mantenimiento::TIPO_CAMBIO_FILTROS => 'Cambio de Filtros',
                        Mantenimiento::TIPO_REVISION_FRENOS => 'Revisión de Frenos',
                        Mantenimiento::TIPO_CAMBIO_LLANTAS => 'Cambio de Llantas',
                        Mantenimiento::TIPO_REVISION_MOTOR => 'Revisión de Motor',
                        Mantenimiento::TIPO_MANTENIMIENTO_GENERAL => 'Mantenimiento General',
                    ])
                    ->placeholder('Todos los tipos'),
                
                SelectFilter::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'Urgente' => 'Urgente',
                        'Alta' => 'Alta',
                        'Media' => 'Media',
                        'Baja' => 'Baja',
                    ])
                    ->placeholder('Todas las prioridades'),
                
                Filter::make('vencidos')
                    ->label('Vencidos')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('estado', Mantenimiento::ESTADO_PROGRAMADO)
                              ->where('fecha_programada', '<', Carbon::today())
                    )
                    ->toggle(),
                
                Filter::make('proximos')
                    ->label('Próximos (7 días)')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('estado', Mantenimiento::ESTADO_PROGRAMADO)
                              ->whereBetween('fecha_programada', [
                                  Carbon::today(),
                                  Carbon::today()->addDays(7)
                              ])
                    )
                    ->toggle(),
                
                Filter::make('preventivos')
                    ->label('Preventivos')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('tipo_mantenimiento', 'like', '%preventivo%')
                    )
                    ->toggle(),
                
                Filter::make('correctivos')
                    ->label('Correctivos')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('tipo_mantenimiento', 'like', '%correctivo%')
                    )
                    ->toggle(),
                
                Filter::make('este_mes')
                    ->label('Este Mes')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereMonth('fecha_programada', Carbon::now()->month)
                              ->whereYear('fecha_programada', Carbon::now()->year)
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->visible(fn (Mantenimiento $record) => 
                            $record->estado !== Mantenimiento::ESTADO_COMPLETADO
                        ),
                    
                    Tables\Actions\Action::make('iniciar')
                        ->label('Iniciar')
                        ->icon('heroicon-o-play')
                        ->color('warning')
                        ->visible(fn (Mantenimiento $record) => 
                            $record->estado === Mantenimiento::ESTADO_PROGRAMADO
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Iniciar Mantenimiento')
                        ->modalDescription('El camión pasará a estado "En Taller"')
                        ->action(function (Mantenimiento $record): void {
                            if ($record->iniciar()) {
                                Notification::make()
                                    ->title('Mantenimiento iniciado')
                                    ->success()
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('completar')
                        ->label('Completar')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (Mantenimiento $record) => 
                            in_array($record->estado, [
                                Mantenimiento::ESTADO_PROGRAMADO,
                                Mantenimiento::ESTADO_EN_PROCESO
                            ])
                        )
                        ->form([
                            Forms\Components\TextInput::make('costo')
                                ->label('Costo Total')
                                ->numeric()
                                ->minValue(0)
                                ->prefix('Q.')
                                ->placeholder('0.00'),
                            Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones')
                                ->placeholder('Trabajos realizados, repuestos cambiados, etc.')
                                ->rows(3),
                        ])
                        ->action(function (Mantenimiento $record, array $data): void {
                            if ($record->completar($data['costo'] ?? null, $data['observaciones'] ?? null)) {
                                Notification::make()
                                    ->title('Mantenimiento completado')
                                    ->success()
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('reprogramar')
                        ->label('Reprogramar')
                        ->icon('heroicon-o-calendar')
                        ->color('info')
                        ->visible(fn (Mantenimiento $record) => 
                            $record->estado === Mantenimiento::ESTADO_PROGRAMADO
                        )
                        ->form([
                            Forms\Components\DatePicker::make('nueva_fecha')
                                ->label('Nueva Fecha')
                                ->required()
                                ->minDate(today()),
                        ])
                        ->action(function (Mantenimiento $record, array $data): void {
                            if ($record->reprogramar(Carbon::parse($data['nueva_fecha']))) {
                                Notification::make()
                                    ->title('Mantenimiento reprogramado')
                                    ->success()
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('cancelar')
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->visible(fn (Mantenimiento $record) => 
                            $record->estado !== Mantenimiento::ESTADO_COMPLETADO
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Cancelar Mantenimiento')
                        ->modalDescription('Esta acción no se puede deshacer.')
                        ->action(function (Mantenimiento $record): void {
                            if ($record->cancelar()) {
                                Notification::make()
                                    ->title('Mantenimiento cancelado')
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
                                    Mantenimiento::ESTADO_PROGRAMADO => 'Programado',
                                    Mantenimiento::ESTADO_CANCELADO => 'Cancelado',
                                ])
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Mantenimiento $record) use ($data) {
                                if ($data['estado'] === Mantenimiento::ESTADO_CANCELADO) {
                                    $record->cancelar();
                                } else {
                                    $record->update(['estado' => $data['estado']]);
                                }
                            });
                            
                            Notification::make()
                                ->title('Estados actualizados')
                                ->body("Se actualizaron {$records->count()} mantenimientos")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\BulkAction::make('reprogramar_masivo')
                        ->label('Reprogramar')
                        ->icon('heroicon-o-calendar')
                        ->color('info')
                        ->form([
                            Forms\Components\DatePicker::make('nueva_fecha')
                                ->label('Nueva Fecha')
                                ->required()
                                ->minDate(today()),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Mantenimiento $record) use ($data) {
                                if ($record->estado === Mantenimiento::ESTADO_PROGRAMADO) {
                                    $record->reprogramar(Carbon::parse($data['nueva_fecha']));
                                }
                            });
                            
                            Notification::make()
                                ->title('Mantenimientos reprogramados')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('fecha_programada', 'asc')
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
        $vencidos = static::getModel()::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->where('fecha_programada', '<', Carbon::today())
            ->count();
        
        $proximos = static::getModel()::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->whereBetween('fecha_programada', [
                Carbon::today(),
                Carbon::today()->addDays(7)
            ])
            ->count();
        
        return $vencidos + $proximos ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $vencidos = static::getModel()::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->where('fecha_programada', '<', Carbon::today())
            ->count();
        
        if ($vencidos > 0) {
            return 'danger';
        }
        
        $proximos = static::getModel()::where('estado', Mantenimiento::ESTADO_PROGRAMADO)
            ->whereBetween('fecha_programada', [
                Carbon::today(),
                Carbon::today()->addDays(7)
            ])
            ->count();
        
        return $proximos > 0 ? 'warning' : 'gray';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMantenimientos::route('/'),
            'create' => Pages\CreateMantenimiento::route('/create'),
            'edit' => Pages\EditMantenimiento::route('/{record}/edit'),
        ];
    }
}
