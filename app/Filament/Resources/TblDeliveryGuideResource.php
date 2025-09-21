<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblDeliveryGuideResource\Pages;
use App\Filament\Resources\TblDeliveryGuideResource\RelationManagers;
use App\Models\TblDeliveryGuide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblDeliveryGuideResource extends Resource
{
    protected static ?string $model = TblDeliveryGuide::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Guias De Entregas';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Entregas';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Clientes';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_delivery')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('guide_number')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Toggle::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_delivery')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guide_number')
                    ->searchable(),
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
            'index' => Pages\ListTblDeliveryGuides::route('/'),
            'create' => Pages\CreateTblDeliveryGuide::route('/create'),
            'edit' => Pages\EditTblDeliveryGuide::route('/{record}/edit'),
        ];
    }
}
