<div class="space-y-4">
    @if($products->isEmpty())
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <p class="text-gray-600 dark:text-gray-400 text-center italic">
                No hay productos agregados a esta entrega
            </p>
        </div>
    @else
        <!-- Tabla de productos estilo similar al Edit -->
        <div class="space-y-3">
            <div class="grid grid-cols-4 gap-4 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300">
                <div>Producto</div>
                <div>Cantidad</div>
                <div>Precio Unitario</div>
                <div>Subtotal</div>
            </div>
            
            @foreach($products as $product)
                <div class="grid grid-cols-4 gap-4 px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $product->product_name }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">
                        {{ number_format($product->quantity) }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">
                        Q{{ number_format($product->unit_price, 2) }}
                    </div>
                    <div class="font-semibold text-green-600 dark:text-green-400">
                        Q{{ number_format($product->subtotal, 2) }}
                    </div>
                </div>
            @endforeach
            
            <!-- Total general -->
            <div class="flex justify-end mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-green-50 dark:bg-green-900/20 px-6 py-3 rounded-lg">
                    <div class="text-sm text-green-700 dark:text-green-300 font-medium">Total General</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Q{{ number_format($total, 2) }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>