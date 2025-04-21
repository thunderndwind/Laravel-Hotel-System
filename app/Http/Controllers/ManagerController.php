<?php


namespace App\Http\Controllers;

use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Models\Manager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = Manager::all();
        return Inertia::render('Managers/Index', [
            'managers' => $managers
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Managers/Create');
    }

    public function store(StoreManagerRequest $request)
    {
        $manager = Manager::create($request->validated());
        $manager->assignRole('Manager');

        return redirect()->route('managers.index');
    }

    public function show(Manager $manager)
    {
        return Inertia::render('Managers/Show', [
            'manager' => $manager
        ]);
    }

    public function edit(Manager $manager)
    {
        return Inertia::render('Managers/Edit', [
            'manager' => $manager
        ]);
    }

    public function update(UpdateManagerRequest $request, Manager $manager)
    {
        $manager->update($request->validated());
        return redirect()->route('managers.index');
    }

    public function destroy(Manager $manager)
    {
        $manager->delete();
        return redirect()->route('managers.index');
    }
}
