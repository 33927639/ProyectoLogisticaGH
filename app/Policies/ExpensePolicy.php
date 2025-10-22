<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Expense;

class ExpensePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Solo Admin, Supervisor y Operador pueden ver gastos
        return $this->isOperator($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        // Solo Admin, Supervisor y Operador pueden ver detalles de gastos
        return $this->isOperator($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo Admin y Supervisor pueden crear gastos
        return $this->isSupervisor($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        // Solo Admin y Supervisor pueden editar gastos
        return $this->isSupervisor($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // Solo Admin puede eliminar gastos
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $this->isSuperAdmin($user);
    }
}