<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    /** @use HasFactory<\Database\Factories\ManagerFactory> */
    use HasFactory;


    //=========== mass assignable ===========
    protected $fillable = [
        'national_id',
        'phone_number',
        'avatar_image',
    ];


    //=========== Manager profile belongs to one user account ===========
    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    //===========  one-to-many relationship between Manager and Floor===========
    public function floors()
    {
        return $this->hasMany(Floor::class, 'manager_id');
    }


    //=========== one-to-many relationship between Manager and Room ===========
    public function rooms()
    {
        return $this->hasMany(Room::class, 'manager_id');
    }



    // =========== Get all receptionists created by this manager ===========
    //   public function receptionists()
    //  {
    //     return $this->hasManyThrough(
    //         Receptionist::class,
    //         User::class,
    //         'profile_id', // Foreign key on users table
    //         'id',         // Foreign key on receptionists table
    //         'id',         // Local key on managers table
    //         'id'          // Local key on users table
    //     );
    //  }

    // Defines the polymorphic relationship for client approvals
    //=========== A manager can approve multiple clients ===========
    public function approvedClients()
    {
        return $this->morphMany(Client::class, 'approver');
    }


    // =========== Get the manager's name through the user relationship ===========
    public function getNameAttribute()
    {
        return $this->user->name;
    }

    // =========== Get the manager's email through the user relationship===========
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    // =========== Call this to Ban reciptionest ===========  
    //     public function banReceptionist(Receptionist $receptionist)
    //   {
    //     if ($this->receptionists->contains($receptionist)) {
    //         $receptionist->update(['banned_at' => now()]);
    //         return true;
    //     }
    //     return false;
    //   }



    //===========Call this to unBan reciptionist =========== 

    //   public function unbanReceptionist(Receptionist $receptionist)
    // {
    //     if ($this->receptionists->contains($receptionist)) {
    //         $receptionist->update(['banned_at' => null]);
    //         return true;
    //     }
    //     return false;
    // }


    public $timestamps = false;

}