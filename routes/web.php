<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
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
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('client')) {
        return redirect()->route('client.dashboard');
    } elseif ($user->hasRole('manager') || $user->hasRole('receptionist')) {
        return redirect()->route('staff.dashboard');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/stripe', [StripeController::class, 'handle'])->name('stripe.handle');

Route::get('/test-approved-clients', [ReceptionistController::class, 'testApprovedClients']);
Route::get('/test-pending-clients', [ReceptionistController::class, 'testPendingClients']);
Route::get('/test-client-reservations', [ReceptionistController::class, 'testClientReservations']);

Route::middleware(['auth'])->group(function () {

    Route::resource('floors', FloorController::class)->except(['show']);
    Route::resource('rooms', RoomController::class);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/managers.php';