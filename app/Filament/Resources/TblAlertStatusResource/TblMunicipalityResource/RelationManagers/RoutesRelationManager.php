<?php

namespace App\Filament\Resources\TblMunicipalityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TblMunicipality;
use App\Models\TblDepartment;

class RoutesRelationManager extends RelationManager
{
    protected static string $relationship = 'tbl_routes';

    protected static ?string $title = 'Rutas desde este Municipio';

    protected static ?string $label = 'Ruta';
    
    protected static ?string $pluralLabel = 'Rutas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Destino de la Ruta')
                    ->schema([
                        Forms\Components\Select::make('destination_department')
                            ->label('Departamento de Destino')
                            ->options(TblDepartment::all()->pluck('name_department', 'id_department'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('id_destination', null)),
                        
                        Forms\Components\Select::make('id_destination')
                            ->label('Municipio de Destino')
                            ->options(function (Forms\Get $get): array {
                                $departmentId = $get('destination_department');
                                if (!$departmentId) {
                                    return [];
                                }
                                $currentMunicipalityId = request()->route('record');
                                return TblMunicipality::where('id_department', $departmentId)
                                    ->where('id_municipality', '!=', $currentMunicipalityId) // Excluir el municipio actual
                                    ->pluck('name_municipality', 'id_municipality')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                    ])->columns(2),
                
                Forms\Components\TextInput::make('distance_km')
                    ->label('Distancia (KM)')
                    ->required()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0),
                    
                Forms\Components\Toggle::make('status')
                    ->label('Ruta Activa')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('distance_km')
            ->columns([
                Tables\Columns\TextColumn::make('destination_municipality.name_municipality')
                    ->label('Municipio Destino')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('destination_municipality.tbl_department.name_department')
                    ->label('Departamento Destino')
                    ->sortable(),

                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Distancia (KM)')
                    ->numeric()
                    ->sortable()
                    ->suffix(' km'),

                Tables\Columns\TextColumn::make('tbl_deliveries_count')
                    ->label('Entregas')
                    ->counts('tbl_deliveries')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Solo Rutas Activas')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->default(),

                Tables\Filters\SelectFilter::make('destination_department')
                    ->label('Departamento Destino')
                    ->options(TblDepartment::all()->pluck('name_department', 'id_department'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas(
                                'destination_municipality.tbl_department',
                                fn (Builder $query) => $query->where('id_department', $value)
                            )
                        );
                    }),

                Tables\Filters\Filter::make('with_deliveries')
                    ->label('Con Entregas')
                    ->query(fn (Builder $query): Builder => $query->has('tbl_deliveries')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nueva Ruta')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_origin'] = request()->route('record');
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
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar Rutas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar Rutas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => false]));
                        }),
                ]),
            ])
            ->defaultSort('distance_km', 'asc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Primera Ruta')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_origin'] = request()->route('record');
                        return $data;
                    }),
            ]);
    }
}