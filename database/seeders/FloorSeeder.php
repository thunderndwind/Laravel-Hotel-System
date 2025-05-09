<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\User;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        // Get all managers (users with Manager role)
        $managers = User::role('Manager')->get();

        if ($managers->isEmpty()) {
            $this->command->warn('No managers found. Please run UserSeeder first.');
            return;
        }

        // Create floors with managers
        foreach ($managers as $manager) {
            // Create 2-3 floors per manager
            $floorsCount = rand(2, 3);

            for ($i = 1; $i <= $floorsCount; $i++) {
                Floor::create([
                    'name' => "Floor {$i} - {$manager->name}",
                    'manager_id' => $manager->id,
                    // number will be auto-generated by the model
                ]);
            }
        }

        $this->command->info('Floors seeded successfully!');
    }
}
