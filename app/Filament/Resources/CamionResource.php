<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CamionResource\Pages;
use App\Filament\Resources\CamionResource\RelationManagers;
use App\Filament\Resources\MantenimientoResource;
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
use Illuminate\Support\Facades\Auth;

class CamionResource extends Resource
{
    protected static ?string $model = Camion::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Camiones';
    
    protected static ?string $modelLabel = 'Camión';
    
    protected static ?string $pluralModelLabel = 'Camiones';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Gestión de Flota';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Vehículo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('placa')
                                    ->label('Placa')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('Ej: ABC-123')
                                    ->suffixIcon('heroicon-m-identification'),
                                Forms\Components\TextInput::make('numero_motor')
                                    ->label('Número de Motor')
                                    ->maxLength(50)
                                    ->placeholder('Opcional')
                                    ->suffixIcon('heroicon-m-cog-6-tooth'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('marca')
                                    ->label('Marca')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Ej: Volvo'),
                                Forms\Components\TextInput::make('modelo')
                                    ->label('Modelo')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Ej: FH16'),
                                Forms\Components\TextInput::make('year')
                                    ->label('Año')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 1)
                                    ->placeholder(date('Y')),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Información Operativa')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('kilometraje_actual')
                                    ->label('Kilometraje Actual')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('km')
                                    ->default(0),
                                Forms\Components\TextInput::make('intervalo_mantenimiento_km')
                                    ->label('Intervalo de Mantenimiento')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(50000)
                                    ->suffix('km')
                                    ->default(5000)
                                    ->helperText('Cada cuántos kilómetros necesita mantenimiento'),
                            ]),
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->options([
                                Camion::ESTADO_ACTIVO => 'Activo',
                                Camion::ESTADO_EN_TALLER => 'En Taller',
                                Camion::ESTADO_INACTIVO => 'Inactivo',
                            ])
                            ->default(Camion::ESTADO_ACTIVO)
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Placa copiada'),
                
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Vehículo')
                    ->searchable(['marca', 'modelo'])
                    ->description(fn (Camion $record): string => "Año: {$record->year}")
                    ->weight(FontWeight::Medium),
                
                Tables\Columns\TextColumn::make('kilometraje_actual')
                    ->label('Kilometraje')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' km')
                    ->sortable()
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('kilometros_hasta_mantenimiento')
                    ->label('Próximo Mant.')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' km')
                    ->color(fn (Camion $record) => match (true) {
                        $record->necesita_mantenimiento => 'danger',
                        $record->kilometros_hasta_mantenimiento <= 1000 => 'warning',
                        default => 'success',
                    })
                    ->weight(fn (Camion $record) => $record->necesita_mantenimiento ? FontWeight::Bold : FontWeight::Medium)
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'En Taller' => 'warning',
                        'Inactivo' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('viaje_actual')
                    ->label('En Viaje')
                    ->boolean()
                    ->trueIcon('heroicon-o-truck')
                    ->falseIcon('heroicon-o-home')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->tooltip(fn (Camion $record) => $record->viaje_actual ? 'En viaje actualmente' : 'Disponible'),
                
                Tables\Columns\TextColumn::make('viajes_count')
                    ->label('Total Viajes')
                    ->counts('viajes')
                    ->alignCenter()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        Camion::ESTADO_ACTIVO => 'Activo',
                        Camion::ESTADO_EN_TALLER => 'En Taller',
                        Camion::ESTADO_INACTIVO => 'Inactivo',
                    ])
                    ->placeholder('Todos los estados'),
                
                Filter::make('necesita_mantenimiento')
                    ->label('Necesita Mantenimiento')
                    ->query(fn (Builder $query): Builder => $query->where(function (Builder $query) {
                        $query->whereRaw('kilometraje_actual >= (
                            SELECT COALESCE(MAX(kilometraje_actual), 0) + intervalo_mantenimiento_km
                            FROM mantemientos 
                            WHERE camiones.id = mantemientos.camion_id 
                            AND estado = "Completado"
                        )');
                    }))
                    ->toggle(),
                
                Filter::make('disponibles')
                    ->label('Disponibles para Viaje')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('estado', Camion::ESTADO_ACTIVO)
                              ->whereDoesntHave('viajes', fn (Builder $query) => 
                                  $query->where('estado', 'En Curso')
                              )
                    )
                    ->toggle(),
                
                SelectFilter::make('marca')
                    ->label('Marca')
                    ->options(fn (): array => 
                        Camion::query()
                            ->distinct()
                            ->pluck('marca', 'marca')
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Todas las marcas'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    
                    Tables\Actions\Action::make('actualizar_kilometraje')
                        ->label('Actualizar Km')
                        ->icon('heroicon-o-calculator')
                        ->color('info')
                        ->form([
                            Forms\Components\TextInput::make('nuevo_kilometraje')
                                ->label('Nuevo Kilometraje')
                                ->required()
                                ->numeric()
                                ->minValue(fn (Camion $record) => $record->kilometraje_actual)
                                ->suffix('km')
                                ->helperText(fn (Camion $record) => "Kilometraje actual: {$record->kilometraje_actual} km"),
                        ])
                        ->action(function (Camion $record, array $data): void {
                            $record->actualizarKilometraje($data['nuevo_kilometraje']);
                            
                            Notification::make()
                                ->title('Kilometraje actualizado')
                                ->body("Nuevo kilometraje: {$data['nuevo_kilometraje']} km")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('mantenimiento')
                        ->label('Programar Mant.')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('warning')
                        ->url(fn (Camion $record): string => MantenimientoResource::getUrl('create', ['camion_id' => $record->id]))
                        ->visible(fn (Camion $record) => $record->estado !== Camion::ESTADO_INACTIVO),
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
                                    Camion::ESTADO_ACTIVO => 'Activo',
                                    Camion::ESTADO_EN_TALLER => 'En Taller',
                                    Camion::ESTADO_INACTIVO => 'Inactivo',
                                ])
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Camion $record) use ($data) {
                                $record->update(['estado' => $data['estado']]);
                            });
                            
                            Notification::make()
                                ->title('Estados actualizados')
                                ->body("Se actualizaron {$records->count()} camiones")
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
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        
        if ($count > 50) {
            return 'success';
        } elseif ($count > 20) {
            return 'warning';
        }
        
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCamions::route('/'),
            'create' => Pages\CreateCamion::route('/create'),
            'edit' => Pages\EditCamion::route('/{record}/edit'),
        ];
    }
}
