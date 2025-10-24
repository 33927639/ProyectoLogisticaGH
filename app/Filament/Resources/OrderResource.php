<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Órdenes';
    protected static ?string $modelLabel = 'Orden';
    protected static ?string $pluralModelLabel = 'Órdenes';
    protected static ?string $navigationGroup = 'Clientes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Orden')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->label('Cliente')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre del Cliente')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Dirección')
                                            ->rows(3),
                                    ]),

                                Forms\Components\DateTimePicker::make('order_date')
                                    ->label('Fecha de Orden')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        'PENDING' => 'Pendiente',
                                        'CONFIRMED' => 'Confirmado',
                                        'CANCELLED' => 'Cancelado',
                                        'DELIVERED' => 'Entregado',
                                    ])
                                    ->default('PENDING')
                                    ->required(),

                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('Q')
                                    ->step(0.01)
                                    ->readOnly()
                                    ->live()
                                    ->afterStateHydrated(function ($component, $state, callable $get) {
                                        // Calcular total dinámicamente basado en productos
                                        $products = $get('products') ?? [];
                                        $total = 0;
                                        foreach ($products as $product) {
                                            $total += ($product['subtotal'] ?? 0);
                                        }
                                        $component->state($total);
                                    }),
                            ]),
                    ]),

                Forms\Components\Section::make('Productos')
                    ->schema([
                        Forms\Components\Repeater::make('products')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Producto')
                                            ->relationship('product', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                if ($state) {
                                                    $product = \App\Models\Product::find($state);
                                                    if ($product) {
                                                        $set('product_name', $product->name);
                                                        $set('product_description', $product->description);
                                                        $set('unit_price', $product->unit_price);
                                                        // Calcular subtotal si ya hay cantidad
                                                        $quantity = $get('quantity') ?? 1;
                                                        $set('subtotal', $quantity * $product->unit_price);
                                                    }
                                                }
                                            })
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->step(0.01)
                                            ->default(1)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => 
                                                $set('subtotal', ($state ?? 0) * ($get('unit_price') ?? 0))
                                            ),

                                        Forms\Components\TextInput::make('unit')
                                            ->label('Unidad')
                                            ->default('unidad')
                                            ->required(),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Precio Unitario')
                                            ->numeric()
                                            ->prefix('Q')
                                            ->step(0.01)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => 
                                                $set('subtotal', ($get('quantity') ?? 0) * ($state ?? 0))
                                            ),

                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->prefix('Q')
                                            ->step(0.01)
                                            ->readOnly()
                                            ->dehydrated(),

                                        Forms\Components\Hidden::make('product_name'),
                                        
                                        Forms\Components\Textarea::make('product_description')
                                            ->label('Descripción')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->readOnly(),
                                    ]),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Producto')
                            ->collapsible()
                            ->cloneable()
                            ->reorderableWithButtons(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_order')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->label('Fecha')
                    ->date('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('GTQ')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PENDING' => 'Pendiente',
                        'CONFIRMED' => 'Confirmado',
                        'CANCELLED' => 'Cancelado',
                        'DELIVERED' => 'Entregado',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'PENDING',
                        'info' => 'CONFIRMED',
                        'danger' => 'CANCELLED',
                        'success' => 'DELIVERED',
                    ]),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Productos')
                    ->counts('products')
                    ->badge(),

                Tables\Columns\TextColumn::make('deliveries_count')
                    ->label('Entregas')
                    ->counts('deliveries')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'PENDING' => 'Pendiente',
                        'CONFIRMED' => 'Confirmado',
                        'CANCELLED' => 'Cancelado',
                        'DELIVERED' => 'Entregado',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('create_delivery')
                    ->label('Crear Entrega')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === 'CONFIRMED')
                    ->url(fn (Order $record): string => route('filament.admin.resources.deliveries.create', ['order_id' => $record->id_order])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Orden')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('id_order')
                                    ->label('ID de Orden'),
                                Infolists\Components\TextEntry::make('customer.name')
                                    ->label('Cliente'),
                                Infolists\Components\TextEntry::make('order_date')
                                    ->label('Fecha de Orden')
                                    ->dateTime('d/m/Y H:i'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'PENDING' => 'Pendiente',
                                        'CONFIRMED' => 'Confirmado',
                                        'CANCELLED' => 'Cancelado',
                                        'DELIVERED' => 'Entregado',
                                        default => $state,
                                    })
                                    ->colors([
                                        'warning' => 'PENDING',
                                        'info' => 'CONFIRMED',
                                        'danger' => 'CANCELLED',
                                        'success' => 'DELIVERED',
                                    ]),
                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label('Total')
                                    ->money('GTQ')
                                    ->weight(FontWeight::Bold),
                            ]),
                    ]),

                Infolists\Components\Section::make('Productos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('products')
                            ->schema([
                                Infolists\Components\Grid::make(5)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('product.name')
                                            ->label('Producto')
                                            ->placeholder(fn ($record) => $record->product_name ?? 'Producto personalizado'),
                                        Infolists\Components\TextEntry::make('quantity')
                                            ->label('Cantidad')
                                            ->suffix(fn ($record) => ' ' . $record->unit),
                                        Infolists\Components\TextEntry::make('unit_price')
                                            ->label('Precio Unit.')
                                            ->money('GTQ'),
                                        Infolists\Components\TextEntry::make('subtotal')
                                            ->label('Subtotal')
                                            ->money('GTQ')
                                            ->weight(FontWeight::Bold),
                                        Infolists\Components\TextEntry::make('product_description')
                                            ->label('Descripción')
                                            ->placeholder('Sin descripción'),
                                    ]),
                            ])
                            ->columns(1),
                    ]),

                Infolists\Components\Section::make('Entregas Relacionadas')
                    ->schema([
                        Infolists\Components\TextEntry::make('deliveries_count')
                            ->label('Total de Entregas')
                            ->formatStateUsing(fn ($record) => $record->deliveries->count()),
                    ])
                    ->visible(fn ($record) => $record->deliveries->count() > 0),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
