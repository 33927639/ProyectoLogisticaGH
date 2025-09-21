<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblCustomerResource\Pages;
use App\Filament\Resources\TblCustomerResource\RelationManagers;
use App\Models\TblCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblCustomerResource extends Resource
{
    protected static ?string $model = TblCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Crear Clientes';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nombre Cliente')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('nit')->label('NIT')
                    ->maxLength(20),
                Forms\Components\TextInput::make('phone')->label('No. Teléfono')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')->label('Correo Electrónico')
                    ->email()
                    ->maxLength(100),
                Forms\Components\Textarea::make('address')->label('Dirección')
                    ->columnSpanFull(),
                Forms\Components\Select::make('id_municipality')
                    ->label('Municipio')
                    ->relationship('municipality', 'name_municipality') // 👈 aquí usa name_municipality
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Toggle::make('status')->label('Estado')
                    ->default(true)
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-s-check'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nit')->label('NIT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('No. Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Correo Electrónico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('municipality.name_municipality')->label('Municipio')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')->label('Estado')
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
                Tables\Actions\EditAction::make()
                ->label('Editar'),
                Tables\Actions\ViewAction::make()
                ->label('Ver'),

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
            'index' => Pages\ListTblCustomers::route('/'),
            'create' => Pages\CreateTblCustomer::route('/create'),
            'edit' => Pages\EditTblCustomer::route('/{record}/edit'),
        ];
    }
}
