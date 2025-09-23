<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblRouteResource\Pages;
use App\Filament\Resources\TblRouteResource\RelationManagers;
use App\Models\TblRoute;
use App\Models\TblDepartment;
use App\Models\TblMunicipality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TblRouteResource extends Resource
{
    protected static ?string $model = TblRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Rutas';

    protected static ?string $modelLabel = '';

    protected static ?string $pluralModelLabel = 'Lista de Rutas';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Departamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Origen')
                    ->schema([
                        Forms\Components\Select::make('origin_department')
                            ->label('Departamento de Origen')
                            ->options(TblDepartment::all()->pluck('name_department', 'id_department'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Set $set) => $set('id_origin', null)),
                        
                        Forms\Components\Select::make('id_origin')
                            ->label('Municipio de Origen')
                            ->options(function (Get $get): array {
                                $departmentId = $get('origin_department');
                                if (!$departmentId) {
                                    return [];
                                }
                                return TblMunicipality::where('id_department', $departmentId)
                                    ->pluck('name_municipality', 'id_municipality')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                    ])->columns(2),
                
                Forms\Components\Section::make('Destino')
                    ->schema([
                        Forms\Components\Select::make('destination_department')
                            ->label('Departamento de Destino')
                            ->options(TblDepartment::all()->pluck('name_department', 'id_department'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Set $set) => $set('id_destination', null)),
                        
                        Forms\Components\Select::make('id_destination')
                            ->label('Municipio de Destino')
                            ->options(function (Get $get): array {
                                $departmentId = $get('destination_department');
                                if (!$departmentId) {
                                    return [];
                                }
                                return TblMunicipality::where('id_department', $departmentId)
                                    ->pluck('name_municipality', 'id_municipality')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                    ])->columns(2),
                
                Forms\Components\TextInput::make('distance_km')
                    ->label('Distancia (KM)')
                    ->required()
                    ->numeric()
                    ->step(0.01),
                Forms\Components\Toggle::make('status')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin_municipality.name_municipality')
                    ->label('Municipio Origen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('origin_municipality.tbl_department.name_department')
                    ->label('Departamento Origen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('destination_municipality.name_municipality')
                    ->label('Municipio Destino')
                    ->sortable(),
                Tables\Columns\TextColumn::make('destination_municipality.tbl_department.name_department')
                    ->label('Departamento Destino')
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Distancia (KM)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
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
            'index' => Pages\ListTblRoutes::route('/'),
            'create' => Pages\CreateTblRoute::route('/create'),
            'edit' => Pages\EditTblRoute::route('/{record}/edit'),
        ];
    }
}
