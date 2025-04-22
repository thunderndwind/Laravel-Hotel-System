<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
         app()[PermissionRegistrar::class]->forgetCachedPermissions();


         $roles = ['Admin', 'Manager', 'Client', 'Receptionist'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }


         
        $clientPermissions = [
            'view rooms',
            'make reservation',
            'view reservations',
        ];
        
        $receptionistPermissions = [
            'approve reservation',
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view rooms',
            'view reservations',
        ];


        $managerPermissions = [
            'create rooms',
            'edit rooms',
            'delete rooms',
            'view floors',
            'create floors',
            'edit floors',
            'delete floors',
            'view managers',
            'create managers',
            'edit managers',
            'delete managers',
            ...$receptionistPermissions
        ];


        $allPermissions = array_unique([
            ...$managerPermissions,
            ...$receptionistPermissions,
            ...$clientPermissions,
            'restore managers',
        ]);
    
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

    
        Role::findByName('Manager')->givePermissionTo(...$managerPermissions);
        Role::findByName('Receptionist')->givePermissionTo(...$receptionistPermissions);
        Role::findByName('Client')->givePermissionTo(...$clientPermissions);


        $adminPermissions = Permission::where('name', '!=', 'make reservation')->get();
        Role::findByName('Admin')->syncPermissions($adminPermissions);
=======
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
>>>>>>> 4c8abcc (Add crud for recep)

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'receptionist']);
        Role::create(['name' => 'client']);
    }
}
