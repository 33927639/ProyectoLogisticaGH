<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['name', 'full_name'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Can access Filament admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status && $this->roles()->exists();
    }

    /**
     * Get name for Filament
     */
    public function getFilamentName(): string
    {
        return $this->getNameAttribute();
    }

    /**
     * Override getName method for compatibility
     */
    public function getName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get full name attribute
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get full name attribute (alternative)
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Relationship with roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'user_id', 'role_id', 'id_user', 'id_role');
    }

    /**
     * Relationship with user tokens
     */
    public function tokens()
    {
        return $this->hasMany(UserToken::class, 'user_id', 'id_user');
    }

    /**
     * Relationship with expenses
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'user_id', 'id_user');
    }

    /**
     * Relationship with incomes
     */
    public function incomes()
    {
        return $this->hasMany(Income::class, 'user_id', 'id_user');
    }

    /**
     * Relationship with fuel logs
     */
    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class, 'user_id', 'id_user');
    }

    /**
     * Relationship with delivery logs
     */
    public function deliveryLogs()
    {
        return $this->hasMany(DeliveryLog::class, 'user_id', 'id_user');
    }

    /**
     * Relationship with notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id_user');
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        return $this->roles()->whereIn('name_role', $roles)->exists();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRoleName(string $roleName): bool
    {
        return $this->roles()->where('name_role', $roleName)->exists();
    }

    /**
     * Get user role names
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name_role')->toArray();
    }

    /**
     * Relationship with approved maintenance requests
     */
    public function approvedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'approved_by', 'id_user');
    }
}
