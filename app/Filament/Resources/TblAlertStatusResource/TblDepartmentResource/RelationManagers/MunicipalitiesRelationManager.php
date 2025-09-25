<?php

namespace App\Filament\Resources\TblDepartmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MunicipalitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'tbl_municipalities';

    protected static ?string $title = 'Municipios del Departamento';

    protected static ?string $label = 'Municipio';
    
    protected static ?string $pluralLabel = 'Municipios';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_municipality')
                    ->label('Nombre del Municipio')
                    ->required()
                    ->maxLength(100),

                Forms\Components\Toggle::make('status_municipality')
                    ->label('Estado Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name_municipality')
            ->columns([
                Tables\Columns\TextColumn::make('name_municipality')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tbl_customers_count')
                    ->label('Clientes')
                    ->counts('tbl_customers')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('tbl_routes_count')
                    ->label('Rutas')
                    ->counts('tbl_routes')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('status_municipality')
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
                    ->query(fn (Builder $query): Builder => $query->where('status_municipality', true))
                    ->default(),

                Tables\Filters\Filter::make('with_customers')
                    ->label('Con Clientes')
                    ->query(fn (Builder $query): Builder => $query->has('tbl_customers')),

                Tables\Filters\Filter::make('with_routes')
                    ->label('Con Rutas')
                    ->query(fn (Builder $query): Builder => $query->has('tbl_routes')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Municipio')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_department'] = request()->route('record');
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
                        ->label('Activar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status_municipality' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status_municipality' => false]));
                        }),
                ]),
            ])
            ->defaultSort('name_municipality', 'asc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Primer Municipio')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_department'] = request()->route('record');
                        return $data;
                    }),
            ]);
    }
}