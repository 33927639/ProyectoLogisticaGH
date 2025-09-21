<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblMaintenanceRequestResource\Pages;
use App\Filament\Resources\TblMaintenanceRequestResource\RelationManagers;
use App\Models\TblMaintenanceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblMaintenanceRequestResource extends Resource
{
    protected static ?string $model = TblMaintenanceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Requerimientos de mantenimiento';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Rutas';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Clientes';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_vehicle')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('request_date')
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('approved_by')
                    ->numeric(),
                Forms\Components\Toggle::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_vehicle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
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
            'index' => Pages\ListTblMaintenanceRequests::route('/'),
            'create' => Pages\CreateTblMaintenanceRequest::route('/create'),
            'edit' => Pages\EditTblMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
