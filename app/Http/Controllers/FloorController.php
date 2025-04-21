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

        $perPage = request()->input('per_page', 10);
        $search = request()->input('search');

        $floors = QueryBuilder::for(Floor::class)
            ->allowedFilters([
                AllowedFilter::exact('number'),
                'name',
                AllowedFilter::exact('manager_id'),
            ])
            ->allowedSorts(['name', 'number', 'created_at'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('number', 'like', "%{$search}%");
                });
            })
            ->when($user->hasRole('Manager'), function ($query) use ($user) {
                $query->where('manager_id', $user->id);
            })
            ->with('manager')
            ->paginate($perPage)
            ->through(function ($floor) use ($user) {
                return [
                    'id' => $floor->id,
                    'name' => $floor->name,
                    'number' => $floor->number,
                    'manager' => $user->hasRole('Admin') ? $floor->manager->name : null,
                    'created_at' => $floor->created_at->format('Y-m-d H:i'),
                    'can_edit' => $user->can('update', $floor),
                    'can_delete' => $user->can('delete', $floor),
                    'show_actions' => $user->hasRole('Admin') ||
                        ($user->hasRole('Manager') && $floor->manager_id === $user->id)
                ];
            });

        return Inertia::render('Floors/Index', [
            'floors' => $floors,
            'isAdmin' => $user->hasRole('Admin'),
            'filters' => array_merge(
                request()->only(['filter', 'sort']),
                ['search' => $search]
            ),
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
            return redirect()->back()->with('error', $e->getMessage());
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