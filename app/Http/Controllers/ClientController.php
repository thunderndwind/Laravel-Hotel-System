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



class ClientController extends Controller
{
    use AuthorizesRequests;


    //============== Display a listing of clients based on user role ==============
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin sees all clients
        if ($user->isAdmin()) {
            $clients = Client::with(['user', 'approver'])->paginate(10);
        }
        // Manager sees all clients
        elseif ($user->isManager()) {
            $clients = Client::with(['user', 'approver'])->paginate(10);
        }
        // Receptionist sees only unapproved clients
        elseif ($user->isReceptionist()) {
            $clients = Client::whereNull('approved_at')
                ->with('user')
                ->paginate(10);
        }

        // return view('clients.index', compact('clients'));
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'can' => [
                'create' => $user->can('create', Client::class),
                'approve' => $user->can('approve', Client::class),
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

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar_image')) {
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
        }


        // Create client profile
        $client = Client::create([
            'phone_number' => $validated['phone_number'],
            'gender' => $validated['gender'],
            'country' => $validated['country'],
            'avatar_image' => $avatarPath,
        ]);

        // Save the client profile to the database
        $client->save();

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'profile_type' => Client::class,
            'profile_id' => $client->id,
        ]);




        // Attach client profile to user
        $user->profile()->associate($client)->save();

        // Assign client role
        $user->assignRole('client');

        return redirect()->route('login')->with('success', 'Registration successful! Please wait for approval.');
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
    public function update(UpdateClientRequest $request, Client $client)
    {
        //
    }

    // ============== Remove the specified resource from storage ==============
    public function destroy(Client $client)
    {
        //
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
}