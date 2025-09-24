<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblKilometerResource\Pages;
use App\Filament\Resources\TblKilometerResource\RelationManagers;
use App\Models\TblKilometer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblKilometerResource extends Resource
{
    protected static ?string $model = TblKilometer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kilometros';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Kilometros';

//    protected static ?int $navigationSort = 1;

//    protected static ?string $navigationGroup = 'Kilometros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_delivery')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_vehicle')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kilometers_traveled')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_alert')
                    ->numeric(),
                Forms\Components\DatePicker::make('record_date')
                    ->required(),
                Forms\Components\Toggle::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_delivery')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_vehicle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kilometers_traveled')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_alert')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('record_date')
                    ->date()
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
            'index' => Pages\ListTblKilometers::route('/'),
            'create' => Pages\CreateTblKilometer::route('/create'),
            'edit' => Pages\EditTblKilometer::route('/{record}/edit'),
        ];
    }
}
