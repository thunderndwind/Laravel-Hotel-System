<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Reservation;

class Receptionist extends Authenticatable
{
    use HasFactory;
    public $timestamps = false;


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
    // Get Clients
            public function clients()
        {
            return $this->hasMany(Client::class);
        }
    // Get All pending clients
    public function pendingClients()
{
    return $this->hasMany(Client::class)->where('status', 'pending');
}

    // Get All approved clients
    public function approvedClients()
{
    return $this->hasMany(Client::class)->where('status', 'approved');
}
    //Get All revervations for approved clients
    // public function clientReservations()
    // {
    //     return $this->hasManyThrough(Reservation::class, Client::class);
    // }

    // Check if receptionist has approved a specific client
    public function hasApprovedClient(Client $client): bool
    {
        return $this->approvedClients()
            ->where('id', $client->id)  // Check the 'id' of the client, not 'client_id'
            ->exists();
    }
 
  
   

}
