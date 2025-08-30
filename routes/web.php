<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController; // 👈 importar
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Painel (só admin/staff)
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Listagem de eventos
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
