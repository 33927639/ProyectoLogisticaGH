<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblDepartmentResource\Pages;
use App\Filament\Resources\TblDepartmentResource\RelationManagers;
use App\Models\TblDepartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblDepartmentResource extends Resource
{
    protected static ?string $model = TblDepartment::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Departamentos';

    protected static ?string $modelLabel = 'Departamento';

    protected static ?string $pluralModelLabel = 'Lista de Departamentos';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Departamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Departamento')
                    ->schema([
                        Forms\Components\TextInput::make('name_department')
                            ->label('Nombre del Departamento')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Toggle::make('status_department')
                            ->label('Estado Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_department')
                    ->label('Nombre del Departamento')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tbl_municipalities_count')
                    ->label('Municipios')
                    ->counts('tbl_municipalities')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\IconColumn::make('status_department')
                    ->label('Estado')
                    ->boolean()
                    ->sortable(),
                    
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
                Tables\Filters\Filter::make('active')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status_department', true))
                    ->default(),
                    
                Tables\Filters\Filter::make('with_municipalities')
                    ->label('Con Municipios')
                    ->query(fn (Builder $query): Builder => $query->has('tbl_municipalities')),
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
                                $record->update(['status_department' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status_department' => false]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name_department', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MunicipalitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTblDepartments::route('/'),
            'create' => Pages\CreateTblDepartment::route('/create'),
            'edit' => Pages\EditTblDepartment::route('/{record}/edit'),
        ];
    }


}
