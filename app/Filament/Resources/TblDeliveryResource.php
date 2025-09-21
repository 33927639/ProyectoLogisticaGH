<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblDeliveryResource\Pages;
use App\Filament\Resources\TblDeliveryResource\RelationManagers;
use App\Models\TblDelivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblDeliveryResource extends Resource
{
    protected static ?string $model = TblDelivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // 👇 AGREGA ESTO
    protected static ?string $modelLabel = 'Entrega';        // Singular
    protected static ?string $pluralModelLabel = 'Entregas'; // Plural
    protected static ?string $navigationLabel = 'Entregas';  // Nombre en el menú lateral

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListTblDeliveries::route('/'),
            'create' => Pages\CreateTblDelivery::route('/create'),
            'edit' => Pages\EditTblDelivery::route('/{record}/edit'),
        ];
    }
}
