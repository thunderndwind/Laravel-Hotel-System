<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class RoomController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
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

        $perPage = min(request()->input('per_page', 10), 100);
        $search = request()->input('search');
        $sort = request()->input('sort', '-created_at');

        $query = QueryBuilder::for(Room::class)
            ->select([
                'rooms.id',
                'rooms.number',
                'rooms.price',
                'rooms.capacity',
                'rooms.manager_id',
                'rooms.floor_id',
                'rooms.created_at',
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

        $rooms = $query->paginate($perPage)
            ->through(function ($room) use ($userRoleData, $user) {
                $isManagerOwner = $userRoleData['is_manager'] && $room->manager_id === $user->id;
                $hasAccess = $userRoleData['is_admin'] || $isManagerOwner;

                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'price' => $room->getPriceInDollarsAttribute(),
                    'capacity' => $room->capacity,
                    'manager' => $userRoleData['is_admin'] ? ($room->manager_name ?? 'None') : null,
                    'floor' => $room->floor_name ?? 'None',
                    'created_at' => $room->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'data' => $rooms->items(),
            'meta' => [
                'current_page' => $rooms->currentPage(),
                'last_page' => $rooms->lastPage(),
                'per_page' => $rooms->perPage(),
                'total' => $rooms->total()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
