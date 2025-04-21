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
         app()[PermissionRegistrar::class]->forgetCachedPermissions();


         $roles = ['Admin', 'Manager', 'Client', 'Receptionist'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

         $permissions = [
            'manage rooms',
            'view rooms',
            'approve reservation',
            'manage floors',
            'manage receptionists',
            'manage clients',
            'view reservations'
         ];
 
         foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
         }
 
         Role::findByName('Manager')->givePermissionTo([
            'view rooms',
            'manage rooms',
            'approve reservation',
            'manage floors',
         ]);


         Role::findByName('Receptionist')->givePermissionTo([
            'approve reservation',
            'view reservations',
            'view rooms'
        ]);

        Role::findByName('Client')->givePermissionTo([
            'view rooms'
        ]);

        Role::findByName('Admin')->givePermissionTo(Permission::all());

    }
}
