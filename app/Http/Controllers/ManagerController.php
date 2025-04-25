<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manager\StoreManagerRequest;
use App\Http\Requests\Manager\UpdateManagerRequest;
use App\Models\Manager;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;

class ManagerController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Manager::class);
        $user = Auth::user();

        // Cache user roles and permissions
        $userRoleData = cache()->remember('user_role_data_' . $user->id, 3600, function () use ($user) {
            return [
                'is_admin' => $user->hasRole('Admin'),
            ];
        });

        $perPage = min(request()->input('per_page', 10), 100);
        $search = request()->input('search');
        $sort = request()->input('sort', '-created_at');

        $cacheKey = 'managers_' . md5(json_encode([
            'page' => request()->input('page', 1),
            'per_page' => $perPage,
            'search' => $search,
            'sort' => $sort,
            'user' => $user->id,
        ]));

        $managers = cache()->remember($cacheKey, 60, function () use ($perPage, $search, $sort, $user) {
            $query = QueryBuilder::for(User::class)
                ->where('profile_type', Manager::class)
                ->with('profile')
                ->allowedSorts([
                    'name',
                    'email',
                    'created_at',
                ])
                ->defaultSort($sort)
                ->when($search, function ($query) use ($search) {
                    $search = "%{$search}%";
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search)
                            ->orWhereHas('profile', function ($q) use ($search) {
                                $q->where('national_id', 'like', $search)
                                    ->orWhere('phone_number', 'like', $search);
                            });
                    });
                });

            return $query->paginate($perPage)
                ->through(function ($user) {
                    return [
                        'id' => $user->profile->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'national_id' => $user->profile->national_id,
                        'phone_number' => $user->profile->phone_number,
                        'avatar_image' => $user->profile->avatar_image ? '/storage/' . $user->profile->avatar_image : '/storage/avatars/Default.png',
                        'created_at' => $user->created_at->format('Y-m-d H:i'),
                        'can_edit' => Auth::user()->can('update', $user->profile),
                        'can_delete' => Auth::user()->can('delete', $user->profile),
                        'show_actions' => Auth::user()->can('update', $user->profile),
                    ];
                });
        });

        return Inertia::render('Managers/Index', [
            'managers' => $managers,
            'filters' => ['search' => $search, 'sort' => $sort],
            'can' => [
                'create_managers' => Auth::user()->can('create', Manager::class),
            ],
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
        } else {
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

        $avatarPath = $manager->avatar_image
            ? '/storage/' . $manager->avatar_image
            : '/storage/avatars/Default.png';

        return Inertia::render('Managers/Show', [
            'manager' => [
                'id' => $manager->id,
                'national_id' => $manager->national_id,
                'phone_number' => $manager->phone_number,
                'avatar_image' => $avatarPath,
                'user' => [
                    'name' => $manager->user->name,
                    'email' => $manager->user->email,
                ]
            ],
            'can' => [
                'update' => Auth::user()->can('update', $manager)
            ]
        ]);
    }

    public function edit(Manager $manager)
    {
        $this->authorize('update', $manager);

        $avatarPath = $manager->avatar_image
            ? '/storage/' . $manager->avatar_image
            : '/storage/avatars/Default.png';

        return Inertia::render('Managers/Edit', [
            'manager' => [
                'id' => $manager->id,
                'national_id' => $manager->national_id,
                'phone_number' => $manager->phone_number,
                'avatar_image' => $avatarPath,
                'name' => $manager->user->name,
                'email' => $manager->user->email,
            ]
        ]);
    }

    public function update(UpdateManagerRequest $request, Manager $manager)
    {
        $this->authorize('update', $manager);

        if ($request->hasFile('avatar_image')) {
            if ($manager->avatar_image && $manager->avatar_image !== 'avatars/Default.png') {
                Storage::disk('public')->delete($manager->avatar_image);
            }
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
            $manager->avatar_image = $avatarPath;
        } else {
            $manager->avatar_image = $manager->avatar_image ?: 'avatars/Default.png';
        }

        $manager->update([
            'national_id' => $request->national_id,
            'phone_number' => $request->phone_number,
            'avatar_image' => $manager->avatar_image
        ]);
        if ($manager->user) {
            $manager->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('managers.index')->with('success', 'Manager updated successfully.');
    }

    public function destroy(Manager $manager)
    {
        $this->authorize('delete', $manager);

        if ($manager->user) {
            $manager->user->delete();
        }

        return redirect()->route('managers.index')->with('success', 'User deleted successfully.');

    }
    public function dashboard()
    {
        $this->authorize('showDashboard', Manager::class);

        return Inertia::render('Managers/Dashboard');
    }
}