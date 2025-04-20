<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Client;

class RegistrationTest extends TestCase
{
    public function test_user_registration()
    {
        Storage::fake('public');
        
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone_number' => '1234567890',
            'gender' => 'male',
            'country' => 'USA',
            'avatar_image' => UploadedFile::fake()->image('avatar.jpg')
        ]);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
        
        // Assert client was created
        $this->assertDatabaseHas('clients', [
            'phone_number' => '1234567890'
        ]);
        
        // Assert relationship exists
        $user = User::where('email', 'test@example.com')->first();
        $this->assertInstanceOf(Client::class, $user->profile);
        
        // Assert file was stored
        $this->assertFileExists(Storage::disk('public')->path('avatars/'.$user->profile->avatar_image));
        
        // Assert redirect
        $response->assertRedirect('/dashboard');
    }
}