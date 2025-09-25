<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblCustomerResource\Pages;
use App\Models\TblCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TblCustomerResource extends Resource
{
    protected static ?string $model = TblCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nombre')->required()->maxLength(150),
            Forms\Components\TextInput::make('nit')->maxLength(20),
            Forms\Components\TextInput::make('phone')->tel()->maxLength(20),
            Forms\Components\TextInput::make('email')->email()->maxLength(100),
            Forms\Components\Textarea::make('address')->label('Dirección')->columnSpanFull(),
            Forms\Components\TextInput::make('id_municipality')->numeric()->label('Municipio (ID)'),
            Forms\Components\Toggle::make('status')->label('Activo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('nit')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('id_municipality')->label('Municipio')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('status')->label('Activo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // agrega filtros si los necesitas
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTblCustomers::route('/'),
            'create' => Pages\CreateTblCustomer::route('/create'),
            'edit'   => Pages\EditTblCustomer::route('/{record}/edit'),
        ];
    }
}
