<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view rooms');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('view rooms');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create rooms');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Room $room): bool
    {
        if (!$user->hasPermissionTo('edit rooms')) {
            return false;
        }

        if ($user->hasRole('Manager')) {
            return $room->manager_id === $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Room $room): Response
    {
        if ($room->reservations()->exists()) {
            return Response::deny('Cannot delete room with active reservations.');
        }

        $allowed = $user->hasPermissionTo('delete rooms') &&
            ($user->hasRole('Admin') ||
                ($user->hasRole('Manager') && $room->manager_id === $user->id));

        return $allowed ? Response::allow() : Response::deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Room $room): bool
    {
        return $user->hasPermissionTo('restore rooms');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Room $room): bool
    {
        return false;
    }
}
