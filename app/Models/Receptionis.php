<?php
// app/Models/Manager.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'national_id',
        'avatar_image',
        
    ];

    protected $hidden = [
        'password',
    ];

    // Accessor for avatar image
    public function getAvatarImageAttribute($value)
    {
        return $value ?? 'default-avatar.jpg'; // fallback if null
    }
}
