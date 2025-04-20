<?php

namespace App\Policies;

use App\Models\Floor;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class FloorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Change from roles to permissions
        return $user->hasPermissionTo('view floors');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Floor $floor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create floors');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Floor $floor): bool
    {
        if (!$user->hasPermissionTo('edit floors')) {
            return false;
        }

        // Keep manager-specific logic
        if ($user->hasRole('Manager')) {
            return $floor->manager_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Floor $floor): Response
    {
        if ($floor->rooms()->exists()) {
            return Response::deny('Cannot delete floor with rooms.');
        }

        $allowed = $user->hasPermissionTo('delete floors') &&
            ($user->hasRole('Admin') ||
                ($user->hasRole('Manager') && $floor->manager_id === $user->id));

        return $allowed ? Response::allow() : Response::deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Floor $floor): bool
    {
        return $user->hasPermissionTo('restore floors');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Floor $floor): bool
    {
        return false;
    }
}
