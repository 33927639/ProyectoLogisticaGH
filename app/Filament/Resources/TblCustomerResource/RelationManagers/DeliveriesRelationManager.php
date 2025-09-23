<?php

namespace App\Filament\Resources\TblCustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TblRoute;

class DeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'Entregas del Cliente';

    protected static ?string $label = 'Entrega';
    
    protected static ?string $pluralLabel = 'Entregas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('delivery_date')
                    ->label('Fecha de Entrega')
                    ->required()
                    ->default(now()),
                    
                Forms\Components\Select::make('id_route')
                    ->label('Ruta')
                    ->options(function () {
                        return TblRoute::with(['origin_municipality.tbl_department', 'destination_municipality.tbl_department'])
                            ->get()
                            ->mapWithKeys(function ($route) {
                                $origin = $route->origin_municipality?->name_municipality . ' (' . $route->origin_municipality?->tbl_department?->name_department . ')';
                                $destination = $route->destination_municipality?->name_municipality . ' (' . $route->destination_municipality?->tbl_department?->name_department . ')';
                                $label = $origin . ' → ' . $destination . ' (' . $route->distance_km . ' km)';
                                return [$route->id_route => $label];
                            })
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
                    
                Forms\Components\Toggle::make('status')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('delivery_date')
            ->columns([
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tbl_route.origin_municipality.name_municipality')
                    ->label('Origen')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tbl_route.destination_municipality.name_municipality')
                    ->label('Destino')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tbl_route.distance_km')
                    ->label('Distancia (KM)')
                    ->numeric()
                    ->suffix(' km')
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
                    ->query(fn (Builder $query): Builder => $query->whereMonth('delivery_date', now()->month))
                    ->toggle(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva Entrega')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_customer'] = request()->route('record');
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
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
            ->defaultSort('delivery_date', 'desc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Primera Entrega')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_customer'] = request()->route('record');
                        return $data;
                    }),
            ]);
    }
}