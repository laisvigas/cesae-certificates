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

    // Global participants (not tied to an event)
    Route::prefix('participants')->group(function () {
        Route::get('/', [ParticipantController::class, 'indexParticipants'])->name('participants.index');
        Route::get('/create', [ParticipantController::class, 'createParticipant'])->name('participants.create'); // NOT USED
        Route::post('/', [ParticipantController::class, 'storeParticipant'])->name('participants.store');
        Route::put('/{participant}', [ParticipantController::class, 'updateParticipantInfo'])->name('participants.update');
        Route::delete('/{participant}', [ParticipantController::class, 'deleteParticipant'])->name('participants.delete');
    });

    // Event ↔ Participants
    Route::prefix('events/{event}')->group(function () {
        // list/edit participants in this event
        Route::get('/participants', [ParticipantController::class, 'showParticipantsInEvent'])
            ->name('participants.view-edit');

        // create (or find) participant and attach to this event
        Route::post('/participants/store-and-attach', [ParticipantController::class, 'storeAndAttach'])
            ->name('participants.storeAndAttach');

        // attach existing participant to this event
        Route::post('/participants/{participant}', [ParticipantController::class, 'attachParticipantToEvent'])
            ->name('participants.attach');

        // detach participant from this event
        Route::delete('/participants/{participant}', [ParticipantController::class, 'detachParticipant'])
            ->name('participants.detach');
    });

});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
