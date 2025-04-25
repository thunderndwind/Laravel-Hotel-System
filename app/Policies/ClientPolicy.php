<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;
//----------------------------------------------
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    /**
     * Determine whether the user can view any clients.
     */
    public function viewAny(User $user): bool
    {
        //return false;
        return $user->hasRole(['Admin', 'Manager', 'Receptionist']);
    }

    /**
     * Determine whether the user can view the client.
     */
    public function view(User $user, Client $client): bool
    {
        // Admin/Manager can view any client
        if ($user->hasRole(['Admin', 'Manager'])) {
            return true;
        }

        // Receptionist can only view unapproved or their approved clients
        if ($user->hasRole(['Receptionist'])) {
            return !$client->approved_at ||
                $client->approver_id === $user->profile->id;
        }

        // Client can view themselves
        return $user->profile_type === Client::class &&
            $user->profile_id === $client->id;
    }

    /**
     * Determine whether the user can create clients.
     */
    public function create(User $user): bool
    {
        // Only guests can register (no logged in user)
        return $user->hasRole(['Admin', 'Manager', 'Receptionist']) || $user === null;
    }

    /**
     * Determine whether the user can update the client.
     */
    public function update(User $user, Client $client): bool
    {
        // Admin/Manager can update any client
        if ($user->hasAnyRole(['Admin', 'Manager'])) {
            return true;
        }

        // Client can update themselves
        return $user->profile_type === Client::class &&
            $user->profile_id === $client->id;
    }

    /**
     * Determine whether the user can delete the client.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can approve the client.
     */
    public function approveAny(User $user)
    {
        return $user->hasAnyRole(['Manager', 'Receptionist']);
    }

    public function approve(User $user, Client $client)
    {
        return $this->approveAny($user) && !$client->approved_at;
    }



    //========== Determine whether the user can view client's reservations =========

    public function viewReservations(User $user, Client $client)
    {
        // Admin/Manager can view any
        if ($user->hasAnyRole(['Admin', 'Manager'])) {
            return true;
        }

        // Receptionist']); can view their approved clients
        if ($user->hasAnyRole('Receptionist')) {
            return $client->approved_at &&
                $client->approver_id === $user->profile->id;
        }

        // Client can view their own
        return $user->profile_type === Client::class &&
            $user->profile_id === $client->id;
    }

    public function makeReservation(User $user)
    {
        return $user->hasRole('Client') &&
            $user->profile->approved_at &&
            $user->hasVerifiedEmail();
    }



    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}