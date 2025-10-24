<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Average;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Vehículos';
    
    protected static ?string $modelLabel = 'Vehículo';
    
    protected static ?string $pluralModelLabel = 'Vehículos';
    
    protected static ?string $navigationGroup = 'Operaciones';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Vehículo')
                    ->schema([
                        Forms\Components\TextInput::make('license_plate')
                            ->label('Placa')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('P-123ABC'),
                            
                        Forms\Components\TextInput::make('capacity')
                            ->label('Capacidad (kg)')
                            ->required()
                            ->numeric()
                            ->suffix(' kg')
                            ->minValue(1),
                            
                        Forms\Components\Toggle::make('available')
                            ->label('Disponible')
                            ->default(true),
                            
                        Forms\Components\Toggle::make('status')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Kilometraje')
                    ->schema([
                        Forms\Components\TextInput::make('total_kilometers')
                            ->label('Kilometraje Total')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->step(0.01)
                            ->suffix(' km')
                            ->helperText('Kilometraje acumulado del vehículo'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-identification'),
                    
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->formatStateUsing(fn ($state) => $state . ' kg')
                    ->sortable()
                    ->icon('heroicon-o-scale'),
                    
                Tables\Columns\TextColumn::make('total_kilometers')
                    ->label('Kilometraje Total')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' km')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 10000 => 'danger',
                        $state >= 9500 => 'warning',
                        $state >= 9000 => 'info',
                        default => 'success',
                    })
                    ->weight('bold')
                    ->icon('heroicon-o-chart-bar')
                    ->summarize(Sum::make()->label('Total Flota (km)')),
                    
                Tables\Columns\TextColumn::make('maintenance_status')
                    ->label('Estado Mantenimiento')
                    ->getStateUsing(function ($record) {
                        $km = $record->total_kilometers;
                        if ($km >= 10000) return 'URGENTE';
                        if ($km >= 9500) return 'Próximo';
                        if ($km >= 9000) return 'Se Acerca';
                        return 'Bueno';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'URGENTE' => 'danger',
                        'Próximo' => 'warning',
                        'Se Acerca' => 'info',
                        'Bueno' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'URGENTE' => 'heroicon-o-exclamation-triangle',
                        'Próximo' => 'heroicon-o-clock',
                        'Se Acerca' => 'heroicon-o-information-circle',
                        'Bueno' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                    
                Tables\Columns\TextColumn::make('km_until_maintenance')
                    ->label('Km Hasta Mantenimiento')
                    ->getStateUsing(function ($record) {
                        $nextMaintenance = ceil($record->total_kilometers / 10000) * 10000;
                        $remaining = max(0, $nextMaintenance - $record->total_kilometers);
                        return number_format($remaining, 2) . ' km';
                    })
                    ->color(function ($record): string {
                        $nextMaintenance = ceil($record->total_kilometers / 10000) * 10000;
                        $remaining = max(0, $nextMaintenance - $record->total_kilometers);
                        return match (true) {
                            $remaining <= 0 => 'danger',
                            $remaining <= 500 => 'warning',
                            $remaining <= 1000 => 'info',
                            default => 'success',
                        };
                    }),
                    
                Tables\Columns\IconColumn::make('available')
                    ->label('Disponible')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('last_kilometer_entry')
                    ->label('Último Registro')
                    ->getStateUsing(function ($record) {
                        $lastEntry = $record->kilometers()->latest()->first();
                        return $lastEntry ? $lastEntry->created_at->format('d/m/Y') : 'Sin registros';
                    })
                    ->icon('heroicon-o-calendar-days'),
            ])
            ->filters([
                Filter::make('needs_maintenance')
                    ->label('Necesita Mantenimiento')
                    ->query(fn (Builder $query): Builder => $query->where('total_kilometers', '>=', 9500))
                    ->toggle(),
                    
                Filter::make('high_mileage')
                    ->label('Alto Kilometraje (>9000 km)')
                    ->query(fn (Builder $query): Builder => $query->where('total_kilometers', '>=', 9000))
                    ->toggle(),
                    
                Filter::make('active_only')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->toggle()
                    ->default(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('add_kilometers')
                    ->label('Agregar Km')
                    ->icon('heroicon-o-plus-circle')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('kilometers')
                            ->label('Kilómetros a Agregar')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->suffix(' km')
                            ->minValue(0.01),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->placeholder('Ej: Mantenimiento, traslado, etc.')
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        $record->addKilometers(
                            (float) $data['kilometers'],
                            $data['notes'] ?? 'Agregado manualmente',
                            auth()->user()->id_user ?? null
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Kilometraje Agregado')
                            ->body("Se agregaron {$data['kilometers']} km al vehículo {$record->license_plate}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('total_kilometers', 'desc')
            ->poll('60s') // Actualizar cada minuto
            ->striped();
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
