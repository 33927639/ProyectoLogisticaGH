<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeResource\Pages;
use App\Filament\Resources\IncomeResource\RelationManagers;
use App\Models\Income;
use App\Models\User;
use App\Models\Delivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    
    protected static ?string $navigationGroup = 'Finanzas';

    /**
     * Check if user can access this resource
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('viewAny', Income::class) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create', Income::class) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Ingreso')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Monto')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('Q')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('income_date')
                                    ->label('Fecha del Ingreso')
                                    ->default(now())
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->label('Usuario')
                                    ->relationship('user', 'first_name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('delivery_id')
                                    ->label('Entrega (Opcional)')
                                    ->relationship('delivery', 'id_delivery')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Toggle::make('status')
                                    ->label('Activo')
                                    ->default(true),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('income_date')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('GTQ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('Usuario'),
                Tables\Columns\TextColumn::make('delivery.id_delivery')
                    ->label('Entrega #'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('income_date')
                    ->form([
                        Forms\Components\DatePicker::make('income_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('income_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['income_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('income_date', '>=', $date),
                            )
                            ->when(
                                $data['income_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('income_date', '<=', $date),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function getWidgets(): array
    {
        return [
            IncomeResource\Widgets\IncomeStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
