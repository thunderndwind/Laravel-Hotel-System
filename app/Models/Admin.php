<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;


class Admin extends Model
{
    use HasRoles;

    protected $guard_name = 'admin';

    protected $fillable = [];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }
}
