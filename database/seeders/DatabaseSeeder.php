<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Manager;
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

        $roles = ['admin', 'manager', 'client', 'receptionist'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $permissions = [
            'manage-rooms',
            'approve-reservation',
            'manage-floors',
            'manage-receptionists',
            'manage-clients',
            'view-reservations'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        Role::findByName('manager')->givePermissionTo([
            'manage-rooms',
            'approve-reservation',
            'manage-floors',

        ]);

        Role::findByName('receptionist')->givePermissionTo([
            'approve-reservation',
            'view-reservations'
        ]);

        Role::findByName('admin')->givePermissionTo(Permission::all());


        $this->call(ManagerSeeder::class);
    }
}
