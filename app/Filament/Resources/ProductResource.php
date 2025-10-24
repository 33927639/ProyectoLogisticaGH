<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Producto')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre del Producto')
                                    ->required()
                                    ->maxLength(150),
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->unique()
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Precio Unitario')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('Q'),
                                Forms\Components\TextInput::make('weight_kg')
                                    ->label('Peso (kg)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('kg'),
                                Forms\Components\TextInput::make('volume_m3')
                                    ->label('Volumen (m³)')
                                    ->numeric()
                                    ->step(0.001)
                                    ->suffix('m³'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio')
                    ->money('GTQ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight_kg')
                    ->label('Peso')
                    ->numeric(2)
                    ->suffix(' kg'),
                Tables\Columns\TextColumn::make('volume_m3')
                    ->label('Volumen')
                    ->numeric(3)
                    ->suffix(' m³'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
