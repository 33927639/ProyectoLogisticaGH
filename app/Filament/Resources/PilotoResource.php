<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PilotoResource\Pages;
use App\Filament\Resources\PilotoResource\RelationManagers;
use App\Filament\Resources\ViajeResource;
use App\Models\Piloto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PilotoResource extends Resource
{
    protected static ?string $model = Piloto::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static ?string $navigationLabel = 'Pilotos';
    
    protected static ?string $modelLabel = 'Piloto';
    
    protected static ?string $pluralModelLabel = 'Pilotos';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationGroup = 'Gestión de Personal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ingrese el nombre')
                                    ->suffixIcon('heroicon-m-user'),
                                Forms\Components\TextInput::make('apellido')
                                    ->label('Apellido')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ingrese el apellido'),
                            ]),
                        Forms\Components\TextInput::make('licencia')
                            ->label('Número de Licencia')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: C-1234567')
                            ->suffixIcon('heroicon-m-credit-card'),
                    ]),
                
                Forms\Components\Section::make('Información de Contacto')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('Ej: 1234-5678')
                                    ->suffixIcon('heroicon-m-phone'),
                                Forms\Components\TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('ejemplo@correo.com')
                                    ->suffixIcon('heroicon-m-envelope'),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado del Piloto')
                            ->required()
                            ->options([
                                Piloto::ESTADO_ACTIVO => 'Activo',
                                Piloto::ESTADO_INACTIVO => 'Inactivo',
                                Piloto::ESTADO_SUSPENDIDO => 'Suspendido',
                            ])
                            ->default(Piloto::ESTADO_ACTIVO)
                            ->native(false)
                            ->helperText('Estado actual del piloto en el sistema'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('iniciales')
                    ->label('Iniciales')
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->size('sm'),
                
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->searchable(['nombre', 'apellido'])
                    ->sortable(['nombre'])
                    ->weight(FontWeight::Bold)
                    ->description(fn (Piloto $record): string => $record->telefono_formateado),
                
                Tables\Columns\TextColumn::make('licencia')
                    ->label('Licencia')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Licencia copiada')
                    ->fontFamily('mono'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('nivel_experiencia')
                    ->label('Experiencia')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nuevo' => 'gray',
                        'Principiante' => 'info',
                        'Intermedio' => 'warning',
                        'Avanzado' => 'success',
                        'Experto' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('total_viajes_completados')
                    ->label('Viajes')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip('Total de viajes completados'),
                
                Tables\Columns\TextColumn::make('total_kilometros_recorridos')
                    ->label('Km Recorridos')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' km')
                    ->alignEnd()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Inactivo' => 'danger',
                        'Suspendido' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('esta_disponible')
                    ->label('Disponible')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Piloto $record) => $record->esta_disponible ? 'Disponible para viajes' : 'No disponible'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        Piloto::ESTADO_ACTIVO => 'Activo',
                        Piloto::ESTADO_INACTIVO => 'Inactivo',
                        Piloto::ESTADO_SUSPENDIDO => 'Suspendido',
                    ])
                    ->placeholder('Todos los estados'),
                
                Filter::make('disponibles')
                    ->label('Disponibles')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('estado', Piloto::ESTADO_ACTIVO)
                              ->whereDoesntHave('viajes', fn (Builder $query) => 
                                  $query->where('estado', 'En Curso')
                              )
                    )
                    ->toggle(),
                
                SelectFilter::make('nivel_experiencia')
                    ->label('Nivel de Experiencia')
                    ->options([
                        'Nuevo' => 'Nuevo',
                        'Principiante' => 'Principiante',
                        'Intermedio' => 'Intermedio',
                        'Avanzado' => 'Avanzado',
                        'Experto' => 'Experto',
                    ])
                    ->placeholder('Todos los niveles'),
                
                Filter::make('con_email')
                    ->label('Con Email Registrado')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    
                    Tables\Actions\Action::make('asignar_viaje')
                        ->label('Asignar Viaje')
                        ->icon('heroicon-o-truck')
                        ->color('success')
                        ->visible(fn (Piloto $record) => $record->esta_disponible)
                        ->url(fn (Piloto $record): string => ViajeResource::getUrl('create', ['piloto_id' => $record->id])),
                    
                    Tables\Actions\Action::make('cambiar_estado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo Estado')
                                ->required()
                                ->options([
                                    Piloto::ESTADO_ACTIVO => 'Activo',
                                    Piloto::ESTADO_INACTIVO => 'Inactivo',
                                    Piloto::ESTADO_SUSPENDIDO => 'Suspendido',
                                ])
                                ->native(false),
                            Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones')
                                ->placeholder('Motivo del cambio de estado...')
                                ->rows(3),
                        ])
                        ->action(function (Piloto $record, array $data): void {
                            $record->update(['estado' => $data['estado']]);
                            
                            Notification::make()
                                ->title('Estado actualizado')
                                ->body("Piloto {$record->nombre_completo} ahora está {$data['estado']}")
                                ->success()
                                ->send();
                        }),
                ])->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cambiar_estado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo Estado')
                                ->required()
                                ->options([
                                    Piloto::ESTADO_ACTIVO => 'Activo',
                                    Piloto::ESTADO_INACTIVO => 'Inactivo',
                                    Piloto::ESTADO_SUSPENDIDO => 'Suspendido',
                                ])
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Piloto $record) use ($data) {
                                $record->update(['estado' => $data['estado']]);
                            });
                            
                            Notification::make()
                                ->title('Estados actualizados')
                                ->body("Se actualizaron {$records->count()} pilotos")
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            // Will be added after creating the relation managers
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $disponibles = static::getModel()::where('estado', Piloto::ESTADO_ACTIVO)
            ->whereDoesntHave('viajes', fn (Builder $query) => 
                $query->where('estado', 'En Curso')
            )->count();
        
        if ($disponibles > 10) {
            return 'success';
        } elseif ($disponibles > 5) {
            return 'warning';
        }
        
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPilotos::route('/'),
            'create' => Pages\CreatePiloto::route('/create'),
            'edit' => Pages\EditPiloto::route('/{record}/edit'),
        ];
    }
}
