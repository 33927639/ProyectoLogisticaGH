<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblCustomersResource\Pages;
use App\Models\TblCustomer; // <- tu modelo
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TblCustomersResource extends Resource
{
    protected static ?string $model = TblCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Gestión de Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Gestión de Personal';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Cliente')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(120),

                        Forms\Components\TextInput::make('nit')
                            ->label('NIT')
                            ->maxLength(50),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->maxLength(120),
                    ]),

                    Forms\Components\Textarea::make('address')
                        ->label('Dirección')
                        ->rows(2)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('id_municipality')
                        ->label('Municipio (ID)')
                        ->numeric(),

                    Forms\Components\Toggle::make('status')
                        ->label('Activo')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_customer')
                    ->label('ID')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name_with_nit')
                    ->label('Nombre · NIT')
                    ->searchable(['name', 'nit'])
                    ->sortable(['name'])
                    ->weight(FontWeight::Bold)
                    ->description(fn (TblCustomer $record): ?string =>
                    $record->phone ? "Tel: {$record->phone}" : null
                    ),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('short_address')
                    ->label('Dirección'),

                Tables\Columns\TextColumn::make('id_municipality')
                    ->label('Municipio'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->placeholder('Todos'),

                Filter::make('con_email')
                    ->label('Con email')
                    ->query(fn (Builder $q): Builder =>
                    $q->whereNotNull('email')->where('email', '!=', '')
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activar')
                        ->label('Marcar como Activos')
                        ->action(fn (Collection $records) =>
                        $records->each->update(['status' => true])
                        ),

                    Tables\Actions\BulkAction::make('inactivar')
                        ->label('Marcar como Inactivos')
                        ->action(fn (Collection $records) =>
                        $records->each->update(['status' => false])
                        ),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTblCustomers::route('/'),
            'create' => Pages\CreateTblCustomers::route('/create'),
            'edit'   => Pages\EditTblCustomers::route('/{record}/edit'),
        ];
    }
}
