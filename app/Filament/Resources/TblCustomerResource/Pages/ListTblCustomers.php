<?php

namespace App\Filament\Resources\TblCustomerResource\Pages;

use App\Filament\Resources\TblCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

class ListTblCustomers extends ListRecords
{
    protected static string $resource = TblCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Cliente'),
            Action::make('exportPdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document')
                ->url(fn () => URL::route('customers.export.pdf'))
                ->openUrlInNewTab(),
        ];
    }
}
