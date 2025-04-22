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

        $manager = Manager::firstOrCreate(
            ['national_id' => '567895432'],
            [
                'phone_number' => '01156755044',
                'avatar_image' => 'manager2.jpg',
            ]
        );
    
        $managerUser = User::firstOrCreate(
            ['email' => 'nourenshallah@gmail.com'],
            [
                'name' => 'nouran',
                'password' => Hash::make('12345678'),
                'profile_type' => Manager::class,
                'profile_id' => $manager->id
            ]
        );
    
        $managerUser->assignRole('Manager');
    }
}