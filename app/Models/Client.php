<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'avatar_image',
        'phone_number',
        'gender',
        'country',
    ];
    public $timestamps = false;

}