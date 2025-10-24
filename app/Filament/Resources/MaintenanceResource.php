<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Filament\Resources\MaintenanceResource\RelationManagers;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\MaintenanceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Mantenimiento')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('vehicle_id')
                                    ->label('Vehículo')
                                    ->relationship('vehicle', 'license_plate')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('request_id')
                                    ->label('Solicitud (Opcional)')
                                    ->options(function () {
                                        return \App\Models\MaintenanceRequest::all()->pluck('id_request', 'id_request');
                                    })
                                    ->searchable(),
                                Forms\Components\TextInput::make('type')
                                    ->label('Tipo de Mantenimiento')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\DatePicker::make('maintenance_date')
                                    ->label('Fecha de Mantenimiento')
                                    ->required(),
                                Forms\Components\Toggle::make('approved')
                                    ->label('Aprobado')
                                    ->default(false),
                                Forms\Components\Toggle::make('status')
                                    ->label('Activo')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Vehículo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('approved')
                    ->label('Aprobado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Vehículo')
                    ->relationship('vehicle', 'license_plate'),
                Tables\Filters\TernaryFilter::make('approved')
                    ->label('Aprobado'),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Activo'),
            ])
            ->actions([
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
            'index' => Pages\ListMaintenances::route('/'),
            'create' => Pages\CreateMaintenance::route('/create'),
            'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }
}
