<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblRouteResource\Pages;
use App\Filament\Resources\TblRouteResource\RelationManagers;
use App\Models\TblRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblRouteResource extends Resource
{
    protected static ?string $model = TblRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Rutas'; //Cambio de nombre en el menu


    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Rutas';


    protected static ?string $navigationGroup = 'Rutas';






// ✅ Estos son los que cambian el título en el body

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_origin')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_destination')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('distance_km')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_origin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_destination')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance_km')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListTblRoutes::route('/'),
            'create' => Pages\CreateTblRoute::route('/create'),
            'edit' => Pages\EditTblRoute::route('/{record}/edit'),
        ];
    }
}

