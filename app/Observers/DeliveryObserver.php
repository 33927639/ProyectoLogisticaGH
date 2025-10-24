<?php

namespace App\Observers;

use App\Models\Delivery;
use App\Models\Income;
use App\Models\DeliveryStatus;

class DeliveryObserver
{
    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "updated" event.
     */
    public function updated(Delivery $delivery): void
    {
        // Verificar si el estado cambió a "Entregado" o "Completado"
        if ($delivery->wasChanged('status_id')) {
            $deliveryStatus = DeliveryStatus::find($delivery->status_id);
            
            if ($deliveryStatus && in_array(strtolower($deliveryStatus->name_status), ['entregado', 'completado', 'delivered', 'completed'])) {
                $this->createIncomeFromDelivery($delivery);
            }
        }
    }

    /**
     * Create income from completed delivery
     */
    protected function createIncomeFromDelivery(Delivery $delivery): void
    {
        // Verificar si ya existe un income para esta entrega
        $existingIncome = Income::where('delivery_id', $delivery->id_delivery)->first();
        
        if ($existingIncome) {
            return; // Ya existe un income para esta entrega
        }

        // Calcular el total de los productos de la entrega desde la tabla pivot
        $totalAmount = \DB::table('delivery_products')
            ->where('delivery_id', $delivery->id_delivery)
            ->sum('subtotal');

        // Si no hay productos, usar un monto base
        if ($totalAmount == 0) {
            $totalAmount = 100.00; // Monto base por entrega
        }

        // Crear el income
        Income::create([
            'amount' => $totalAmount,
            'description' => "Ingreso por entrega #{$delivery->id_delivery} - Ruta: " . 
                           ($delivery->route ? $delivery->route->origin->name_municipality . ' → ' . $delivery->route->destination->name_municipality : 'N/A'),
            'income_date' => now(),
            'user_id' => auth()->user()->id_user ?? 1, // Usuario actual o admin por defecto
            'delivery_id' => $delivery->id_delivery,
            'status' => true,
        ]);

        \Log::info("Income creado automáticamente para entrega #{$delivery->id_delivery}", [
            'delivery_id' => $delivery->id_delivery,
            'amount' => $totalAmount,
            'status' => $delivery->deliveryStatus->name_status ?? 'Unknown'
        ]);
    }

    /**
     * Handle the Delivery "deleted" event.
     */
    public function deleted(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "restored" event.
     */
    public function restored(Delivery $delivery): void
    {
        //
    }

    /**
     * Handle the Delivery "force deleted" event.
     */
    public function forceDeleted(Delivery $delivery): void
    {
        //
    }
}
