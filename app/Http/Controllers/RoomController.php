<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\Room;
use App\Models\User;
use App\Models\Floor;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;

class RoomController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Room::class);
        $user = Auth::user();

        // Cache user roles and permissions
        $userRoleData = cache()->remember('user_role_data_' . $user->id, 3600, function () use ($user) {
            return [
                'is_admin' => $user->hasRole('Admin'),
                'is_manager' => $user->hasRole('Manager'),
            ];
        });

        // Get and validate request parameters
        $perPage = min(request()->input('per_page', 10), 100);
        $search = request()->input('search');
        $sort = request()->input('sort', '-created_at');

        // Cache key for query results
        $cacheKey = 'rooms_' . md5(json_encode([
            'page' => request()->input('page', 1),
            'per_page' => $perPage,
            'search' => $search,
            'sort' => $sort,
            'user' => $user->id,
            'admin' => $userRoleData['is_admin'] ?? false,
        ]));

        $rooms = cache()->remember($cacheKey, 60, function () use ($user, $perPage, $search, $sort, $userRoleData) {
            $query = QueryBuilder::for(Room::class)
                ->select([
                    'rooms.id',
                    'rooms.number',
                    'rooms.price',
                    'rooms.capacity',
                    'rooms.manager_id',
                    'rooms.floor_id',
                    'rooms.created_at',
                    'rooms.deleted_at'
                ])
                ->leftJoin('users', 'rooms.manager_id', '=', 'users.id')
                ->leftJoin('floors', 'rooms.floor_id', '=', 'floors.id')
                ->addSelect([
                    'users.name as manager_name',
                    'floors.name as floor_name'
                ])
                ->allowedSorts([
                    'number',
                    'price',
                    'capacity',
                    'created_at',
                    AllowedSort::callback('manager_name', function ($query, $direction) {
                        $query->orderBy('users.name', $direction === 'desc' ? 'desc' : 'asc');
                    }),
                    AllowedSort::callback('floor_name', function ($query, $direction) {
                        $query->orderBy('floors.name', $direction === 'desc' ? 'desc' : 'asc');
                    })
                ])
                ->defaultSort($sort)
                ->when($search, function ($query) use ($search) {
                    $search = "%{$search}%";
                    $query->where(function ($q) use ($search) {
                        $q->where('rooms.number', 'like', $search)
                            ->orWhere('rooms.price', 'like', $search)
                            ->orWhere('rooms.capacity', 'like', $search)
                            ->orWhere('users.name', 'like', $search)
                            ->orWhere('floors.name', 'like', $search);
                    });
                });

            return $query->paginate($perPage)
                ->through(function ($room) use ($userRoleData, $user) {
                    $isManager = $userRoleData['is_manager'] ?? false;
                    $isAdmin = $userRoleData['is_admin'] ?? false;
                    $isManagerOwner = $isManager && $room->manager_id === $user->id;
                    $hasAccess = $isAdmin || $isManagerOwner;

                    return [
                        'id' => $room->id,
                        'number' => $room->number,
                        'price' => $room->getPriceInDollarsAttribute(),
                        'capacity' => $room->capacity,
                        'manager' => $isAdmin ? ($room->manager_name ?? 'None') : null,
                        'floor' => $room->floor_name ?? 'None',
                        'created_at' => $room->created_at->format('Y-m-d H:i'),
                        'can_edit' => $hasAccess,
                        'can_delete' => $hasAccess,
                        'show_actions' => $hasAccess,
                        'deleted_at' => $room->deleted_at,
                        'can_restore' => $room->deleted_at && $isAdmin
                    ];
                });
        });

        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
            'isAdmin' => $userRoleData['is_admin'] ?? false,
            'filters' => ['search' => $search, 'sort' => $sort],
            'can' => [
                'create_rooms' => ($userRoleData['is_manager'] ?? false) || ($userRoleData['is_admin'] ?? false),
                'restore_rooms' => $userRoleData['is_admin'] ?? false
            ],
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
                'price' => $room->getPriceInDollarsAttribute(),
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
