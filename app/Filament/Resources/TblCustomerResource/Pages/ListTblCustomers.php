<?php

namespace App\Filament\Resources\TblCustomerResource\Pages;

use App\Filament\Resources\TblCustomerResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use App\Filament\Pages\TopActiveCustomers;

class ListTblCustomers extends ListRecords
{
    protected static string $resource = TblCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('top10')
                ->label('Top 10 activos')
                ->icon('heroicon-o-user-group')
                ->color('info')
                ->url(TopActiveCustomers::getUrl())   // <--- usar la página, NO route()
                ->openUrlInNewTab(),
        ];
    }
}
