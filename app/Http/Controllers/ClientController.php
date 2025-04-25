<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
//------------------------
use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ClientController extends Controller
{
    use AuthorizesRequests;


    //============== Display a listing of clients based on user role ==============
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin sees all clients
        if ($user->hasRole('Admin')) {     //$user->hasRole('Admin'),
            $clients = Client::with(['user', 'approver'])->paginate(10);
        }
        // Manager sees all clients
        elseif ($user->hasRole('Manager')) {
            $clients = Client::with(['user', 'approver'])->paginate(10);
        }
        // Receptionist sees only unapproved clients
        elseif ($user->hasRole('Receptionist')) {
            $clients = Client::whereNull('approved_at')
                ->with('user')
                ->paginate(10);
        } else {
            $clients = Client::whereNull('id');
        }

        // return view('clients.index', compact('clients'));
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'can' => [
                'create' => $user->can('create', Client::class),
                'approve' => $user->can('approveAny', Client::class),
            ]
        ]);
    }


    // ============== Show the form for creating a new client (registration form) ==============
    public function create()
    {
        // $countries = \Countries::all()->pluck('name', 'iso_3166_2');
        // return view('clients.create', compact('countries'));
        $this->authorize('create', Client::class);

        return Inertia::render('Clients/Create', [
            // 'countries' => \Countries::all()->pluck('name', 'iso_3166_2'),
            'genders' => ['male', 'female']
        ]);
    }

    //    * Store a newly created client in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone_number' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'country' => 'required|string|max:30',
            'avatar_image' => 'nullable|image|mimes:jpeg,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

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

            DB::commit();

            return redirect()->route('login')->with('success', 'Registration successful! Please wait for approval.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if transaction fails
            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }

    //===== Display the specified client ==============
    public function show(Client $client)
    {
        $this->authorize('view', $client);

        // $client->load(['user', 'approver', 'reservations.room.floor']);
        // return view('clients.show', compact('client'));

        return Inertia::render('Clients/Show', [
            'client' => $client->load(['user', 'approver', 'reservations.room.floor']),
            'can' => [
                'update' => Auth::user()->can('update', $client),
                'delete' => Auth::user()->can('delete', $client),
            ]
        ]);
    }

    // ==============Show the form for editing the specified client==============
    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        // $countries = \Countries::all()->pluck('name', 'iso_3166_2');
        // return view('clients.edit', compact('client', 'countries'));
        return Inertia::render('Clients/Edit', [
            'client' => $client->load('user'),
            // 'countries' => \Countries::all()->pluck('name', 'iso_3166_2'),
            'genders' => ['male', 'female']
        ]);
    }


    // ============== Update the specified resource in storage ==============
    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($client->user->id),
            ],
            'phone_number' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'country' => 'required|string|max:30',
            'avatar_image' => 'nullable|image|mimes:jpeg,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $oldAvatarPath = $client->avatar_image;
            $newAvatarPath = $oldAvatarPath;

            // Handle avatar upload
            if ($request->hasFile('avatar_image')) {
                $newAvatarPath = $request->file('avatar_image')->store('avatars', 'public');
            }

            // Update user
            $client->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update client
            $client->update([
                'phone_number' => $validated['phone_number'],
                'gender' => $validated['gender'],
                'country' => $validated['country'],
                'avatar_image' => $newAvatarPath,
            ]);

            DB::commit();

            // Delete old avatar after successful update
            if ($request->hasFile('avatar_image') && $oldAvatarPath) {
                Storage::disk('public')->delete($oldAvatarPath);
            }

            return redirect()->route('clients.show', $client)
                ->with('success', 'Client updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete new uploaded file if transaction fails
            if (isset($newAvatarPath)) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            return back()->withInput()->with('error', 'Update failed. Please try again.');
        }
    }

    // ============== Remove the specified resource from storage ==============
    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        // Check if client has reservations
        if ($client->reservations()->exists()) {
            return back()->with(
                'error',
                'Cannot delete client with active reservations. ' .
                'Please delete their reservations first.'
            );
        }

        try {
            DB::beginTransaction();

            $avatarPath = $client->avatar_image;
            $userId = $client->user->id;

            // Delete user (which will cascade to client via morph)
            $client->user()->delete();

            DB::commit();

            // Delete avatar after successful deletion
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }

            return redirect()->route('clients.index')
                ->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Deletion failed. Please try again.');
        }

    }

    ///===================================================== Approval Methods =====================================================================

    //=========== Approve the specified client==============
    public function approve(Request $request, Client $client)
    {
        $this->authorize('approve', $client);

        $approver = $request->user()->profile;

        if (!$client->approved_at) {
            $client->approve($approver);
            // Now notify the client
            $client->notify(new ClientApprovedNotification());

            return back()->with('success', 'Client approved successfully.');
        }

        return back()->with('error', 'Client is already approved.');
    }

    //======== Show client's reservation ==============
    public function reservations(Client $client)
    {
        $this->authorize('viewReservations', $client);

        // $reservations = $client->reservations()
        //     ->with('room.floor')
        //     ->latest()
        //     ->paginate(10);

        // return view('clients.reservations', compact('client', 'reservations'));
        return Inertia::render('Clients/Reservations', [
            'client' => $client,
            'reservations' => $client->reservations()
                ->with('room.floor')
                ->latest()
                ->paginate(10)
        ]);
    }

    //======== Show client  Dashboard  ==============
    public function dashboard()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if user has a client profile
        if (!$user || $user->profile_type !== Client::class) {
            abort(403, 'Unauthorized access');
        }

        // Load the client with relationships
        $client = Client::with(['user', 'reservations.room.floor'])
            ->findOrFail($user->profile_id);

        return Inertia::render('Clients/Dashboard', [
            'client' => $client,
            'pendingReservations' => $client->reservations()->where('status', 'pending')->count(),
            'upcomingReservations' => $client->reservations()
                ->where('check_in_date', '>=', now())
                ->orderBy('check_in_date')
                ->take(3)
                ->get()
        ]);
    }
}