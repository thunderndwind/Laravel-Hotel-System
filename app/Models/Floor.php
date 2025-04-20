<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Floor extends Model
{
    /** @use HasFactory<\Database\Factories\FloorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'number', 'manager_id'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

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

    public function scopeWithValidManager($query)
    {
        return $query->whereHas('manager', fn($q) => $q->role(['Admin', 'Manager']));
    }
}
