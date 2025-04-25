<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;





class ReservationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Reservation::class);

        $query = Reservation::with(['room', 'user']);

        if (Auth::user()->hasRole('Client')) {
            $query->where('user_id', Auth::id());
        }

        $reservations = $query->latest()->paginate(10)
            ->through(fn($reservation) => [
                'id' => $reservation->id,
                'room_number' => $reservation->room->number,
                'room_name' => $reservation->room->name ?? "Room {$reservation->room->number}",
                'check_in_date' => $reservation->check_in_date->toDateString(),
                'check_out_date' => $reservation->check_out_date->toDateString(),
                'duration' => $reservation->duration,
                'status' => $reservation->status,
                'paid_price' => $reservation->paid_price,
                'accompany_number' => $reservation->accompany_number,
                'total_guests' => $reservation->accompany_number + 1,
                'client_name' => $reservation->user->name,
                'created_at' => $reservation->created_at->format('Y-m-d H:i'),
                'can_edit' => Auth::user()->can('update', $reservation),
                'can_delete' => Auth::user()->can('delete', $reservation),
            ]);

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Reservation::class);

        // Get available rooms
        $availableRooms = Room::withCount('reservations')
            ->orderBy('number')
            ->get()
            ->map(fn($room) => [
                'id' => $room->id,
                'number' => $room->number,
                'name' => $room->name ?? "Room {$room->number}",
                'price' => $room->price,
                'capacity' => $room->capacity,
                'floor' => $room->floor->name ?? $room->floor->number,
            ]);

        return Inertia::render('Reservations/Create', [
            'rooms' => $availableRooms,
            'userId' => Auth::id(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $this->authorize('create', Reservation::class);

        $validated = $request->validated();

        // Calculate paid price based on room price and duration
        $room = Room::findOrFail($validated['room_id']);
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights = $checkIn->diffInDays($checkOut);

        $validated['paid_price'] = $room->price * $nights;

        $reservation = Reservation::create($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        return Inertia::render('Reservations/Show', [
            'reservation' => [
                'id' => $reservation->id,
                'room' => [
                    'id' => $reservation->room->id,
                    'number' => $reservation->room->number,
                    'name' => $reservation->room->name ?? "Room {$reservation->room->number}",
                    'price' => $reservation->room->price,
                    'capacity' => $reservation->room->capacity,
                    'floor' => $reservation->room->floor->name ?? $reservation->room->floor->number,
                ],
                'user' => [
                    'id' => $reservation->user->id,
                    'name' => $reservation->user->name,
                    'email' => $reservation->user->email,
                ],
                'check_in_date' => $reservation->check_in_date->toDateString(),
                'check_out_date' => $reservation->check_out_date->toDateString(),
                'duration' => $reservation->duration,
                'paid_price' => $reservation->paid_price,
                'accompany_number' => $reservation->accompany_number,
                'total_guests' => $reservation->accompany_number + 1,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at->format('Y-m-d H:i'),
                'updated_at' => $reservation->updated_at->format('Y-m-d H:i'),
                'can_edit' => Auth::user()->can('update', $reservation),
                'can_delete' => Auth::user()->can('delete', $reservation),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        // Get available rooms
        $availableRooms = Room::withCount('reservations')
            ->orderBy('number')
            ->get()
            ->map(fn($room) => [
                'id' => $room->id,
                'number' => $room->number,
                'name' => $room->name ?? "Room {$room->number}",
                'price' => $room->price,
                'capacity' => $room->capacity,
                'floor' => $room->floor->name ?? $room->floor->number,
            ]);

        return Inertia::render('Reservations/Edit', [
            'reservation' => [
                'id' => $reservation->id,
                'room_id' => $reservation->room_id,
                'user_id' => $reservation->user_id,
                'check_in_date' => $reservation->check_in_date->toDateString(),
                'check_out_date' => $reservation->check_out_date->toDateString(),
                'paid_price' => $reservation->paid_price,
                'accompany_number' => $reservation->accompany_number,
            ],
            'rooms' => $availableRooms,
            'users' => Auth::user()->hasRole('Client') ? [] : User::role('Client')->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $validated = $request->validated();

        // Recalculate price if dates or room changed
        if (isset($validated['check_in_date']) || isset($validated['check_out_date']) || isset($validated['room_id'])) {
            $roomId = $validated['room_id'] ?? $reservation->room_id;
            $room = Room::findOrFail($roomId);

            $checkIn = Carbon::parse($validated['check_in_date'] ?? $reservation->check_in_date);
            $checkOut = Carbon::parse($validated['check_out_date'] ?? $reservation->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);

            $validated['paid_price'] = $room->price * $nights;
        }

        $reservation->update($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation cancelled successfully.');
    }



    /**
 * Show available rooms for reservation
 */
public function availableRooms()
{
    $this->authorize('create', Reservation::class);

    $availableRooms = Room::whereDoesntHave('reservations', function($query) {
        $query->where('check_out_date', '>', now());
    })
    ->with('floor')
    ->orderBy('number')
    ->get()
    ->map(function($room) {
        return [
            'id' => $room->id,
            'number' => $room->number,
            'floor_name' => $room->floor->name ?? 'Floor '.$room->floor->number,
            'capacity' => $room->capacity,
            'price' => $room->price,
        ];
    });

    return Inertia::render('Clients/MakeReservation', [
        'availableRooms' => $availableRooms,
    ]);
}
}