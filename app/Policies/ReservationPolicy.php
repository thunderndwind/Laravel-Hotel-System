<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReservationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view reservations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('Client')) {
            return $reservation->user_id === $user->id;
        }

        return $user->hasPermissionTo('view reservations');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('make reservation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        if ($user->hasRole('Client')) {
            // Client can only update their upcoming reservations
            return $reservation->user_id === $user->id && $reservation->status === 'upcoming';
        }

        return $user->hasPermissionTo('approve reservation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reservation $reservation): Response
    {
        if ($user->hasRole('Client')) {
            if ($reservation->user_id !== $user->id) {
                return Response::deny('You can only cancel your own reservations.');
            }

            if ($reservation->status !== 'upcoming') {
                return Response::deny('You can only cancel upcoming reservations.');
            }

            return Response::allow();
        }

        if ($user->hasPermissionTo('approve reservation')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to cancel this reservation.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return $user->hasPermissionTo('approve reservation');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('Admin');
    }
}
