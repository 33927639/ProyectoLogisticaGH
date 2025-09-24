<?php

namespace App\Filament\Resources\TblMunicipalityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TblMunicipality;

class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'tbl_customers';

    protected static ?string $title = 'Clientes del Municipio';

    protected static ?string $label = 'Cliente';
    
    protected static ?string $pluralLabel = 'Clientes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del Cliente')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('nit')
                    ->label('NIT')
                    ->maxLength(20),

                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(100),

                Forms\Components\Textarea::make('address')
                    ->label('Dirección')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('status')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nit')
                    ->label('NIT')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Solo Activos')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->default(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Cliente')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_municipality'] = request()->route('record');
                        return $data;
                    }),
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
                        ->label('Activar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => true]));
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => false]));
                        }),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Primer Cliente')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_municipality'] = request()->route('record');
                        return $data;
                    }),
            ]);
    }
}