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

// Reservation routes
Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\ReservationController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\Api\ReservationController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\Api\ReservationController::class, 'show'])->name('show');
    Route::put('/{id}', [App\Http\Controllers\Api\ReservationController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\Api\ReservationController::class, 'cancel'])->name('cancel');
    Route::post('/check-availability', [App\Http\Controllers\Api\ReservationController::class, 'checkAvailability'])->name('check-availability');
});



//=============================================================================

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
