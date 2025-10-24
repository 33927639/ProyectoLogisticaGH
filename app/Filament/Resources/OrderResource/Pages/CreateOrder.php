<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calcular el total basado en los productos
        $total = 0;
        if (isset($data['products'])) {
            foreach ($data['products'] as &$product) {
                // Si se seleccionó un producto_id, obtener información del producto
                if (isset($product['product_id']) && $product['product_id']) {
                    $productModel = \App\Models\Product::find($product['product_id']);
                    if ($productModel) {
                        $product['product_name'] = $productModel->name;
                        $product['product_description'] = $productModel->description;
                        $product['unit_price'] = $productModel->unit_price;
                    }
                }
                
                // Calcular subtotal
                $product['subtotal'] = ($product['quantity'] ?? 0) * ($product['unit_price'] ?? 0);
                $total += $product['subtotal'];
            }
        }
        $data['total_amount'] = $total;

        return $data;
    }
}
