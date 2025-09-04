<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function indexEvent()
    {
        // avoids N+1 and shows type name
        $events = Event::with('type')
            ->orderBy('start_at', 'desc')
            ->get();

        return view('events.index', compact('events'));
    }

    public function createEvent()
    {
        $types = EventType::orderBy('name')->get();
        return view('events.create-event', compact('types'));
    }

    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'start_at'      => ['required', 'date'],
            'end_at'        => ['required', 'date', 'after_or_equal:start_at'],
            'hours'         => ['nullable', 'integer', 'min:0'],
            'event_type_id' => ['nullable', 'exists:event_types,id'],
        ]);

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    public function deleteEvent($id)
    {
        // finds it or returns 404; deletes and returns with message
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento removido com sucesso!');
    }

    public function viewEditEvent($id)
    {
        // loads event + types for the <select>
        $event = Event::with('type')->findOrFail($id);
        $types = EventType::orderBy('name')->get();

        return view('events.view-edit-event', compact('event', 'types'));
    }

    public function updateEvent(Request $request)
    {
        // includes validation for ID and event_type_id
        $validated = $request->validate([
            'id'            => ['required', 'exists:events,id'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'start_at'      => ['required', 'date'],
            'end_at'        => ['required', 'date', 'after_or_equal:start_at'],
            'hours'         => ['nullable', 'integer', 'min:0'],
            'event_type_id' => ['nullable', 'exists:event_types,id'],
        ]);

        $event = Event::findOrFail($validated['id']);
        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }
}
