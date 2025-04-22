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
      
        'national_id',
        'avatar_image',
        'phone_number',

    ];
    protected $hidden = [
        'password',
    ];
    //=========== Receptionist profile belongs to one user account ===========
    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }
    //=========== Receptionist profile belongs to one manager account ===========
    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }
    //=========== Receptionist profile has many reservations ===========
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'receptionist_id');
    }
    //=========== Receptionist profile has many clients ===========
    public function clients()
    {
        return $this->hasMany(Client::class, 'receptionist_id');

    }
    //================ Receptionist Approved clients ================
    public function approvedClients()
    {
        return $this->hasMany(Client::class, 'receptionist_id')->wherenotNull('approved_at');
    }
    //================ Receptionist Pending clients ================
    public function pendingClients()
    {
        return $this->hasMany(Client::class, 'receptionist_id')->whereNull('approved_at');
    }
    //================ Receptionist Approved reservations ================
    public function approvedReservations()
    {
        return $this->hasMany(Reservation::class, 'receptionist_id')->wherenotNull('approved_at');
    }
    //================ Receptionist Pending reservations ================
    public function pendingReservations()
    {
        return $this->hasMany(Reservation::class, 'receptionist_id')->whereNull('approved_at');
    }
    
   
   
   
}
