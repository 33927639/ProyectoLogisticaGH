<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblDeliveryAssignmentResource\Pages;
use App\Filament\Resources\TblDeliveryAssignmentResource\RelationManagers;
use App\Models\TblDeliveryAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblDeliveryAssignmentResource extends Resource
{
    protected static ?string $model = TblDeliveryAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Asignación de entrega';

    protected static ?string $modelLabel = 'Asignación de entrega';

    protected static ?string $pluralModelLabel = 'Asignaciones de entrega';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Operaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_delivery')
                    ->label('ID de entrega')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_vehicle')
                    ->label('ID de vehículo')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_driver')
                    ->label('ID de conductor')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('assignment_date')
                    ->label('Fecha de entrega')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_delivery')
                    ->label('ID de entrega')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_vehicle')
                    ->label('ID de vehículo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_driver')
                    ->label('ID de conductor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignment_date')
                    ->label('Fecha de entrega')
                    ->date()
                    ->sortable(),
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
            'index' => Pages\ListTblDeliveryAssignments::route('/'),
            'create' => Pages\CreateTblDeliveryAssignment::route('/create'),
            'edit' => Pages\EditTblDeliveryAssignment::route('/{record}/edit'),
        ];
    }
}
