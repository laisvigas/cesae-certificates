<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function indexEvent(Request $request)
    {
        $selected = collect($request->input('types', []))
            ->filter(fn ($v) => $v !== '' && $v !== null)
            ->values()
            ->all();

        $selectedStatus = collect($request->input('status', []))
            ->filter(fn ($v) => in_array($v, ['past', 'ongoing', 'upcoming'], true))
            ->values()
            ->all();

        $types = EventType::orderBy('name')->get();
        $now = now();

        $eventsQuery = Event::with('type')
            ->withCount('participants')
            ->orderBy('start_at', 'desc')
            ->filterTypes($selected)
            ->filterStatus($selectedStatus, $now);

        $events = $eventsQuery
            ->paginate(20)
            ->withQueryString();

        $totalAllEvents = Event::count();

        return view('events.index', compact('events','types','selected','selectedStatus', 'totalAllEvents'));
    }


    public function createEvent()
    {
        $types = EventType::orderBy('name')->get();
        $event = new Event();
        return view('events.create-event', compact('types', 'event'));
    }

    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'start_at'            => ['required', 'date'],
            'end_at'              => ['required', 'date', 'after_or_equal:start_at'],
            'hours'               => ['nullable', 'integer', 'min:0'],
            'event_type_id'       => ['nullable', 'exists:event_types,id'],

            // NOVOS CAMPOS (opcionais)
            'issuer_institution'  => ['nullable', 'string', 'max:150'],
            'issuer_name'         => ['nullable', 'string', 'max:120'],
            'issuer_role'         => ['nullable', 'string', 'max:120'],
            'issuer_signature'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);

        // Upload da assinatura (se houver)
        if ($request->hasFile('issuer_signature')) {
            $path = $request->file('issuer_signature')->store('signatures', 'public');
            $data['issuer_signature_path'] = $path;
        }

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    public function viewEditEvent(Event $event)
    {
        $types = EventType::orderBy('name')->get();
        return view('events.view-edit-event', compact('event', 'types'));
    }

    public function deleteEvent(Event $event)
    {
        if ($event->issuer_signature_path && Storage::disk('public')->exists($event->issuer_signature_path)) {
            Storage::disk('public')->delete($event->issuer_signature_path);
        }
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento removido com sucesso!');
    }

    public function updateEvent(Request $request)
    {
        $validated = $request->validate([
            'id'                  => ['required', 'exists:events,id'],
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'start_at'            => ['required', 'date'],
            'end_at'              => ['required', 'date', 'after_or_equal:start_at'],
            'hours'               => ['nullable', 'integer', 'min:0'],
            'event_type_id'       => ['nullable', 'exists:event_types,id'],

            // NOVOS CAMPOS (opcionais)
            'issuer_institution'  => ['nullable', 'string', 'max:150'],
            'issuer_name'         => ['nullable', 'string', 'max:120'],
            'issuer_role'         => ['nullable', 'string', 'max:120'],
            'issuer_signature'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);

        $event = Event::findOrFail($validated['id']);

        // Se veio nova assinatura, apaga a antiga e grava a nova
        if ($request->hasFile('issuer_signature')) {
            if ($event->issuer_signature_path && Storage::disk('public')->exists($event->issuer_signature_path)) {
                Storage::disk('public')->delete($event->issuer_signature_path);
            }
            $path = $request->file('issuer_signature')->store('signatures', 'public');
            $validated['issuer_signature_path'] = $path;
        }

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }
}
