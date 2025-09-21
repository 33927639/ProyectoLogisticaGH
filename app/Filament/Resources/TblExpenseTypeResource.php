<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblExpenseTypeResource\Pages;
use App\Filament\Resources\TblExpenseTypeResource\RelationManagers;
use App\Models\TblExpenseType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblExpenseTypeResource extends Resource
{
    protected static ?string $model = TblExpenseType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Tipos de gastos';

    protected static ?string $modelLabel = '';

//    protected static ?string $pluralModelLabel = 'Rutas';

//    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Tipos de gastos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    
                    ->required()
                    ->maxLength(100),
                Forms\Components\Toggle::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
            'index' => Pages\ListTblExpenseTypes::route('/'),
            'create' => Pages\CreateTblExpenseType::route('/create'),
            'edit' => Pages\EditTblExpenseType::route('/{record}/edit'),
        ];
    }
}
