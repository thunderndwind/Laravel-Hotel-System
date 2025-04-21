<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations for the authenticated user.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can access reservations');
        }

        // Get reservations from the user directly (since Reservation belongs to User)
        $query = Reservation::where('user_id', $user->id);

        if ($status) {
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'upcoming') {
                $query->upcoming();
            } elseif ($status === 'completed') {
                $query->completed();
            }
        }

        $reservations = $query->with('room')
            ->orderBy('check_in_date', 'desc')
            ->paginate(10);

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
            'filters' => $request->only('status')
        ]);
    }

    /**
     * Check if a room is available for the given date range.
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $roomId = $request->input('room_id');
        $checkInDate = $request->input('check_in_date');
        $checkOutDate = $request->input('check_out_date');

        $isAvailable = $this->isRoomAvailableForDates($roomId, $checkInDate, $checkOutDate);

        return response()->json([
            'success' => true,
            'available' => $isAvailable
        ]);
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request, $roomId = null)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can make reservations');
        }

        // Check if client can make reservations
        if (!$client->canMakeReservations()) {
            return redirect()->route('dashboard')->with('error', 'Your account must be approved and verified to make reservations');
        }

        $room = null;
        if ($roomId) {
            $room = Room::find($roomId);
            if (!$room) {
                return redirect()->route('rooms.index')->with('error', 'Room not found');
            }
        }

        return Inertia::render('Reservations/Create', [
            'room' => $room,
            'checkInDate' => $request->input('check_in_date'),
            'checkOutDate' => $request->input('check_out_date')
        ]);
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can make reservations');
        }

        // Check if client can make reservations
        if (!$client->canMakeReservations()) {
            return redirect()->route('dashboard')->with('error', 'Your account must be approved and verified to make reservations');
        }

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'accompany_number' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $roomId = $request->input('room_id');
        $checkInDate = $request->input('check_in_date');
        $checkOutDate = $request->input('check_out_date');

        // Check if room is available for the selected dates
        if (!$this->isRoomAvailableForDates($roomId, $checkInDate, $checkOutDate)) {
            return back()->withErrors(['availability' => 'Room is not available for the selected dates']);
        }

        $room = Room::findOrFail($roomId);

        // Check if the number of guests exceeds room capacity
        if ($request->input('accompany_number') > $room->capacity) {
            return back()->withErrors(['capacity' => 'Number of guests exceeds room capacity']);
        }

        // Calculate the number of days
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $numberOfDays = $checkIn->diffInDays($checkOut);

        // Calculate the total price
        $totalPrice = $room->price * $numberOfDays;

        try {
            $reservation = new Reservation();
            $reservation->room_id = $roomId;
            $reservation->user_id = $user->id;
            $reservation->check_in_date = $checkInDate;
            $reservation->check_out_date = $checkOutDate;
            $reservation->accompany_number = $request->input('accompany_number');
            $reservation->paid_price = $totalPrice;
            $reservation->status = 'confirmed';
            $reservation->save();

            return redirect()->route('reservations.show', $reservation->id)
                ->with('success', 'Reservation created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Failed to create reservation: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified reservation.
     */
    public function show($id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can view reservations');
        }

        $reservation = Reservation::with(['room', 'user'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return redirect()->route('reservations.index')
                ->with('error', 'Reservation not found');
        }

        return Inertia::render('Reservations/Show', [
            'reservation' => $reservation
        ]);
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit($id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can edit reservations');
        }

        $reservation = Reservation::with('room')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return redirect()->route('reservations.index')
                ->with('error', 'Reservation not found');
        }

        // Only allow editing for upcoming reservations
        if (Carbon::parse($reservation->check_in_date)->lte(Carbon::today())) {
            return redirect()->route('reservations.show', $reservation->id)
                ->with('error', 'Cannot edit a reservation that has already started or completed');
        }

        return Inertia::render('Reservations/Edit', [
            'reservation' => $reservation
        ]);
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, $id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can update reservations');
        }

        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return redirect()->route('reservations.index')
                ->with('error', 'Reservation not found');
        }

        // Only allow updates for upcoming reservations
        if (Carbon::parse($reservation->check_in_date)->lte(Carbon::today())) {
            return back()->withErrors(['general' => 'Cannot update a reservation that has already started or completed']);
        }

        $validator = Validator::make($request->all(), [
            'check_in_date' => 'sometimes|required|date|after_or_equal:today',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'accompany_number' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $checkInDate = $request->input('check_in_date', $reservation->check_in_date);
        $checkOutDate = $request->input('check_out_date', $reservation->check_out_date);

        // If dates are changed, check availability
        if ($checkInDate != $reservation->check_in_date || $checkOutDate != $reservation->check_out_date) {
            // Check if room is available for the selected dates (excluding current reservation)
            if (!$this->isRoomAvailableForDates($reservation->room_id, $checkInDate, $checkOutDate, $reservation->id)) {
                return back()->withErrors(['availability' => 'Room is not available for the selected dates']);
            }

            // Recalculate price if dates changed
            $checkIn = Carbon::parse($checkInDate);
            $checkOut = Carbon::parse($checkOutDate);
            $numberOfDays = $checkIn->diffInDays($checkOut);
            $reservation->paid_price = $reservation->room->price * $numberOfDays;
        }

        // Update reservation
        $reservation->check_in_date = $checkInDate;
        $reservation->check_out_date = $checkOutDate;

        if ($request->has('accompany_number')) {
            // Check if the number of guests exceeds room capacity
            if ($request->input('accompany_number') > $reservation->room->capacity) {
                return back()->withErrors(['capacity' => 'Number of guests exceeds room capacity']);
            }
            $reservation->accompany_number = $request->input('accompany_number');
        }

        $reservation->save();

        return redirect()->route('reservations.show', $reservation->id)
            ->with('success', 'Reservation updated successfully');
    }

    /**
     * Cancel the specified reservation.
     */
    public function cancel($id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get client profile from user
        $client = $user->profile;

        if (!$client || !($client instanceof \App\Models\Client)) {
            return redirect()->route('dashboard')->with('error', 'Only clients can cancel reservations');
        }

        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reservation) {
            return redirect()->route('reservations.index')
                ->with('error', 'Reservation not found');
        }

        // Only allow cancellation for upcoming reservations
        if (Carbon::parse($reservation->check_in_date)->lte(Carbon::today())) {
            return back()->withErrors(['general' => 'Cannot cancel a reservation that has already started or completed']);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        // Soft delete the reservation
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation cancelled successfully');
    }

    /**
     * Check if a room is available for the given date range.
     */
    private function isRoomAvailableForDates($roomId, $checkInDate, $checkOutDate, $excludeReservationId = null)
    {
        $query = Reservation::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                // Check if there are any overlapping reservations
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                    ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                        $query->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    });
            });

        // Exclude current reservation when updating
        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        // If no conflicting reservations exist, the room is available
        return $query->count() === 0;
    }
}
