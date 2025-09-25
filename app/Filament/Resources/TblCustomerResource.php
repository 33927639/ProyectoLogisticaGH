<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TblCustomerResource\Pages;
use App\Filament\Resources\TblCustomerResource\RelationManagers;
use App\Models\TblCustomer;
use App\Models\TblDepartment;
use App\Models\TblMunicipality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class TblCustomerResource extends Resource
{
    protected static ?string $model = TblCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Lista de Clientes';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Cliente')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('nit')
                            ->label('NIT')
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. Teléfono')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(100)
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\Select::make('department')
                            ->label('Departamento')
                            ->options(TblDepartment::where('status_department', true)->pluck('name_department', 'id_department'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Set $set) => $set('id_municipality', null))
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\Select::make('id_municipality')
                            ->label('Municipio')
                            ->options(function (Get $get): array {
                                $departmentId = $get('department');
                                if (!$departmentId) {
                                    return [];
                                }
                                return TblMunicipality::where('id_department', $departmentId)
                                    ->where('status_municipality', true)
                                    ->pluck('name_municipality', 'id_municipality')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('status')
                            ->label('Cliente Activo')
                            ->default(true)
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-s-check')
                            ->columnSpan(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre Cliente')
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('municipality.name_municipality')
                    ->label('Municipio')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('municipality.tbl_department.name_department')
                    ->label('Departamento')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('status')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportPdf')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        // Obtener todos los clientes con sus relaciones
                        $customers = TblCustomer::with(['municipality.tbl_department'])->get();
                        
                        // Generar el PDF
                        $pdf = Pdf::loadView('pdf.customers-report', compact('customers'));
                        
                        // Configurar el PDF
                        $pdf->setPaper('A4', 'portrait');
                        
                        // Generar nombre del archivo con timestamp
                        $filename = 'clientes_reporte_' . now()->format('Y-m-d_H-i-s') . '.pdf';
                        
                        // Descargar el PDF
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\ViewAction::make()->label('Ver'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
    // No relation managers needed, only create, edit, list
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
