<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Client; // Ensure the Client model exists in this namespace
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{    public $timestamps = false;
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'password_confirmation' => 'required',
        'avatar_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'phone_number' => 'required|string|max:20',
        'gender' => 'required|in:male,female',
        'country' => 'required|string|max:30',
    ]);

    $avatarImagePath = $request->file('avatar_image')->store('avatars', 'public');

    $client = new Client([
        'avatar_image' => $avatarImagePath,
        'phone_number' => $request->phone_number,
        'gender' => $request->gender,
        'country' => $request->country,
    ]);
    $client->save();
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'profile_type' => Client::class,
        'profile_id' => $client->id,
    ]);
    $user->save();

    $user->assignRole('client'); // Assign the 'client' role to the user
   

    $user->profile()->associate($client)->save();



    event(new Registered($user));
    Auth::login($user);

    return redirect(route('dashboard', absolute: false));
}
}
