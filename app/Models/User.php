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
        // Verificar que el usuario esté activo
        if (!$this->status) {
            return false;
        }
        
        // Panel principal para administradores y supervisores
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['Super Administrador', 'Administrador', 'Supervisor', 'Operador']);
        }
        
        // Panel de conductores
        if ($panel->getId() === 'driver') {
            return $this->hasRole(['Conductor']);
        }
        
        return false;
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
        return $this->hasMany(UserToken::class, 'user_id');
    }

    /**
     * Relationship with expenses
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'user_id');
    }

    /**
     * Relationship with incomes
     */
    public function incomes()
    {
        return $this->hasMany(Income::class, 'user_id');
    }

    /**
     * Relationship with fuel logs
     */
    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class, 'user_id');
    }

    /**
     * Relationship with delivery logs
     */
    public function deliveryLogs()
    {
        return $this->hasMany(DeliveryLog::class, 'user_id');
    }

    /**
     * Relationship with notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Relationship with approved maintenance requests
     */
    public function approvedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'approved_by');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name_role', $role)->exists();
        }

        if (is_array($role)) {
            return $this->roles()->whereIn('name_role', $role)->exists();
        }

        return false;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name_role', $roles)->exists();
    }

    /**
     * Check if user has all the given roles
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles()->pluck('name_role')->toArray();
        return empty(array_diff($roles, $userRoles));
    }

    /**
     * Get user's role names
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name_role')->toArray();
    }

    /**
     * Get user's primary role
     */
    public function getPrimaryRole(): ?string
    {
        $role = $this->roles()->first();
        return $role ? $role->name_role : null;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['Administrador', 'Super Administrador']);
    }

    /**
     * Check if user is supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->hasRole(['Administrador', 'Super Administrador', 'Supervisor']);
    }

    /**
     * Check if user can manage operations
     */
    public function canManageOperations(): bool
    {
        return $this->hasRole(['Administrador', 'Super Administrador', 'Supervisor', 'Operador']);
    }
}
