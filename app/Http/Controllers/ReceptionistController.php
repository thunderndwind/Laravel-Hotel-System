<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Receptionist;
use App\Models\Client;
use App\Models\User;

class ReceptionistController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:receptionists,email',
            'password' => 'required|string|min:8|confirmed',
            'national_id' => 'required|string|max:20',
            'avatar_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone_number' => 'required|string|max:20',
        ]);
        $receptionist = new Receptionist([
            'avatar_image' => $request->file('avatar_image')->store('avatars', 'public'),
            'phone_number' => $request->phone_number,
            'national_id' => $request->national_id,
        ]);
        $receptionist->save();
        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'profile_type' => Receptionist::class,
            'profile_id' => $receptionist->id,
        ]);
        $user->profile()->associate($receptionist)->save();
       
        // Save the user to the database
        $user->save();
        // Handle avatar upload
    
        $receptionist = Receptionist::create($validated);
    
        

    }
    //
    public function testApprovedClients()
    {
        $receptionist = Receptionist::find(1);
    
        if (!$receptionist) {
            return response()->json(['error' => 'Receptionist not found'], 404);
        }
    
        dd($receptionist->approvedClients()->get());
    }
    public function testPendingClients()
    {
        $receptionist = Receptionist::find(1);
    
        if (!$receptionist) {
            return response()->json(['error' => 'Receptionist not found'], 404);
        }
    
        dd($receptionist->pendingClients()->get());
    }
    public function testClientReservations()
    {
        $receptionist = Receptionist::find(1);
    
        if (!$receptionist) {
            return response()->json(['error' => 'Receptionist not found'], 404);
        }
    
        dd($receptionist->clientReservations()->get());
    }
    
}
