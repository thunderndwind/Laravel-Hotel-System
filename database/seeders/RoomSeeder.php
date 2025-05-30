<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $floors = Floor::with('manager')->get();

        if ($floors->isEmpty()) {
            $this->command->warn('No floors found. Please run FloorSeeder first.');
            return;
        }

        foreach ($floors as $floor) {
            // Create 4-6 rooms per floor
            $roomsCount = rand(4, 6);

            for ($i = 1; $i <= $roomsCount; $i++) {
                Room::create([
                    'price' => rand(100, 500), // Random price between 100 and 500
                    'capacity' => rand(1, 4), // Random capacity between 1 and 4
                    'floor_id' => $floor->id,
                    'manager_id' => $floor->manager_id, // Same manager as the floor
                    // number will be auto-generated by the model
                ]);
            }
        }

        $this->command->info('Rooms seeded successfully!');
    }
}
