<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Reservation;

class Manager extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_image',
        'phone_number',
       
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];
    // Get All pending clients
    public function pendingClients()
    {
        return $this->hasMany(Client::class)->whereNull('approved_at');
    }
    // Get All approved clients
    public function approvedClients()
    {
        return $this->hasMany(Client::class)->whereNotNull('approved_at');
    }
    //Get All revervations for approved clients
    public function clientReservations()
    {
        return $this->hasManyThrough(
            Reservation::class,
            Client::class,
            'receptionist_id', // Foreign key on clients table
            'client_id', // Foreign key on reservations table
            'id', // Local key on receptionists table
            'id' // Local key on clients table
        )->whereNotNull('receptionist_client.approved_at');
    }

    // Check if receptionist has approved a specific client
    public function hasApprovedClient(Client $client): bool
    {
        return $this->approvedClients()
            ->where('client_id', $client->id)
            ->exists();
    }
 
  
   

}
