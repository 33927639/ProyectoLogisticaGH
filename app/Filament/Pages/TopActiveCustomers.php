<?php

namespace App\Filament\Pages;

use App\Models\TblCustomer;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class TopActiveCustomers extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    // Aparición en el menú de Filament
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Top activos';
    protected static ?string $title           = 'Top 10 clientes más activos';
    protected static ?string $slug = 'top-active-customers';

    protected static ?string $navigationGroup = 'Clientes';

    // Vista Blade de la página
    protected static string $view = 'filament.pages.top-active-customers';

    // Parámetro editable desde el Blade
    public int $days = 30;

    public function table(Table $table): Table
    {
        $desde = Carbon::now()->subDays($this->days)->startOfDay()->toDateString();
        $hasta = Carbon::now()->endOfDay()->toDateString();

        // Builder Eloquent (para que Filament lo acepte sin problemas)
        $query = TblCustomer::query()
            ->join('tbl_deliveries as d', 'd.id_customer', '=', 'tbl_customers.id_customer')
            ->join('tbl_incomes as v', 'v.id_delivery', '=', 'd.id_delivery')
            ->where('v.status', 1)
            ->whereBetween('v.income_date', [$desde, $hasta]) // usa income_date; cambia a created_at si prefieres
            ->groupBy('tbl_customers.id_customer', 'tbl_customers.name', 'tbl_customers.email')
            ->selectRaw('
                tbl_customers.id_customer,
                tbl_customers.name,
                tbl_customers.email,
                COUNT(*) as ventas_totales
            ')
            ->orderByDesc('ventas_totales')
            ->limit(10);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ventas_totales')
                    ->label(fn () => "Ingresos ({$this->days} días)")
                    ->sortable()
                    ->alignRight(),
            ])
            ->paginated(false);
    }
}
