<?php

namespace App\Http\Controllers;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Manager\StoreManagerRequest;
use App\Http\Requests\Manager\UpdateManagerRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ManagerController extends Controller
{
    use AuthorizesRequests;


    public function index()
    {
        $this->authorize('viewAny', Manager::class);

        $managers = User::where('profile_type', Manager::class)
            ->with('profile')   
            ->paginate(10)
            ->through(fn($user) => [
                'id' => $user->profile->id,
                'name' => $user->name,
                'email' => $user->email,
                'national_id' => $user->profile->national_id,
                'phone_number' => $user->profile->phone_number,
                'avatar_image' => $user->profile->avatar_image ? '/storage/' . $user->profile->avatar_image : '/storage/avatars/Default.png',
                'can_edit' => Auth::user()->can('update', $user->profile),
            ]);

        return Inertia::render('Managers/Index', [
            'managers' => $managers
        ]);
    }

    public function create()
    {
        $this->authorize('create', Manager::class);

        return Inertia::render('Managers/Create');
    }

    public function store(StoreManagerRequest $request)
    {
        $this->authorize('create', Manager::class);

        $avatarPath = null;
        if ($request->hasFile('avatar_image')) {
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
        }
        else{
            $avatarPath = 'avatars/Default.png';
        }
        

        $manager = Manager::create(array_merge(
            $request->validated(),
            ['avatar_image' => $avatarPath]
        ));

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->profile()->associate($manager);
        $user->save();
        $user->assignRole('Manager');

        return redirect()->route('managers.index');
    }

    public function show(Manager $manager)
    {
        $this->authorize('view', $manager);

        return Inertia::render('Managers/Show', [
            'manager' => $manager->load('user')
        ]);
    }

    public function edit(Manager $manager)
    {
        $this->authorize('update', $manager);

        return Inertia::render('Managers/Edit', [
            'manager' => [
                'id' => $manager->id,
                'national_id' => $manager->national_id,
                'phone_number' => $manager->phone_number,
                'avatar_image' => $manager->avatar_image,
                'name' => $manager->user->name,
                'email' => $manager->user->email,
            ]
        ]);
    }

    public function update(UpdateManagerRequest $request, Manager $manager)
    {
        $this->authorize('update', $manager);

        $manager->update($request->validated());

        if ($manager->user) {
            $manager->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('managers.index');
    }

    public function destroy(Manager $manager)
    {
        $this->authorize('delete', $manager);

        if ($manager->user) {
            $manager->user->delete();
        }

        return redirect()->route('managers.index')->with('success', 'User deleted successfully.');

    }
}