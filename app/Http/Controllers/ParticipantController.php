<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;

class ParticipantController extends Controller
{

    public function indexParticipants() // NOT USED! // show all participants stored in the participants table
    {
        $participants = Participant::orderBy('name', 'asc')->get();

        return view('participants.index', compact('participants'));
    }

    public function showParticipantsInEvent(Event $event) // fetch participants for this one event
    {
        $participants = $event->participants;

        return view('participants.view-edit-participants', compact('event', 'participants'));
    }


    public function createParticipant() // not needed
    {
        return;
    }

    public function deleteParticipant(Participant $participant){

        $participant->delete();

        return back();
    }


    public function storeParticipant(Request $request) // Store a new participant
    {
        $participantData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|in:national_id,passport',
            'document_number' => 'nullable|string|max:50',
        ]);

        // This prevents duplicates by email
        $participant = Participant::firstOrCreate(
        ['email' => $participantData['email']], // unique field
        $participantData // fields to fill if not found
        );

        return $participant; // return the model/object to use it in the next function
    }

    public function attachParticipantToEvent(Event $event, Participant $participant) // attach participant to event.
    {
    // Attach participant to the event, but do not detach existing ones (difference between sync() and syncWithoutDetaching())
        $event->participants()->syncWithoutDetaching($participant->id);

        return redirect()->route('participants.view-edit', $event)
                         ->with('success', 'Participantes adicionados com sucesso!');
    }

    public function storeAndAttach(Request $request, $eventId) // search for a participant and attach it a to an event. If the participant dosnt exist, create it
    {
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
    ]);

    $event = Event::findOrFail($eventId);

    // Check if participant exists. If doesnt, create it
    $participant = Participant::firstOrCreate(
        ['email' => $validated['email']],
        ['name' => $validated['name']]
    );

    // Attach participant to event if not already attached
    if (!$event->participants->contains($participant->id)) {
        $event->participants()->attach($participant->id);
    }

    return back()->with('success', 'Participante adicionado.');
    }


    public function detachParticipant(Event $event, Participant $participant)
    {
    $event = Event::findOrFail($event->id);
    $event->participants()->detach($participant->id);

    return back()->with('success', 'Participante removido do evento.');
    }

    public function updateParticipantInfo(Request $request, Participant $participant)
    {
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'document_type' => 'nullable|string',
        'document_number' => 'nullable|string',
    ]);

    $participant->update($validated);

    return back()->with('success', 'Participante atualizado.');
    }



}
