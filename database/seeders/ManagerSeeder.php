<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $manager = Manager::create([
            'national_id' => '123456789',
            'phone_number' => '01156755044',
            'avatar_image' => 'manager.jpg',
        ]);
        
        $managerUser = new User([
            'name' => 'Nouran',
            'email' => 'nouran@gmail.com',
            'password' => Hash::make('12345'),
        ]);
        
        $managerUser->profile()->associate($manager);
        $managerUser->save(); 
        
        $managerUser->assignRole('manager');
        $managerUser->givePermissionTo([
            'manage-rooms',
            'approve-reservation',
            'manage-floors',
        ]);
        
    }
}
