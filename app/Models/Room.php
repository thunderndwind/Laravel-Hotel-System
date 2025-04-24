<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['number', 'price', 'capacity', 'manager_id', 'floor_id'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            $room->number = self::generateRoomNumber();
            $room->price = $room->price ? $room->price * 100 : 0;
        });
    }

    private static function generateRoomNumber(): int
    {
        $lastNumber = self::withTrashed()->max('number') ?? 1000;
        return $lastNumber + 1;
    }
}
