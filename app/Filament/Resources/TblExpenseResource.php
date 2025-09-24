<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblExpenseResource\Pages;
use App\Filament\Resources\TblExpenseResource\RelationManagers;
use App\Models\TblExpense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblExpenseResource extends Resource
{
    protected static ?string $model = TblExpense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListTblExpenses::route('/'),
            'create' => Pages\CreateTblExpense::route('/create'),
            'edit' => Pages\EditTblExpense::route('/{record}/edit'),
        ];
    }
}
