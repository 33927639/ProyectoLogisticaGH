<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function view(User $user, Order $order): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function create(User $user): bool
    {
        return $this->isOperator($user);
    }

    public function update(User $user, Order $order): bool
    {
        return $this->isOperator($user);
    }

    public function delete(User $user, Order $order): bool
    {
        return $this->isSupervisor($user);
    }

    public function restore(User $user, Order $order): bool
    {
        return $this->isSupervisor($user);
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $this->isAdmin($user);
    }
}