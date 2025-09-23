<?php

namespace App\Filament\Resources\TblCustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TblDelivery;

class SalesRelationManager extends RelationManager
{
    // Nombre de la relación definida en el modelo TblCustomer
    protected static string $relationship = 'income';

    protected static ?string $title = 'Historial de Ventas/Ingresos';

    protected static ?string $label = 'Venta';
    
    protected static ?string $pluralLabel = 'Ventas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('Q')
                    ->step(0.01),
                    
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull(),
                    
                Forms\Components\DatePicker::make('income_date')
                    ->label('Fecha de Venta')
                    ->required()
                    ->default(now()),
                    
                Forms\Components\Select::make('id_delivery')
                    ->label('Entrega Asociada')
                    ->options(function () {
                        $customerId = request()->route('record'); // Obtener el ID del cliente actual
                        return TblDelivery::where('id_customer', $customerId)
                            ->with(['tbl_route.origin_municipality', 'tbl_route.destination_municipality'])
                            ->get()
                            ->mapWithKeys(function ($delivery) {
                                $origin = $delivery->tbl_route?->origin_municipality?->name_municipality ?? 'N/A';
                                $destination = $delivery->tbl_route?->destination_municipality?->name_municipality ?? 'N/A';
                                $date = $delivery->delivery_date->format('d/m/Y');
                                $label = "#{$delivery->id_delivery} - ({$origin} → {$destination}) - {$date}";
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('income_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('GTQ')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('GTQ')
                            ->label('Total Ventas'),
                    ]),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('tbl_delivery.id_delivery')
                    ->label('No. Entrega')
                    ->prefix('#')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->default(),
                    
                Tables\Filters\Filter::make('this_month')
                    ->label('Este Mes')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('income_date', now()->month))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('this_year')
                    ->label('Este Año')
                    ->query(fn (Builder $query): Builder => $query->whereYear('income_date', now()->year))
                    ->toggle(),
                    
                Tables\Filters\SelectFilter::make('id_delivery')
                    ->label('Por Entrega')
                    ->relationship('tbl_delivery', 'id_delivery')
                    ->searchable(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Venta')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_customer'] = request()->route('record');
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('income_date', 'desc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Primera Venta')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_customer'] = request()->route('record');
                        return $data;
                    }),
            ]);
    }
}
