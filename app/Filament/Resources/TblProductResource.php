<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblProductResource\Pages;
use App\Filament\Resources\TblProductResource\RelationManagers;
use App\Models\TblProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TblProductResource extends Resource
{
    protected static ?string $model = TblProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(150),

            Forms\Components\TextInput::make('stock')
                ->numeric()
                ->label('Stock')
                ->default(0),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->label('Precio')
                ->prefix('Q')
                ->default(0.00),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('name')->label('Nombre'),
            Tables\Columns\TextColumn::make('stock')->label('Stock'),
            Tables\Columns\TextColumn::make('price')->label('Precio')->money('GTQ'),
            Tables\Columns\IconColumn::make('status')
                ->boolean()
                ->label('Activo')
                ->trueIcon('heroicon-s-check-circle')
                ->falseIcon('heroicon-s-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Creado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Modificado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            //
        ])->actions([
            Tables\Actions\EditAction::make()
                ->label('Editar'),
            Tables\Actions\ViewAction::make()
                ->label('Ver'),
            Tables\Actions\DeleteAction::make()
                ->label('Eliminar')
                ->requiresConfirmation() // Esto activa el modal de confirmación
                ->modalHeading('Eliminar producto') // Título del modal
                ->modalDescription('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.') // Descripción del mensaje
                ->modalSubmitActionLabel('Sí, eliminar') // Texto del botón de confirmación
                ->modalCancelActionLabel('Cancelar'), // Texto del botón de cancelación


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
            'index' => Pages\ListTblProducts::route('/'),
            'create' => Pages\CreateTblProduct::route('/create'),
            'edit' => Pages\EditTblProduct::route('/{record}/edit'),
        ];
    }
}
