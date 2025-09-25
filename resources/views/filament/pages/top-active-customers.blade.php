<x-filament::page>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold">Top 10 clientes más activos</h2>
            <p class="text-sm text-gray-500">
                Basado en número de ingresos asociados a entregas en los últimos {{ $this->days }} días.
            </p>
        </div>

        <form wire:submit.prevent="$refresh" class="flex items-center gap-2">
            <input type="number" min="1" wire:model.defer="days" class="fi-input w-24" />
            <x-filament::button type="submit">Aplicar</x-filament::button>
        </form>
    </div>

    {{ $this->table }}
</x-filament::page>
