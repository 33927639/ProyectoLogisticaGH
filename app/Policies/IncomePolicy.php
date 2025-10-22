<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Income;

class IncomePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Solo Admin, Supervisor y Operador pueden ver ingresos
        return $this->isOperator($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Income $income): bool
    {
        // Solo Admin, Supervisor y Operador pueden ver detalles de ingresos
        return $this->isOperator($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo Admin y Supervisor pueden crear ingresos
        return $this->isSupervisor($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Income $income): bool
    {
        // Solo Admin y Supervisor pueden editar ingresos
        return $this->isSupervisor($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Income $income): bool
    {
        // Solo Admin puede eliminar ingresos
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Income $income): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Income $income): bool
    {
        return $this->isSuperAdmin($user);
    }
}