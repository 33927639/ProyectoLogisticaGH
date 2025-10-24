<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDelivery extends EditRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Cargar productos existentes
        $delivery = $this->record;
        $products = \DB::table('delivery_products')
            ->where('delivery_id', $delivery->id_delivery)
            ->get();

        $data['deliveryProducts'] = $products->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'quantity' => $product->quantity,
                'unit_price' => $product->unit_price,
                'subtotal' => $product->subtotal, // Este será el valor calculado automáticamente
            ];
        })->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $delivery = $this->record;
        $products = $this->data['deliveryProducts'] ?? [];

        // Eliminar productos existentes
        \DB::table('delivery_products')
            ->where('delivery_id', $delivery->id_delivery)
            ->delete();

        // Agregar productos actualizados
        foreach ($products as $productData) {
            if (isset($productData['product_id'])) {
                \DB::table('delivery_products')->insert([
                    'delivery_id' => $delivery->id_delivery,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    // No insertar subtotal ya que es una columna generada
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
