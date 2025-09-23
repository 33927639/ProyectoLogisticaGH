<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblIncomeResource\Pages;
use App\Filament\Resources\TblIncomeResource\RelationManagers;
use App\Models\TblIncome;
use App\Models\TblDelivery;
use App\Models\TblCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblIncomeResource extends Resource
{
    protected static ?string $model = TblIncome::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Lista de Ventas';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Ingresos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Monto del Ingreso')
                    ->numeric()
                    ->prefix('Q')
                    ->step(0.01)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('income_date')
                    ->label('Fecha de Ingreso')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('id_customer')
                    ->label('Cliente')
                    ->options(TblCustomer::all()->pluck('name', 'id_customer'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('id_delivery')
                    ->label('Pedido/Entrega')
                    ->options(function () {
                        return TblDelivery::with(['tbl_customer', 'tbl_route.origin_municipality', 'tbl_route.destination_municipality'])
                            ->get()
                            ->mapWithKeys(function ($delivery) {
                                $customerName = $delivery->tbl_customer?->name ?? 'Cliente N/A';
                                $origin = $delivery->tbl_route?->origin_municipality?->name_municipality ?? 'N/A';
                                $destination = $delivery->tbl_route?->destination_municipality?->name_municipality ?? 'N/A';
                                $date = $delivery->delivery_date->format('d/m/Y');
                                $label = "#{$delivery->id_delivery} - {$customerName} ({$origin} → {$destination}) - {$date}";
                                return [$delivery->id_delivery => $label];
                            })
                            ->toArray();
                    })
                    ->searchable(),
                Forms\Components\Toggle::make('status')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('GTQ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('income_date')
                    ->label('Fecha de Ingreso')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tbl_customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tbl_delivery.id_delivery')
                    ->label('No. Pedido')
                    ->prefix('#')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
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
            'index' => Pages\ListTblIncomes::route('/'),
            'create' => Pages\CreateTblIncome::route('/create'),
            'edit' => Pages\EditTblIncome::route('/{record}/edit'),
        ];
    }
}
