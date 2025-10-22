<?php

namespace App\Filament\Resources\RouteResource\Pages;

use App\Filament\Resources\RouteResource;
use App\Models\Route;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRoute extends CreateRecord
{
    protected static string $resource = RouteResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->data;
        
        // Verificar que no sean el mismo municipio
        if ($data['origin_id'] === $data['destination_id']) {
            Notification::make()
                ->title('Error de Validación')
                ->body('El municipio de origen y destino no pueden ser el mismo.')
                ->danger()
                ->send();
            
            $this->halt();
        }

        // Verificar si ya existe una ruta entre estos municipios
        $existingRoute = Route::findExistingRoute($data['origin_id'], $data['destination_id']);
        
        if ($existingRoute) {
            Notification::make()
                ->title('Ruta Duplicada')
                ->body("Ya existe una ruta entre estos municipios: {$existingRoute->route_name} ({$existingRoute->distance_km} km)")
                ->warning()
                ->send();
            
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Ruta creada exitosamente con distancia calculada automáticamente';
    }
}
