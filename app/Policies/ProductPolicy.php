<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function view(User $user, Product $product): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function create(User $user): bool
    {
        return $this->isOperator($user);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->isOperator($user);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->isSupervisor($user);
    }

    public function restore(User $user, Product $product): bool
    {
        return $this->isSupervisor($user);
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $this->isAdmin($user);
    }
}