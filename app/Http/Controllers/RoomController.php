<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\Room;
use App\Models\User;
use App\Models\Floor;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Room::class);

        return Inertia::render('Rooms/Index', [
            'rooms' => Room::with(['manager', 'floor'])
                ->paginate(10)
                ->through(fn($room) => [
                    'id' => $room->id,
                    'number' => $room->number,
                    'price' => $room->price,
                    'capacity' => $room->capacity,
                    'manager' => $room->manager->name,
                    'floor' => $room->floor->name,
                    'can_edit' => Auth::user()->can('update', $room),
                    'can_delete' => Auth::user()->can('delete', $room),
                ])
        ]);
    }

    public function create()
    {
        $this->authorize('create', Room::class);

        return Inertia::render('Rooms/Create', [
            'managers' => User::role(['Admin', 'Manager'])->get(['id', 'name']),
            'floors' => Floor::all(['id', 'name']),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        $this->authorize('create', Room::class);

        Room::create($request->validated());

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        $this->authorize('update', $room);

        return Inertia::render('Rooms/Edit', [
            'room' => [
                'id' => $room->id,
                'number' => $room->number,
                'price' => $room->price,
                'capacity' => $room->capacity,
                'manager_id' => $room->manager_id,
                'floor_id' => $room->floor_id,
            ],
            'managers' => User::role(['Admin', 'Manager'])->get(['id', 'name']),
            'floors' => Floor::all(['id', 'name']),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $this->authorize('update', $room);

        $room->update($request->validated());

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        try {
            $this->authorize('delete', $room);

            $room->delete();

            return redirect()
                ->route('rooms.index')
                ->with('success', 'Room deleted successfully.');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
