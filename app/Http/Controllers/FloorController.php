<?php

namespace App\Http\Controllers;

use App\Http\Requests\Floor\StoreFloorRequest;
use App\Http\Requests\Floor\UpdateFloorRequest;
use App\Models\Floor;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class FloorController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Floor::class);
        $user = Auth::user();

        // Cache user roles and admin status together - more efficient
        $userRoleData = cache()->remember('user_role_data_' . $user->id, 3600, function () use ($user) {
            return [
                'is_admin' => $user->hasRole('Admin'),
                'is_manager' => $user->hasRole('Manager'),
            ];
        });

        // Get and validate request parameters
        $perPage = min(request()->input('per_page', 10), 100); // Limit max per page
        $search = request()->input('search');
        $sort = request()->input('sort', '-created_at');

        // Simplified cache key
        $cacheKey = 'floors_' . md5(json_encode([
            'page' => request()->input('page', 1),
            'per_page' => $perPage,
            'search' => $search,
            'sort' => $sort,
            'user' => $user->id,
            'admin' => $userRoleData['is_admin'] ?? false,
        ]));

        $floors = cache()->remember($cacheKey, 60, function () use ($user, $perPage, $search, $sort, $userRoleData) {
            $query = QueryBuilder::for(Floor::class)
                ->select([
                    'floors.id',
                    'floors.name',
                    'floors.number',
                    'floors.manager_id',
                    'floors.created_at',
                    'floors.deleted_at'
                ])
                ->when($userRoleData['is_admin'] ?? false, function ($query) {
                    $query->leftJoin('users', 'floors.manager_id', '=', 'users.id')
                        ->addSelect('users.name as manager_name');
                })
                ->allowedSorts([
                    'name',
                    'number',
                    'created_at',
                    AllowedSort::callback('manager_name', function ($query, $direction) {
                        $query->orderBy('users.name', $direction === 'desc' ? 'desc' : 'asc');
                    })
                ])
                ->defaultSort($sort)
                ->when($search, function ($query) use ($search) {
                    $search = "%{$search}%";
                    $query->where(function ($q) use ($search) {
                        $q->where('floors.name', 'like', $search)
                            ->orWhere('floors.number', 'like', $search)
                            ->when($userRoleData['is_admin'] ?? false, function ($query) use ($search) {
                                $query->orWhere('users.name', 'like', $search);
                            });
                    });
                });

            return $query->paginate($perPage)
                ->through(function ($floor) use ($userRoleData, $user) {
                    $isManager = $userRoleData['is_manager'] ?? false;
                    $isAdmin = $userRoleData['is_admin'] ?? false;
                    $isManagerOwner = $isManager && $floor->manager_id === $user->id;
                    $hasAccess = $isAdmin || $isManagerOwner;

                    return [
                        'id' => $floor->id,
                        'name' => $floor->name,
                        'number' => $floor->number,
                        'manager' => $isAdmin ? ($floor->manager_name ?? 'None') : null,
                        'created_at' => $floor->created_at->format('Y-m-d H:i'),
                        'can_edit' => $hasAccess,
                        'can_delete' => $hasAccess,
                        'show_actions' => $hasAccess,
                        'deleted_at' => $floor->deleted_at,
                        'can_restore' => $floor->deleted_at && $isAdmin
                    ];
                });
        });

        return Inertia::render('Floors/Index', [
            'floors' => $floors,
            'isAdmin' => $userRoleData['is_admin'] ?? false,
            'filters' => ['search' => $search, 'sort' => $sort],
            'can' => [
                'create_floors' => ($userRoleData['is_manager'] ?? false) || ($userRoleData['is_admin'] ?? false),
                'restore_floors' => $userRoleData['is_admin'] ?? false
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Floor::class);

        return Inertia::render('Floors/Create', [
            'managers' => User::whereHas(
                'roles',
                fn($q) =>
                $q->whereIn('name', ['Admin', 'Manager'])
            )->get(['id', 'name'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFloorRequest $request)
    {
        $this->authorize('create', Floor::class);

        $floor = Floor::create($request->validated());

        return redirect()
            ->route('floors.index')
            ->with('success', 'Floor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Floor $floor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Floor $floor)
    {
        $this->authorize('update', $floor);

        return Inertia::render('Floors/Edit', [
            'floor' => [
                'id' => $floor->id,
                'name' => $floor->name,
                'number' => $floor->number,
                'manager_id' => $floor->manager_id,
                'manager_name' => $floor->manager ? $floor->manager->name : null,
            ],
            'managers' => User::whereHas(
                'roles',
                fn($q) =>
                $q->whereIn('name', ['Admin', 'Manager'])
            )->get(['id', 'name'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFloorRequest $request, Floor $floor)
    {
        $this->authorize('update', $floor);

        $floor->update($request->validated());

        return redirect()
            ->route('floors.index')
            ->with('success', 'Floor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Floor $floor)
    {
        try {
            $this->authorize('delete', $floor);

            $floor->delete();

            return redirect()
                ->route('floors.index')
                ->with('success', 'Floor deleted successfully.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'An unexpected error occurred.');
        }
    }

    /**
     * Restore the specified soft-deleted floor.
     */
    public function restore($id)
    {
        try {
            $floor = Floor::withTrashed()->findOrFail($id);

            $this->authorize('restore', $floor);

            $floor->restore();

            return redirect()
                ->route('floors.index')
                ->with('success', 'Floor restored successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Floor not found.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
