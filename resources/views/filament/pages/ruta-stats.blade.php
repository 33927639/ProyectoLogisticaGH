<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Distancia</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($record->distancia_km, 2) }} km</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tiempo Estimado</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $record->tiempo_estimado_formato }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Velocidad Promedio</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $record->velocidad_promedio }} km/h</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas de Viajes</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total de viajes completados:</span>
                    <span class="font-semibold">{{ $record->total_viajes_completados }}</span>
                </div>
                @if($record->tiempo_promedio_real)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tiempo promedio real:</span>
                    <span class="font-semibold">{{ number_format($record->tiempo_promedio_real, 1) }}h</span>
                </div>
                @endif
                @if($record->eficiencia_ruta)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Eficiencia de la ruta:</span>
                    <span class="font-semibold {{ $record->eficiencia_ruta >= 90 ? 'text-green-600' : ($record->eficiencia_ruta >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($record->eficiencia_ruta, 1) }}%
                    </span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Dificultad:</span>
                    <span class="font-semibold">{{ $record->dificultad }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Consumo estimado:</span>
                    <span class="font-semibold">{{ $record->consumo_estimado_combustible }}L</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Adicional</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Estado:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->estado === 'Activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $record->estado }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Ruta de regreso:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->tiene_ruta_inversa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $record->tiene_ruta_inversa ? 'Disponible' : 'No disponible' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Ruta larga:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->es_ruta_larga ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $record->es_ruta_larga ? 'Sí (+200 km)' : 'No' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($record->descripcion)
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Descripción</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ $record->descripcion }}</p>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rutas Similares</h3>
        @if($record->rutasSimilares()->exists())
        <div class="space-y-2">
            @foreach($record->rutasSimilares()->get() as $rutaSimilar)
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $rutaSimilar->nombre_completo }}</span>
                <span class="text-sm font-medium">{{ number_format($rutaSimilar->distancia_km, 0) }} km</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-sm">No hay otras rutas desde {{ $record->origen }}</p>
        @endif
    </div>
</div>