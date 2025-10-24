<div class="fi-wi-notification-bell">
    <div class="relative" x-data="{ 
        open: false,
        notifications: @js($notifications),
        unreadCount: @js($unreadCount),
        markAsRead(id) {
            $wire.markAsRead(id);
            this.notifications = this.notifications.filter(n => n.id_notification !== id);
            this.unreadCount = Math.max(0, this.unreadCount - 1);
        },
        markAllAsRead() {
            $wire.markAllAsRead();
            this.notifications = [];
            this.unreadCount = 0;
            this.open = false;
        }
    }">
        <!-- Notification Bell Button -->
        <button 
            @click="open = !open"
            class="relative flex items-center justify-center w-10 h-10 text-gray-500 bg-white rounded-full shadow-sm ring-1 ring-gray-950/5 hover:text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 dark:text-gray-400 dark:ring-white/10 dark:hover:text-gray-300 dark:hover:bg-gray-800"
        >
            <x-heroicon-o-bell class="h-6 w-6" />
            
            <!-- Notification Count Badge -->
            <span 
                x-show="unreadCount > 0"
                x-text="unreadCount > 99 ? '99+' : unreadCount"
                class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 text-xs font-bold text-white bg-red-500 rounded-full"
            ></span>
        </button>

        <!-- Notification Dropdown -->
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="open = false"
            class="absolute right-0 top-12 z-50 mt-2 w-80 max-w-sm bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 dark:ring-white/10"
        >
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Notificaciones
                </h3>
                <button 
                    x-show="unreadCount > 0"
                    @click="markAllAsRead()"
                    class="text-xs text-primary-600 hover:text-primary-500 dark:text-primary-400"
                >
                    Marcar todas como leídas
                </button>
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                <template x-if="notifications.length === 0">
                    <div class="px-4 py-6 text-center">
                        <x-heroicon-o-bell-slash class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            No hay notificaciones
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No tienes notificaciones pendientes en este momento.
                        </p>
                    </div>
                </template>

                <template x-for="notification in notifications" :key="notification.id_notification">
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                        <div class="flex items-start space-x-3">
                            <!-- Notification Icon -->
                            <div class="flex-shrink-0 mt-0.5">
                                <template x-if="notification.type === 'maintenance'">
                                    <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-full dark:bg-yellow-900/20">
                                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                </template>
                                <template x-if="notification.type === 'delivery'">
                                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full dark:bg-blue-900/20">
                                        <x-heroicon-o-truck class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                </template>
                                <template x-if="notification.type === 'vehicle'">
                                    <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full dark:bg-green-900/20">
                                        <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    </div>
                                </template>
                                <template x-if="notification.type === 'financial'">
                                    <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-full dark:bg-red-900/20">
                                        <x-heroicon-o-currency-dollar class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    </div>
                                </template>
                                <template x-if="notification.type === 'system'">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full dark:bg-gray-900/20">
                                        <x-heroicon-o-information-circle class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                                    </div>
                                </template>
                            </div>

                            <!-- Notification Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white" x-text="notification.message"></p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" 
                                   x-text="new Date(notification.created_at).toLocaleString('es-GT', {
                                       year: 'numeric',
                                       month: 'short',
                                       day: 'numeric',
                                       hour: '2-digit',
                                       minute: '2-digit'
                                   })">
                                </p>
                            </div>

                            <!-- Mark as Read Button -->
                            <button 
                                @click="markAsRead(notification.id_notification)"
                                class="flex-shrink-0 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                title="Marcar como leída"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <template x-if="notifications.length > 0">
                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50">
                    <button 
                        @click="open = false"
                        class="block w-full text-center text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400"
                    >
                        Ver todas las notificaciones
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh notifications every 30 seconds
    setInterval(() => {
        if (typeof $wire !== 'undefined') {
            $wire.$refresh();
        }
    }, 30000);
</script>
@endpush