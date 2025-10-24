<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('create_delivery')
                ->label('Crear Entrega')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === 'CONFIRMED')
                ->url(fn (): string => route('filament.admin.resources.deliveries.create', ['order_id' => $this->record->id_order])),
        ];
    }
}