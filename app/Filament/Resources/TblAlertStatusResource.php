<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblAlertStatusResource\Pages;
use App\Filament\Resources\TblAlertStatusResource\RelationManagers;
use App\Models\TblAlertStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblAlertStatusResource extends Resource
{
    protected static ?string $model = TblAlertStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Alertas';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Lista de Alertas';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Operaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_alert')
                    ->label('Nombre de Alerta')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Textarea::make('description')
                    ->label('Descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('threshold_km')
                    ->label('Km')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_alert')
                    ->searchable(),
                Tables\Columns\TextColumn::make('threshold_km')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTblAlertStatuses::route('/'),
            'create' => Pages\CreateTblAlertStatus::route('/create'),
            'edit' => Pages\EditTblAlertStatus::route('/{record}/edit'),
        ];
    }
}
