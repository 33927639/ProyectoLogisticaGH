<?php

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    /**
     * Check if user has any of the specified roles
     */
    protected function hasRole(User $user, array $roles): bool
    {
        return $user->roles()->whereIn('name_role', $roles)->exists();
    }

    /**
     * Check if user is Super Administrator
     */
    protected function isSuperAdmin(User $user): bool
    {
        return $this->hasRole($user, ['Super Administrador']);
    }

    /**
     * Check if user is Administrator or Super Administrator
     */
    protected function isAdmin(User $user): bool
    {
        return $this->hasRole($user, ['Super Administrador', 'Administrador']);
    }

    /**
     * Check if user is Supervisor, Administrator or Super Administrator
     */
    protected function isSupervisor(User $user): bool
    {
        return $this->hasRole($user, ['Super Administrador', 'Administrador', 'Supervisor']);
    }

    /**
     * Check if user is Operator, Supervisor, Administrator or Super Administrator
     */
    protected function isOperator(User $user): bool
    {
        return $this->hasRole($user, ['Super Administrador', 'Administrador', 'Supervisor', 'Operador']);
    }

    /**
     * Check if user is Driver
     */
    protected function isDriver(User $user): bool
    {
        return $this->hasRole($user, ['Conductor']);
    }

    /**
     * Check if user can only view (not manage) resources
     */
    protected function canOnlyView(User $user): bool
    {
        return $this->hasRole($user, ['Conductor']);
    }
}