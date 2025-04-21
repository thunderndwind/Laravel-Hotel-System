<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Receptionist;
use App\Models\Client;
use App\Models\User;

class ReceptionistController extends Controller
{
    public function index()
    {
        $receptionists = Receptionist::all();
        return inertia('Receptionists/Index', [
            'receptionists' => $receptionists
        ]);
    }

    public function create()
    {
        return inertia('Receptionists/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'national_id' => 'required|string|max:50',
            'avatar_image' => 'nullable|image|max:2048',
            'phone_number' => 'required|string|max:20',
        ]);

        $receptionist = Receptionist::create($request->all());
        $receptionist->assignRole('Receptionist');

        return redirect()->route('receptionists.index');
    }

    public function show(Receptionist $receptionist)
    {
        return inertia('Receptionists/Show', [
            'receptionist' => $receptionist
        ]);
    }

    public function edit(Receptionist $receptionist)
    {
        return inertia('Receptionists/Edit', [
            'receptionist' => $receptionist
        ]);
    }

    public function update(Request $request, Receptionist $receptionist)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $receptionist->id,
            'password' => 'nullable|string|min:8|confirmed',
            'national_id' => 'required|string|max:50',
            'avatar_image' => 'nullable|image|max:2048',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($request->has('password')) {
            $request['password'] = bcrypt($request['password']);
        } else {
            unset($request['password']);
        }

        $receptionist->update($request->all());

        return redirect()->route('receptionists.index');
    }

    public function destroy(Receptionist $receptionist)
    {
        $receptionist->delete();
        return redirect()->route('receptionists.index');
    }

   
}