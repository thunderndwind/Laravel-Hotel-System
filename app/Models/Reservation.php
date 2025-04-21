<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'paid_price',
        'accompany_number',
        'check_in_date',
        'check_out_date',
        'user_id',
        'room_id',
        'status'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('check_out_date', '>=', Carbon::today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>', Carbon::today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('check_out_date', '<', Carbon::today());
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            // Reservations that start during the period
            $q->whereBetween('check_in_date', [$startDate, $endDate])
                // Or reservations that end during the period
                ->orWhereBetween('check_out_date', [$startDate, $endDate])
                // Or reservations that span the entire period
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('check_in_date', '<=', $startDate)
                        ->where('check_out_date', '>=', $endDate);
                });
        });
    }

    // Methods

    public function getDurationAttribute()
    {
        return Carbon::parse($this->check_in_date)->diffInDays(Carbon::parse($this->check_out_date));
    }

    public function getTotalPriceAttribute()
    {
        return $this->paid_price;
    }

    public function getStatusAttribute()
    {
        $today = Carbon::today();

        if ($this->check_out_date < $today) {
            return 'completed';
        } elseif ($this->check_in_date <= $today && $this->check_out_date >= $today) {
            return 'active';
        } else {
            return 'upcoming';
        }
    }

    public function cancel()
    {
        // Only allow cancellation for upcoming reservations
        if ($this->status !== 'upcoming') {
            return false;
        }

        $this->delete();
        return true;
    }
}
