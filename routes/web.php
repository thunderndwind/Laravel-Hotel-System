<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Check roles and redirect accordingly
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    } elseif ($user->hasRole('receptionist')) {
        return redirect()->route('receptionist.dashboard');
    } elseif ($user->hasRole('client')) {
        return redirect()->route('client.dashboard');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');


Route::middleware(['role:manager'])->group(function () {

    Route::get('/stripe', [StripeController::class, 'show'])->name('stripe.show');
});

///===================================================== Client Routes =========================================================

Route::get('/client-dashboard', [ClientController::class, 'dashboard'])
    ->middleware(['auth', 'role:client'])
    ->name('client.dashboard');

Route::resource('clients', ClientController::class)
    ->middleware('auth')
    ->except(['create', 'store']);

// Registration routes (no auth)
Route::get('register', [ClientController::class, 'create'])
    ->name('clients.create')
    ->middleware('guest');

Route::post('register', [ClientController::class, 'store'])
    ->name('clients.store')
    ->middleware('guest');

// Approval route
Route::post('clients/{client}/approve', [ClientController::class, 'approve'])
    ->name('clients.approve')
    ->middleware('auth');

// Reservations route
Route::get('clients/{client}/reservations', [ClientController::class, 'reservations'])
    ->name('clients.reservations')
    ->middleware('auth');



//=============================================================================
//======================== Receptionist Routes ==========================
Route::middleware(['role:receptionist'])->group(function () {
    Route::get('/receptionist', [ReceptionistController::class, 'index'])->name('receptionist.index');
    Route::get('/receptionist/clients', [ReceptionistController::class, 'clients'])->name('receptionist.clients');
    Route::get('/receptionist/reservations', [ReceptionistController::class, 'reservations'])->name('receptionist.reservations');
    Route::get('/receptionist/approved-clients', [ReceptionistController::class, 'approvedClients'])->name('receptionist.approvedClients');
    Route::get('/receptionist/pending-clients', [ReceptionistController::class, 'pendingClients'])->name('receptionist.pendingClients');
});
//======================================CRUD RECEPTIONIST=========================================
Route::resource('receptionists', ReceptionistController::class)
    ->middleware('auth')
    ->except(['create', 'store']);
// Receptionist Registration routes (no auth)
Route::get('receptionists/create', [ReceptionistController::class, 'create'])
    ->name('receptionists.create')
    ->middleware('auth');
Route::post('receptionists', [ReceptionistController::class, 'store'])
    ->name('receptionists.store')
    ->middleware('auth');
// Receptionist Edit route
Route::get('receptionists/{receptionist}/edit', [ReceptionistController::class, 'edit'])
    ->name('receptionists.edit')
    ->middleware('auth');
    // Receptionist Update route    
Route::patch('receptionists/{receptionist}', [ReceptionistController::class, 'update'])
    ->name('receptionists.update')
    ->middleware('auth');
// Receptionist Delete route  
Route::delete('receptionists/{receptionist}', [ReceptionistController::class, 'destroy'])
    ->name('receptionists.destroy')
    ->middleware('auth');
// Receptionist Show route
Route::get('receptionists/{receptionist}', [ReceptionistController::class, 'show'])
    ->name('receptionists.show')
    ->middleware('auth');
// Receptionist Profile route
Route::get('receptionists/{receptionist}/profile', [ReceptionistController::class, 'profile'])
    ->name('receptionists.profile')
    ->middleware('auth');              

// Receptionist Approval route
Route::post('receptionists/{receptionist}/approve', [ReceptionistController::class, 'approve'])
    ->name('receptionists.approve')
    ->middleware('auth');
// Receptionist Reservations route
Route::get('receptionists/{receptionist}/reservations', [ReceptionistController::class, 'reservations'])
    ->name('receptionists.reservations')
    ->middleware('auth');
// Receptionist Clients route
Route::get('receptionists/{receptionist}/clients', [ReceptionistController::class, 'clients'])
    ->name('receptionists.clients')
    ->middleware('auth');
// Receptionist Approved Clients route
Route::get('receptionists/{receptionist}/approved-clients', [ReceptionistController::class, 'approvedClients'])
    ->name('receptionists.approvedClients')
    ->middleware('auth');
// Receptionist Pending Clients route
Route::get('receptionists/{receptionist}/pending-clients', [ReceptionistController::class, 'pendingClients'])
    ->name('receptionists.pendingClients')
    ->middleware('auth');
// Receptionist Approved Reservations route
Route::get('receptionists/{receptionist}/approved-reservations', [ReceptionistController::class, 'approvedReservations'])
    ->name('receptionists.approvedReservations')
    ->middleware('auth');
//===========================================================================================================
Route::post('/stripe', [StripeController::class, 'handle'])->name('stripe.handle');

Route::get('/test-approved-clients', [ReceptionistController::class, 'testApprovedClients']);
Route::get('/test-pending-clients', [ReceptionistController::class, 'testPendingClients']);
Route::get('/test-client-reservations', [ReceptionistController::class, 'testClientReservations']);

Route::middleware(['auth'])->group(function () {

    Route::resource('floors', FloorController::class)->except(['show']);
    Route::resource('rooms', RoomController::class);
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users_count' => \App\Models\User::count(),
                'rooms_count' => \App\Models\Room::count(),
                'floors_count' => \App\Models\Floor::count(),
                'reservations_count' => \App\Models\Reservation::count(),
                'receptionists_count' => \App\Models\Receptionist::count(),
            ],
            'receptionists' => \App\Models\Receptionist::with('user')->get()->map(function ($receptionist) {
                return [
                    'id' => $receptionist->id,
                    'national_id' => $receptionist->national_id,
                    'phone_number' => $receptionist->phone_number,
                    'avatar_image' => $receptionist->avatar_image,
                    'user' => $receptionist->user ? [
                        'name' => $receptionist->user->name,
                        'email' => $receptionist->user->email,
                    ] : null
                ];
            })
        ]);
    })->name('admin.dashboard');
});

// Manager Routes
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])
        ->name('manager.dashboard');
});

// Receptionist Routes
Route::middleware(['auth', 'role:receptionist'])->group(function () {
    Route::get('/receptionist/dashboard', [ReceptionistController::class, 'dashboard'])
        ->name('receptionist.dashboard');
});

// Client Routes
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/dashboard', function () {
        return Inertia::render('Client/Dashboard');
    })->name('client.dashboard');
});

// Staff Routes (shared between manager and receptionist)
Route::middleware(['auth', 'role:manager|receptionist'])->group(function () {
    Route::get('/staff/dashboard', function () {
        return Inertia::render('Staff/Dashboard');
    })->name('staff.dashboard');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

require __DIR__ . '/auth.php';
require __DIR__ . '/managers.php';