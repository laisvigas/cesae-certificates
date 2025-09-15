<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function createParticipant() // not used (used storeAndAttach instead)
    {
        return;
    }

    public function deleteParticipant(Participant $participant)
    {
        $participant->delete();
        return back();
    }

    public function storeParticipant(Request $request) // not used (used storeAndAttach instead)
    {
        $participantData = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'document_type'   => 'nullable|string|in:national_id,passport',
            'document_number' => 'nullable|string|max:50',
            'nationality'     => 'nullable|string|max:150',
        ]);

        // This prevents duplicates by email
        $participant = Participant::firstOrCreate(
            ['email' => $participantData['email']], // unique field
            $participantData // fields to fill if not found (inclui nationality)
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

    public function storeAndAttach(Request $request, $eventId)
    // Search for a participant and attach it to an event.
    // If the participant doesn't exist, create it. If it exists, update optional fields.
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'document_type'   => 'nullable|string|max:100',
            'document_number' => 'nullable|string|max:100',
            'nationality'     => 'nullable|string|max:150',
        ]);

        $event = Event::findOrFail($eventId);

        // Check if participant exists. If not, create it
        $participant = Participant::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name'            => $validated['name'],
                'phone'           => $validated['phone'] ?? null,
                'address'         => $validated['address'] ?? null,
                'document_type'   => $validated['document_type'] ?? null,
                'document_number' => $validated['document_number'] ?? null,
                'nationality'     => $validated['nationality'] ?? null,
            ]
        );

        // If participant already existed, update optional fields (inclui nationality)
        $participant->fill($validated);
        $participant->save();

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
            'name'            => 'required|string',
            'email'           => 'required|email',
            'phone'           => 'nullable|string',
            'address'         => 'nullable|string',
            'document_type'   => 'nullable|string',
            'document_number' => 'nullable|string',
            'nationality'     => 'nullable|string|max:150',
        ]);

        $participant->update($validated);

        return back()->with('success', 'Participante atualizado.');
    }

    // Imports a csv file with participants data to attach to an event
    // Obs: Validator::make() is native to Laravel.
    // It builds a validator instance for an array of data against a set of rules.
    // make($data, $rules, $messages = [], $customAttributes = [])
    // It works the same way as the $request->validate() method we already used to validate data
    // in a request. But, in our CSV import we can’t use $request->validate() directly
    // because each line isn’t a request. So, that’s why we need to create
    // a Valitador for each row of the cvs
    public function importCsv(Request $request, Event $event)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');

        $header = fgetcsv($handle, 1000, ','); // First row (headers)
        $imported = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $data = array_combine($header, $row);

            // Example expected headers: name,email,phone,address,document_type,document_number,nationality
            $validator = Validator::make($data, [
                'name'            => 'required|string|max:255',
                'email'           => 'required|email',
                'phone'           => 'nullable|string',
                'address'         => 'nullable|string',
                'document_type'   => 'nullable|string',
                'document_number' => 'nullable|string',
                'nationality'     => 'nullable|string|max:150',
            ]);

            if ($validator->fails()) {
                continue; // skip invalid row
            }

            // Find or create participant
            $participant = Participant::firstOrCreate(
                ['email' => $data['email']], // unique by email
                [
                    'name'            => $data['name'],
                    'phone'           => $data['phone'] ?? null,
                    'address'         => $data['address'] ?? null,
                    'document_type'   => $data['document_type'] ?? null,
                    'document_number' => $data['document_number'] ?? null,
                    'nationality'     => $data['nationality'] ?? null,
                ]
            );

            // If it already existed, we can update optional fields (safe update)
            $participant->fill([
                'name'            => $data['name'],
                'phone'           => $data['phone'] ?? null,
                'address'         => $data['address'] ?? null,
                'document_type'   => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'nationality'     => $data['nationality'] ?? null,
            ])->save();

            // Attach to event (avoid duplicate with syncWithoutDetaching)
            $event->participants()->syncWithoutDetaching([$participant->id]);

            $imported++;
        }

        fclose($handle);

        return back()->with('success', "$imported participantes importados com sucesso.");
    }
}
