<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;
    public $timestamps = false;


    //=========== mass assignable ===========
    protected $fillable = [
        'name', 
        'avatar_image',
         'phone_number', 
         'gender', 
         'country',
         'approver_type', 
         'approver_id', 
         'approved_at', 
         'status',

    ];

    //===========  casts dates to Carbon instances ===========   
    protected $casts = [
        'approved_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // ===========  sets polymorphic relation client can be approved by either a manager or receptionist ===========
    public function approver()
    {
        return $this->morphTo();
    }

    //=========== Each client profile belongs to one user account ===========
    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }


    //=========== Get the client's name through the user relationship ===========
    public function getNameAttribute()
    {
        return $this->user->name;
    }

    //=========== Get the client's email through the user relationship===========
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    //=========== Check if the client is approved===========
    public function getIsApprovedAttribute()
    {
        return !is_null($this->approved_at);
    }



    //=========== Get all reservations for this client===========
    // public function reservations()
    // {
    //     return $this->hasManyThrough(
    //         Reservation::class,
    //         User::class,
    //         'profile_id', // Foreign key on users table
    //         'user_id',    // Foreign key on reservations table
    //         'id',         // Local key on clients table
    //         'id'          // Local key on users table
    //     );
    // }

    //===========Check if client can make reservations===========
    public function canMakeReservations()
    {
        return $this->is_approved && $this->email_verified_at;
    }


    // public function approve(Model $approver)
    // {
    //     $this->update([
    //         'approver_type' => get_class($approver),
    //         'approver_id' => $approver->id,
    //         'approved_at' => now(),
    //     ]);

    //     $this->user->notify(new ClientApprovedNotification);
    // }
    public function receptionist()
{
    return $this->belongsTo(Receptionist::class);
}



}