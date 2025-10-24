<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationGroup = 'Sistema';
    
    protected static ?string $navigationLabel = 'Notificaciones';
    
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Notificación')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                Notification::TYPE_MAINTENANCE => 'Mantenimiento',
                                Notification::TYPE_DELIVERY => 'Entrega',
                                Notification::TYPE_VEHICLE => 'Vehículo',
                                Notification::TYPE_FINANCIAL => 'Financiero',
                                Notification::TYPE_SYSTEM => 'Sistema',
                            ])
                            ->required(),

                        Forms\Components\Select::make('channel')
                            ->label('Canal')
                            ->options([
                                Notification::CHANNEL_IN_APP => 'In-App',
                                Notification::CHANNEL_EMAIL => 'Email',
                                Notification::CHANNEL_SMS => 'SMS',
                            ])
                            ->default(Notification::CHANNEL_IN_APP)
                            ->required(),

                        Forms\Components\Textarea::make('message')
                            ->label('Mensaje')
                            ->required()
                            ->maxLength(500),
                    ])->columns(2),

                Forms\Components\Section::make('Referencias Opcionales')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehículo')
                            ->relationship('vehicle', 'license_plate')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('maintenance_id')
                            ->label('Mantenimiento')
                            ->relationship('maintenance', 'id_maintenance')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('delivery_id')
                            ->label('Entrega')
                            ->relationship('delivery', 'id_delivery')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('trigger_km')
                            ->label('Kilómetro Disparador')
                            ->numeric(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('sent')
                            ->label('Enviado')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Enviado en')
                            ->displayFormat('d/m/Y H:i'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Notification::TYPE_MAINTENANCE => 'warning',
                        Notification::TYPE_DELIVERY => 'info',
                        Notification::TYPE_VEHICLE => 'success',
                        Notification::TYPE_FINANCIAL => 'danger',
                        Notification::TYPE_SYSTEM => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Notification::TYPE_MAINTENANCE => 'Mantenimiento',
                        Notification::TYPE_DELIVERY => 'Entrega',
                        Notification::TYPE_VEHICLE => 'Vehículo',
                        Notification::TYPE_FINANCIAL => 'Financiero',
                        Notification::TYPE_SYSTEM => 'Sistema',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('channel')
                    ->label('Canal')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Notification::CHANNEL_IN_APP => 'In-App',
                        Notification::CHANNEL_EMAIL => 'Email',
                        Notification::CHANNEL_SMS => 'SMS',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('sent')
                    ->label('Enviado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Enviado en')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        Notification::TYPE_MAINTENANCE => 'Mantenimiento',
                        Notification::TYPE_DELIVERY => 'Entrega',
                        Notification::TYPE_VEHICLE => 'Vehículo',
                        Notification::TYPE_FINANCIAL => 'Financiero',
                        Notification::TYPE_SYSTEM => 'Sistema',
                    ]),

                Tables\Filters\SelectFilter::make('channel')
                    ->label('Canal')
                    ->options([
                        Notification::CHANNEL_IN_APP => 'In-App',
                        Notification::CHANNEL_EMAIL => 'Email',
                        Notification::CHANNEL_SMS => 'SMS',
                    ]),

                Tables\Filters\TernaryFilter::make('sent')
                    ->label('Enviado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markAsRead')
                    ->label('Marcar como leído')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Notification $record): bool => !$record->sent)
                    ->action(fn (Notification $record) => $record->markAsRead()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsRead')
                        ->label('Marcar como leídas')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if (!$record->sent) {
                                    $record->markAsRead();
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('viewAny', Notification::class) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create', Notification::class) ?? false;
    }
}
