<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Delivery;

class DeliveryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Todos los usuarios pueden ver entregas
        return $this->isOperator($user) || $this->isDriver($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        // Todos los usuarios pueden ver detalles de entregas
        return $this->isOperator($user) || $this->isDriver($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin, Supervisor y Operador pueden crear entregas
        return $this->isOperator($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        // Admin, Supervisor y Operador pueden editar entregas
        return $this->isOperator($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        // Solo Admin y Supervisor pueden eliminar entregas
        return $this->isSupervisor($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Delivery $delivery): bool
    {
        return $this->isSupervisor($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Delivery $delivery): bool
    {
        return $this->isAdmin($user);
    }
}