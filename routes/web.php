<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('welcome');
});

// Painel (só admin/staff)
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Rotas de eventos
    Route::get('/events', [EventController::class, 'indexEvent'])->name('events.index');
    Route::get('/events/create-event', [EventController::class, 'createEvent'])->name('events.create');
    Route::get('/event-delete/{id}', [EventController::class, 'deleteEvent'])->name('events.delete');
    Route::get('/events/view-edit-event/{id}', [EventController::class, 'viewEditEvent']) -> name('events.view-edit');
    Route::put('/event-update', [EventController::class, 'updateEvent']) -> name('events.update');
    Route::post('/events', [EventController::class, 'storeEvent'])->name('events.store');


    // Rotas de participantes

    // Participant CRUD (not tied to a single event)
    Route::prefix('participants')->group(function () {
        Route::get('/', [ParticipantController::class, 'index'])->name('participants.index');
        Route::get('/participant-create', [ParticipantController::class, 'createParticipant'])->name('participants.create');
        Route::post('/participant-store', [ParticipantController::class, 'storeParticipant'])->name('participants.store');
        Route::put('/participant-update/{participant}', [ParticipantController::class, 'update'])->name('participants.update');
        Route::delete('/participant-delete/{participant}', [ParticipantController::class, 'deleteParticipant'])->name('participants.delete');
    });

    // Participants <-> Events relationship
    Route::prefix('events/{event}/participants')->group(function () {
        Route::get('/view-edit-participants', [ParticipantController::class, 'viewEditParticipants'])->name('participants.view-edit');
        Route::post('/event_participant-attach/{participant}', [ParticipantController::class, 'attachParticipant'])->name('participants.attach');
        Route::delete('/event_participant-detach/{participant}', [ParticipantController::class, 'detachParticipant'])->name('participants.detach');
    });


});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
