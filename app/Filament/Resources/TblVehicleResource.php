<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblVehicleResource\Pages;
use App\Models\TblVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class TblVehicleResource extends Resource
{
    protected static ?string $model = TblVehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Vehículos';
    protected static ?string $pluralLabel = 'Vehículos';
    protected static ?string $modelLabel = 'Vehículo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('license_plate')
                    ->label('Placa')
                    ->required()
                    ->maxLength(20),

                TextInput::make('capacity')
                    ->label('Capacidad')
                    ->numeric()
                    ->required(),

                TextInput::make('plates')
                    ->label('Número de placas')
                    ->maxLength(50),

                Toggle::make('available')
                    ->label('Disponible')
                    ->default(true),

                Toggle::make('status')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_vehicle')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad'),

                Tables\Columns\TextColumn::make('plates')
                    ->label('Número de placas'),

                Tables\Columns\BooleanColumn::make('available')
                    ->label('Disponible'),

                Tables\Columns\BooleanColumn::make('status')
                    ->label('Activo'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTblVehicles::route('/'),
            'create' => Pages\CreateTblVehicle::route('/create'),
            'edit' => Pages\EditTblVehicle::route('/{record}/edit'),
        ];
    }
}
