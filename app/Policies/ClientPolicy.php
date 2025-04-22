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
        return $user->hasAnyRole(['admin', 'manager', 'receptionist']);
    }

    /**
     * Determine whether the user can view the client.
     */
    public function view(User $user, Client $client): bool
    {
        // Admin/manager can view any client
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }

        // Receptionist can only view unapproved or their approved clients
        if ($user->isReceptionist()) {
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
        return $user === null;
    }

    /**
     * Determine whether the user can update the client.
     */
    public function update(User $user, Client $client): bool
    {
        // Admin/manager can update any client
        if ($user->hasAnyRole(['admin', 'manager'])) {
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
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can approve the client.
     */
    public function approve(User $user, Client $client)
    {
        // Only manager/receptionist can approve
        return $user->hasAnyRole(['manager', 'receptionist']) &&
            !$client->approved_at;
    }



    //========== Determine whether the user can view client's reservations =========

    public function viewReservations(User $user, Client $client)
    {
        // Admin/manager can view any
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }

        // Receptionist can view their approved clients
        if ($user->isReceptionist()) {
            return $client->approved_at &&
                $client->approver_id === $user->profile->id;
        }

        // Client can view their own
        return $user->profile_type === Client::class &&
            $user->profile_id === $client->id;
    }

    public function makeReservation(User $user)
    {
        return $user->hasRole('client') && 
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