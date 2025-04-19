<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;


class Floor extends Model
{
    /** @use HasFactory<\Database\Factories\FloorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'number', 'manager_id'];

    // public function manager()
    // {
    //     return $this->belongsTo(BaseUser::class)
    //         ->whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'Manager']));
    // }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($floor) {
            $floor->number = self::generateFloorNumber();
        });
    }

    private static function generateFloorNumber()
    {
        $lastNumber = self::withTrashed()->max('number') ?? 999;
        return $lastNumber + 1; // Ensures 4-digit minimum
    }
}
