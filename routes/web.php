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
    Route::get('/events', [EventController::class, 'indexEvent'])->name('events.index');
    Route::get('/events/create-event', [EventController::class, 'createEvent'])->name('events.create');
    Route::get('/event-delete/{id}', [EventController::class, 'deleteEvent'])->name('events.delete');
    Route::get('/events/view-edit-event/{id}', [EventController::class, 'viewEditEvent']) -> name('events.view-edit');
    Route::put('/event-update', [EventController::class, 'updateEvent']) -> name('events.update');
    Route::post('/events', [EventController::class, 'storeEvent'])->name('events.store');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
