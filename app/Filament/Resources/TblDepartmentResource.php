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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Departamentos';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Departamentos';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Departamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_department')
                    ->label('Nombre del Departamento')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Toggle::make('status_department')
                    ->label('Estado'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_department')
                    ->label('Nombre del Departamento')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status_department')
                    ->label('Estado')
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTblDepartments::route('/'),
            'create' => Pages\CreateTblDepartment::route('/create'),
            'edit' => Pages\EditTblDepartment::route('/{record}/edit'),
        ];
    }


}
