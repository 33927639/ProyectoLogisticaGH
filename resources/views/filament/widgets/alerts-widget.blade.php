<div class="fi-wi-alerts">
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
            <div class="grid flex-1 gap-y-1">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    <span class="flex items-center gap-x-2">
                        <x-heroicon-o-bell class="h-5 w-5" />
                        Alertas Importantes
                    </span>
                </h3>
            </div>
        </div>

        <div class="fi-section-content-ctn overflow-hidden">
            <div class="fi-section-content p-6 pt-0">
                @if($alerts->isEmpty())
                    <div class="flex items-center justify-center py-8">
                        <div class="text-center">
                            <x-heroicon-o-check-circle class="mx-auto h-12 w-12 text-green-500" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Todo en orden</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No hay alertas importantes en este momento.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($alerts as $alert)
                            <div class="flex items-start space-x-3 rounded-lg border p-4 
                                {{ $alert['type'] === 'danger' ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' : '' }}
                                {{ $alert['type'] === 'warning' ? 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20' : '' }}
                                {{ $alert['type'] === 'info' ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20' : '' }}
                            ">
                                <div class="flex-shrink-0">
                                    @php
                                        $iconColor = match($alert['type']) {
                                            'danger' => 'text-red-500',
                                            'warning' => 'text-yellow-500',
                                            'info' => 'text-blue-500',
                                            default => 'text-gray-500'
                                        };
                                    @endphp
                                    <x-dynamic-component :component="$alert['icon']" class="h-5 w-5 {{ $iconColor }}" />
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium 
                                            {{ $alert['type'] === 'danger' ? 'text-red-900 dark:text-red-100' : '' }}
                                            {{ $alert['type'] === 'warning' ? 'text-yellow-900 dark:text-yellow-100' : '' }}
                                            {{ $alert['type'] === 'info' ? 'text-blue-900 dark:text-blue-100' : '' }}
                                        ">
                                            {{ $alert['title'] }}
                                        </h4>
                                        
                                        @if(isset($alert['action_url']))
                                            <a href="{{ $alert['action_url'] }}" 
                                               class="text-sm font-medium 
                                                   {{ $alert['type'] === 'danger' ? 'text-red-700 hover:text-red-800 dark:text-red-300 dark:hover:text-red-200' : '' }}
                                                   {{ $alert['type'] === 'warning' ? 'text-yellow-700 hover:text-yellow-800 dark:text-yellow-300 dark:hover:text-yellow-200' : '' }}
                                                   {{ $alert['type'] === 'info' ? 'text-blue-700 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-200' : '' }}
                                               ">
                                                Ver →
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <p class="mt-1 text-sm 
                                        {{ $alert['type'] === 'danger' ? 'text-red-700 dark:text-red-200' : '' }}
                                        {{ $alert['type'] === 'warning' ? 'text-yellow-700 dark:text-yellow-200' : '' }}
                                        {{ $alert['type'] === 'info' ? 'text-blue-700 dark:text-blue-200' : '' }}
                                    ">
                                        {{ $alert['message'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>