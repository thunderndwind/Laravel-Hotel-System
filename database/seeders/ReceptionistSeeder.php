<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $receptionist = Receptionist::firstOrCreate(
            [
                'national_id' => '123456541',
                'phone_number' => '0123456789',
                'avatar_image' => 'image.png',
            ]
        );

        $receptionistUser = User::firstOrCreate(
            ['email' => 'receptionist@gmail.com'],
            [
                'name' => 'receptionist',
                'password' => '12345678',
                'profile_type' => Receptionist::class,
                'profile_id' => $receptionist->id,
            ]
        );

        $receptionistUser->assignRole('Receptionist');
    }
}
