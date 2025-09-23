<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblMunicipalityResource\Pages;
use App\Filament\Resources\TblMunicipalityResource\RelationManagers;
use App\Models\TblMunicipality;
use App\Models\TblDepartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblMunicipalityResource extends Resource
{
    protected static ?string $model = TblMunicipality::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Municipios';

    protected static ?string $modelLabel = 'Municipio';

    protected static ?string $pluralModelLabel = 'Lista de Municipios';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Departamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Municipio')
                    ->schema([
                        Forms\Components\TextInput::make('name_municipality')
                            ->label('Nombre del Municipio')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),

                        Forms\Components\Select::make('id_department')
                            ->label('Departamento')
                            ->options(TblDepartment::where('status_department', true)->pluck('name_department', 'id_department'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('status_municipality')
                            ->label('Estado Activo')
                            ->default(true)
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_municipality')
                    ->label('Municipio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tbl_department.name_department')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('status_municipality')
                    ->label('Estado')
                    ->boolean()
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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_department')
                    ->label('Departamento')
                    ->relationship('tbl_department', 'name_department')
                    ->searchable()
                    ->preload(),

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
                        ->label('Activar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status_municipality' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status_municipality' => false]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name_municipality', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CustomersRelationManager::class,
            RelationManagers\RoutesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTblMunicipalities::route('/'),
            'create' => Pages\CreateTblMunicipality::route('/create'),
            'edit' => Pages\EditTblMunicipality::route('/{record}/edit'),
        ];
    }
}
