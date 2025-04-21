<?php

namespace App\Http\Controllers;

use App\Http\Requests\Floor\StoreFloorRequest;
use App\Http\Requests\Floor\UpdateFloorRequest;
use App\Models\Floor;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FloorController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', arguments: Floor::class);

        return Inertia::render('Floors/Index', [
            'floors' => Floor::with('manager')
                ->paginate(10)
                ->through(fn($floor) => [
                    'id' => $floor->id,
                    'name' => $floor->name,
                    'number' => $floor->number,
                    'manager' => $floor->manager->name,
                    'can_edit' => Auth::user()->can('update', $floor)
                ])
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
}
