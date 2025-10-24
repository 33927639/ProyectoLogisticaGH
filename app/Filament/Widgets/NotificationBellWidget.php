<?php

namespace App\Filament\Widgets;

use App\Models\Notification;
use App\Services\NotificationService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationBellWidget extends Widget
{
    protected static string $view = 'filament.widgets.notification-bell-widget';
    protected static ?int $sort = -10; // Show at top
    protected static ?string $pollingInterval = '5s'; // CRÍTICO: Notificaciones importantes

    protected function getViewData(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'notifications' => collect(),
                'unreadCount' => 0,
            ];
        }

        $notifications = NotificationService::getUnreadForUser($user->id_user);
        $unreadCount = NotificationService::getUnreadCountForUser($user->id_user);

        return [
            'notifications' => $notifications->take(10), // Show max 10 recent notifications
            'unreadCount' => $unreadCount,
            'userId' => $user->id_user,
        ];
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if ($user) {
            NotificationService::markAllAsReadForUser($user->id_user);
        }
    }
}