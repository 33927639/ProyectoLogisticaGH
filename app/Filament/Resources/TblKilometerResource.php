<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblKilometerResource\Pages;
use App\Models\TblKilometer;
use App\Models\Vehicle;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class TblKilometerResource extends Resource
{
    protected static ?string $model = TblKilometer::class;

    protected static ?string $slug = 'kilometers';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    
    protected static ?string $navigationLabel = 'Kilometraje';
    
    protected static ?string $modelLabel = 'Registro de Kilometraje';
    
    protected static ?string $pluralModelLabel = 'Registros de Kilometraje';
    
    protected static ?string $navigationGroup = 'Operaciones';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_vehicle')
                    ->label('Vehículo')
                    ->options(Vehicle::all()->pluck('license_plate', 'id_vehicle'))
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\TextInput::make('kilometers')
                    ->label('Kilómetros Recorridos')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->suffix(' km')
                    ->minValue(0.01)
                    ->maxValue(1000),
                    
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->required()
                    ->default(today())
                    ->maxDate(today()),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Notas/Descripción')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Ej: Entrega #123 - Cliente ABC, Ruta Guatemala-Mixco'),
                    
                Forms\Components\Select::make('id_user')
                    ->label('Registrado por')
                    ->options(User::all()->mapWithKeys(function ($user) {
                        return [$user->id_user => $user->first_name . ' ' . $user->last_name];
                    }))
                    ->default(auth()->user()->id_user ?? null)
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Placa del Vehículo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-truck'),
                    
                Tables\Columns\TextColumn::make('kilometers')
                    ->label('Km Recorridos')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' km')
                    ->sortable()
                    ->color('primary')
                    ->weight('medium'),
                    
                Tables\Columns\TextColumn::make('vehicle.total_kilometers')
                    ->label('Total Acumulado')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' km')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 10000 => 'danger',
                        $state >= 9500 => 'warning',
                        $state >= 9000 => 'info',
                        default => 'success',
                    })
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days'),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->notes;
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('Registrado por')
                    ->formatStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        return 'Sistema';
                    })
                    ->icon('heroicon-o-user'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_vehicle')
                    ->label('Vehículo')
                    ->options(Vehicle::all()->pluck('license_plate', 'id_vehicle'))
                    ->searchable()
                    ->preload(),
                    
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Desde'),
                        DatePicker::make('date_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                    
                Filter::make('high_mileage')
                    ->label('Solo Vehículos Alto Kilometraje')
                    ->query(fn (Builder $query): Builder => $query->whereHas('vehicle', function (Builder $query) {
                        $query->where('total_kilometers', '>=', 9000);
                    }))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Actualizar cada 30 segundos
            ->striped()
            ->paginated([25, 50, 100]);
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
            'index' => Pages\ListTblKilometers::route('/'),
            'create' => Pages\CreateTblKilometer::route('/create'),
            'edit' => Pages\EditTblKilometer::route('/{record}/edit'),
        ];
    }
}
