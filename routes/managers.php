<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;

Route::get('/managers', [ManagerController::class, 'index'])->name('managers.index');

Route::get('/managers/create', [ManagerController::class, 'create'])->name('managers.create');

Route::post('/managers', [ManagerController::class, 'store'])->name('managers.store');

Route::get('/managers/{manager}', [ManagerController::class, 'show'])->name('managers.show');

Route::get('/managers/{manager}/edit', [ManagerController::class, 'edit'])->name('managers.edit');

Route::patch('/managers/{manager}', [ManagerController::class, 'update'])->name('managers.update');

Route::delete('/managers/{manager}', [ManagerController::class, 'destroy'])->name('managers.destroy');

