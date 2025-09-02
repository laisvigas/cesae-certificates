<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;

class ParticipantController extends Controller
{

    public function indexParticipants() // show all participants stored in the participants table
    {
        $participants = Participant::orderBy('name', 'asc')->get();

        return view('participants.index', compact('participants'));
    }

    public function showParticipantsInEvent(Event $event) // fetch participants for this one event
    {
        $participants = $event->participants;

        return view('participants.view-edit-participants', compact('event', 'participants'));
    }


    public function create(Event $event) // Show form to add a new participant - better to edit directly on the blade table?
    {
        return view('participants.create', compact('event'));
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

    public function storeAndAttach(Request $request, Event $event)
    {
    $participant = $this->storeParticipant($request);
    return $this->attachParticipantToEvent($event, $participant);
    }


    public function detachParticipant(Event $event, Participant $participant) // Detach a participant from a event
    {
        $event->participants()->detach($participant->id);
        return redirect()->route('participants.view-edit', $event)->with('success', 'Participante removido com sucesso.');
    }

    public function updateParticipantInfo(Request $request, Participant $participant)
    {
    $participantData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:255',
        'document_type' => 'nullable|string|in:national_id,passport',
        'document_number' => 'nullable|string|max:50',
    ]);

    // Update the participant information
    $participant->update($participantData);

    return back()->with('success', 'Dados do participante atualizados com sucesso!');
}


}
