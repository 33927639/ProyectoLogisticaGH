<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// FORM imports
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;

// TABLE imports
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    // Navegación/labels
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos del producto')
                    ->description('Completa los datos. Los campos con * son obligatorios.')
                    ->schema([
                        Grid::make(2)->schema([

                            TextInput::make('name')
                                ->label('Nombre del producto')
                                ->placeholder('Ej.: Caja de cartón grande')
                                ->required()
                                ->maxLength(150)
                                ->helperText('Usa un nombre que el usuario reconozca.'),

                            TextInput::make('sku')
                                ->label('Código interno (SKU)')
                                ->placeholder('Ej.: CAJ-001')
                                ->required()
                                ->maxLength(30)
                                ->regex('/^[A-Za-z0-9\-]+$/')   // solo letras, números y guiones
                                ->unique(ignoreRecord: true)    // create/update sin chocar
                                ->helperText('Identificador único. Sin espacios.'),

                            TextInput::make('price')
                                ->label('Precio de venta')
                                ->placeholder('Ej.: 500.00')
                                ->prefix('Q')                   // cambia a $, S/, etc.
                                ->numeric()
                                ->step('0.01')
                                ->required()
                                ->minValue(0)
                                ->helperText('Usa punto para decimales (ej.: 199.99).'),

                            TextInput::make('stock')
                                ->label('Stock disponible')
                                ->placeholder('Ej.: 100')
                                ->integer()
                                ->required()
                                ->minValue(0)
                                ->default(0)
                                ->helperText('Cantidad actual en inventario.'),
                        ]),

                        Textarea::make('description')
                            ->label('Descripción (opcional)')
                            ->placeholder('Ej.: Caja reforzada, 50x40x30 cm.')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Detalles útiles para diferenciar variantes.'),

                        DatePicker::make('expires_at')
                            ->label('Fecha de expiración (si aplica)')
                            ->placeholder('Selecciona una fecha')
                            ->native(false)
                            ->rule('after:today')              // válida > hoy si se llena
                            ->helperText('Solo para productos perecederos.'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->stock <= 5 ? 'danger' : 'gray')
                    ->formatStateUsing(fn ($state) => $state <= 5 ? "Bajo: $state" : (string) $state),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
