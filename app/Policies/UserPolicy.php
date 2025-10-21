<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->active && ($user->isAdmin() || $user->isSupervisor());
    }

    public function view(User $user, User $model): bool
    {
        if (!$user->active) return false;

        // Admin can view all users
        if ($user->isAdmin()) return true;

        // Supervisor can view users in their zone
        if ($user->isSupervisor() && $user->zone_id === $model->zone_id) return true;

        // Users can view their own profile
        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->active && $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if (!$user->active) return false;

        // Admin can update all users
        if ($user->isAdmin()) return true;

        // Users can update their own profile (limited fields)
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->active && $user->isAdmin() && $user->id !== $model->id;
    }
}
