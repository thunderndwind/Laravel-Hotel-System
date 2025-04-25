<?php

namespace App\Policies;

use App\Models\Manager;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ManagerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function view(User $user, Manager $manager): bool
    {
        return $user->hasRole('Admin') ||
            ($user->hasRole('Manager') && $user->profile_type === Manager::class && $user->profile_id === $manager->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function update(User $user, Manager $manager): bool
    {
        return $user->hasRole('Admin') ||
            ($user->hasRole('Manager') && $user->profile_id === $manager->id);
    }

    public function delete(User $user, Manager $manager): Response
    {
        return ($user->hasRole('Admin') || ($user->hasRole('Manager') && $user->profile_type === Manager::class && $user->profile_id === $manager->id))
            ? Response::allow()
            : Response::deny('You are not allowed to delete this manager.');
    }

    public function restore(User $user, Manager $manager): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Manager $manager): bool
    {
        return false;
    }

    public function showDashboard(User $user): bool
    {
        return $user->hasRole('Manager');
    }
}
