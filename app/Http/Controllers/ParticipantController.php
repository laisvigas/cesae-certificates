<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Certificate;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
    public function indexParticipants()
    {
        $participants = Participant::with([
            'events',
            'certificates.event'
        ])->orderBy('name')->get();

        $totalParticipants = $participants->count();

        return view('participants.index', compact('participants', 'totalParticipants'));
    }

    public function showParticipantsInEvent(Event $event) // fetch participants for this one event
    {
        $participants = $event->participants;
        $template = $event->template;
        $templates = CertificateTemplate::all();
        $events = Event::all(); // para poder importar participantes de outro evento

        return view('participants.view-edit-participants', compact('event', 'participants', 'template', 'templates', 'events'));
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
    {
        // valida primeiro o email
        $base = $request->validate([
            'email' => 'required|email|max:255',
            'update_existing' => 'nullable|boolean',
        ]);
        $email = strtolower(trim($base['email']));
        $updateExisting = (bool)($base['update_existing'] ?? false);

        $event = Event::findOrFail($eventId);

        // Procura participante
        $participant = Participant::where('email', $email)->first();

        if (!$participant) {
            // Se não existe, agora sim exigimos os outros campos
            $extra = $request->validate([
                'name'            => 'required|string|max:255',
                'phone'           => 'nullable|string|max:50',
                'address'         => 'nullable|string|max:255',
                'document_type'   => 'nullable|string|max:100',
                'document_number' => 'nullable|string|max:100',
                'nationality'     => 'nullable|string|max:150',
            ]);

            $participant = Participant::create([
                'email'           => $email,
                'name'            => $extra['name'],
                'phone'           => $extra['phone'] ?? null,
                'address'         => $extra['address'] ?? null,
                'document_type'   => $extra['document_type'] ?? null,
                'document_number' => $extra['document_number'] ?? null,
                'nationality'     => $extra['nationality'] ?? null,
            ]);

        } else {
            // Já existe → opcionalmente atualizar
            if ($updateExisting) {
                $upd = $request->validate([
                    'name'            => 'required|string|max:255',
                    'phone'           => 'nullable|string|max:50',
                    'address'         => 'nullable|string|max:255',
                    'document_type'   => 'nullable|string|max:100',
                    'document_number' => 'nullable|string|max:100',
                    'nationality'     => 'nullable|string|max:150',
                ]);

                $participant->fill($upd)->save();
            }
            // Se não marcou update_existing, não altera nada
        }

        // Anexa ao evento se ainda não estiver
        if (!$event->participants()->where('participants.id', $participant->id)->exists()) {
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

    public function importFromEventSimple(Request $request)
    {
        // IDs vindos do formulário
        $targetEventId = $request->input('target_event_id'); // evento que vai receber os participantes
        $sourceEventId = $request->input('source_event_id'); // evento de onde os participantes vêm

        $targetEvent = Event::find($targetEventId);
        $sourceEvent = Event::with('participants')->find($sourceEventId);

        if (!$targetEvent || !$sourceEvent) {
            return "Evento de origem ou destino não encontrado.";
        }

        // Pega os IDs dos participantes do evento de origem
        $participantIds = $sourceEvent->participants->pluck('id')->toArray();

        // Adiciona ao evento destino sem remover os existentes
        $targetEvent->participants()->syncWithoutDetaching($participantIds);

        return back()->with('success', "Importação concluída: " . count($participantIds) . " participantes adicionados ao evento '{$targetEvent->title}'.");
    }

    public function lookupByEmail(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($data['email']));
        $participant = Participant::where('email', $email)->first();

        if ($participant) {
            return response()->json([
                'found' => true,
                'participant' => $participant->only([
                    'id','name','email','phone','address','document_type','document_number','nationality'
                ])
            ]);
        }

        return response()->json(['found' => false]);
    }
}
