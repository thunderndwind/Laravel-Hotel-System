<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Receptionist;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'national_id' => 'required|string|unique:receptionists',
            'phone_number' => 'required|string',
            'avatar_image' => 'nullable'
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                // Create receptionist with base64 image
                $receptionist = Receptionist::create([
                    'national_id' => $validated['national_id'],
                    'phone_number' => $validated['phone_number'],
                    'avatar_image' => isset($validated['avatar_image']) ? 
                        preg_replace('/^data:image\/\w+;base64,/', '', $validated['avatar_image']) : 
                        null
                ]);

                // Create user account
                $user = $receptionist->user()->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password'])
                ]);

                $user->assignRole('receptionist');

                return $receptionist->load('user');
            });

            return redirect()->back()->with([
                'success' => 'Receptionist created successfully',
                'receptionist' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error('Receptionist creation failed: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'Failed to create receptionist: ' . $e->getMessage()
            ]);
        }
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($receptionist->user->id)],
            'national_id' => ['required', 'string', Rule::unique('receptionists')->ignore($receptionist->id)],
            'phone_number' => 'required|string',
            'avatar_image' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated, $receptionist) {
                $receptionist->update([
                    'national_id' => $validated['national_id'],
                    'phone_number' => $validated['phone_number'],
                    'avatar_image' => $validated['avatar_image']
                ]);

                $receptionist->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email']
                ]);
            });

            return redirect()->back()->with('success', 'Receptionist updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Receptionist $receptionist)
    {
        DB::transaction(function () use ($receptionist) {
            if ($receptionist->user) {
                $receptionist->user->delete();
            }
            $receptionist->delete();
        });

        return redirect()->back()->with('success', 'Receptionist deleted successfully.');
    }
}