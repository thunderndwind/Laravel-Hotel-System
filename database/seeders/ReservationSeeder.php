<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get client users
        $clientUsers = User::role('Client')->get();

        if ($clientUsers->isEmpty()) {
            // No client users, so create some sample data
            echo "No client users found. Please run ClientSeeder first.\n";
            return;
        }

        // Get all rooms
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            echo "No rooms found. Please run RoomSeeder first.\n";
            return;
        }

        // Create some past, current, and future reservations
        $today = Carbon::today();

        // Create 5 past reservations
        for ($i = 0; $i < 5; $i++) {
            $user = $clientUsers->random();
            $room = $rooms->random();
            $checkIn = $today->copy()->subDays(rand(30, 60));
            $checkOut = $checkIn->copy()->addDays(rand(1, 7));

            Reservation::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'paid_price' => $room->price * $checkIn->diffInDays($checkOut),
                'accompany_number' => rand(0, $room->capacity - 1),
            ]);
        }

        // Create 3 current reservations
        for ($i = 0; $i < 3; $i++) {
            $user = $clientUsers->random();
            $room = $rooms->random();
            $checkIn = $today->copy()->subDays(rand(1, 5));
            $checkOut = $today->copy()->addDays(rand(1, 5));

            Reservation::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'paid_price' => $room->price * $checkIn->diffInDays($checkOut),
                'accompany_number' => rand(0, $room->capacity - 1),
            ]);
        }

        // Create 7 future reservations
        for ($i = 0; $i < 7; $i++) {
            $user = $clientUsers->random();
            $room = $rooms->random();
            $checkIn = $today->copy()->addDays(rand(5, 30));
            $checkOut = $checkIn->copy()->addDays(rand(1, 10));

            Reservation::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'paid_price' => $room->price * $checkIn->diffInDays($checkOut),
                'accompany_number' => rand(0, $room->capacity - 1),
            ]);
        }
    }
}
