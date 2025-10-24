<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDelivery extends CreateRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function afterCreate(): void
    {
        $delivery = $this->record;
        $products = $this->data['deliveryProducts'] ?? [];

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
