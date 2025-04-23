<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Manager;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(RoleAndPermissionSeeder::class);
        $this->call(ManagerSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(ReceptionistSeeder::class);

    }
}
