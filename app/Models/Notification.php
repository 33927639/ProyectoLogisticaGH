<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id_notification';
    public $timestamps = false; // Using only created_at

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'maintenance_id',
        'delivery_id',
        'type',
        'channel',
        'message',
        'trigger_km',
        'sent',
        'sent_at',
        'created_at',
    ];

    protected $casts = [
        'sent' => 'boolean',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'trigger_km' => 'integer',
    ];

    // Notification types
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_DELIVERY = 'delivery';
    const TYPE_VEHICLE = 'vehicle';
    const TYPE_FINANCIAL = 'financial';
    const TYPE_SYSTEM = 'system';

    // Notification channels
    const CHANNEL_IN_APP = 'in_app';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';

    /**
     * Relationship with user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with vehicle (optional)
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Relationship with maintenance (optional)
     */
    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    /**
     * Relationship with delivery (optional)
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('sent', false);
    }

    /**
     * Scope for in-app notifications
     */
    public function scopeInApp($query)
    {
        return $query->where('channel', self::CHANNEL_IN_APP);
    }

    /**
     * Scope for user notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'sent' => true,
            'sent_at' => now(),
        ]);
    }
}
