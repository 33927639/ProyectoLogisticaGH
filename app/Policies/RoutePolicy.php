<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Route;

class RoutePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function view(User $user, Route $route): bool
    {
        return $this->isOperator($user) || $this->isDriver($user);
    }

    public function create(User $user): bool
    {
        return $this->isOperator($user);
    }

    public function update(User $user, Route $route): bool
    {
        return $this->isOperator($user);
    }

    public function delete(User $user, Route $route): bool
    {
        return $this->isSupervisor($user);
    }

    public function restore(User $user, Route $route): bool
    {
        return $this->isSupervisor($user);
    }

    public function forceDelete(User $user, Route $route): bool
    {
        return $this->isAdmin($user);
    }
}