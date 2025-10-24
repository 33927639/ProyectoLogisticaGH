<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function create(User $user): bool
    {
        return $this->isOperator($user);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->isOperator($user);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->isSupervisor($user);
    }

    public function restore(User $user, Customer $customer): bool
    {
        return $this->isSupervisor($user);
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $this->isAdmin($user);
    }
}