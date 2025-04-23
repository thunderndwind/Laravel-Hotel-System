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



        $clientPermissions = [
            'view rooms',
            'make reservation',
            'view reservations',
            'update own reservations',
            'cancel own reservations',
        ];

        $receptionistPermissions = [
            'approve reservation',
            'update reservations',
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
            'view receptionists',
            'create receptionists',
            'edit receptionists',
            'ban receptionists',
            'unban receptionists',
            ...$receptionistPermissions
        ];


        $allPermissions = array_unique([
            ...$managerPermissions,
            ...$receptionistPermissions,
            ...$clientPermissions,
            'restore managers',
            'restore reservations',
        ]);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        Role::findByName('Manager')->givePermissionTo(...$managerPermissions);
        Role::findByName('Receptionist')->givePermissionTo(...$receptionistPermissions);
        Role::findByName('Client')->givePermissionTo(...$clientPermissions);


        $adminPermissions = Permission::where('name', '!=', 'make reservation')
            ->where('name', '!=', 'update own reservations')
            ->where('name', '!=', 'cancel own reservations')
            ->get();
        Role::findByName('Admin')->syncPermissions($adminPermissions);
    }
}
