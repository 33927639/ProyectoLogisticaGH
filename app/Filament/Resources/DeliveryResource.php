<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
use App\Models\Route;
use App\Models\DeliveryStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationGroup = 'Operaciones';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Entrega')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('order_id')
                                    ->label('Orden')
                                    ->relationship('order', 'id_order')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "#{$record->id_order} - {$record->customer->customer_name}")
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $order = \App\Models\Order::find($state);
                                            if ($order) {
                                                $set('customer_name', $order->customer->customer_name);
                                                $set('total_amount', $order->total_amount);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Nombre del Cliente')
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('delivery_address')
                                    ->label('Dirección de Entrega')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\DatePicker::make('delivery_date')
                                    ->label('Fecha de Entrega')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Monto Total')
                                    ->numeric()
                                    ->prefix('Q')
                                    ->step(0.01)
                                    ->readOnly(),

                                Forms\Components\Select::make('route_id')
                                    ->label('Ruta')
                                    ->relationship('route', 'id_route')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->origin->name_municipality} → {$record->destination->name_municipality}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('status_id')
                                    ->label('Estado de Entrega')
                                    ->options(\App\Models\DeliveryStatus::where('status', true)->pluck('name_status', 'id_status'))
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Toggle::make('status')
                                    ->label('Activa')
                                    ->default(true),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('Productos de la Entrega')
                    ->schema([
                        Forms\Components\Repeater::make('deliveryProducts')
                            ->label('Productos')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Producto')
                                            ->options(\App\Models\Product::where('status', true)->pluck('name', 'id_product'))
                                            ->required()
                                            ->searchable()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                if ($state) {
                                                    $product = \App\Models\Product::find($state);
                                                    if ($product) {
                                                        $set('unit_price', $product->unit_price);
                                                        // Recalcular subtotal si ya hay cantidad
                                                        $quantity = $get('quantity') ?? 1;
                                                        if ($quantity) {
                                                            $set('subtotal', $quantity * $product->unit_price);
                                                        }
                                                    }
                                                }
                                            }),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $quantity = $state;
                                                $unitPrice = $get('unit_price');
                                                if ($quantity && $unitPrice) {
                                                    $set('subtotal', $quantity * $unitPrice);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Precio Unitario')
                                            ->numeric()
                                            ->prefix('Q')
                                            ->step(0.01)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $unitPrice = $state;
                                                $quantity = $get('quantity');
                                                if ($quantity && $unitPrice) {
                                                    $set('subtotal', $quantity * $unitPrice);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->prefix('Q')
                                            ->step(0.01)
                                            ->disabled()
                                            ->dehydrated(false) // No enviar este valor al servidor
                                            ->helperText('Calculado automáticamente: Cantidad × Precio'),
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Producto')
                            ->collapsible()
                            ->reorderableWithButtons()
                            ->deletable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_delivery')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.id_order')
                    ->label('Orden #')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : 'Sin orden'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('Sin cliente'),

                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('route.origin.name_municipality')
                    ->label('Origen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('route.destination.name_municipality')
                    ->label('Destino')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('GTQ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery_status.name_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Ruta' => 'info',
                        'Entregado' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_products')
                    ->label('Total Productos')
                    ->getStateUsing(function ($record) {
                        return \DB::table('delivery_products')
                            ->where('delivery_id', $record->id_delivery)
                            ->sum('subtotal');
                    })
                    ->money('GTQ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('# Productos')
                    ->getStateUsing(function ($record) {
                        return \DB::table('delivery_products')
                            ->where('delivery_id', $record->id_delivery)
                            ->count();
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Estado')
                    ->options(\App\Models\DeliveryStatus::where('status', true)->pluck('name_status', 'id_status')),
                Tables\Filters\Filter::make('delivery_date')
                    ->form([
                        Forms\Components\DatePicker::make('delivery_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('delivery_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['delivery_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '>=', $date),
                            )
                            ->when(
                                $data['delivery_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '<=', $date),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Activa'),
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'view' => Pages\ViewDelivery::route('/{record}'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
