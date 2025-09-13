<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function indexEvent(Request $request)
{
    $selected = collect($request->input('types', []))
        ->filter(fn ($v) => $v !== '' && $v !== null)
        ->values()
        ->all();

    // estados selecionados 
    $selectedStatus = collect($request->input('status', []))
        ->filter(fn ($v) => in_array($v, ['past', 'ongoing', 'upcoming'], true))
        ->values()
        ->all();

    // carrega os tipos para popular o dropdown 
    $types = EventType::orderBy('name')->get();

    $now = now();

    $eventsQuery = Event::with('type')
        ->withCount('participants')   // evita N+1 no count
        ->orderBy('start_at', 'desc');

    // filtro por tipo
    if (!empty($selected)) {
        $wantsNull = in_array('null', $selected, true);
        $ids = array_filter($selected, fn ($v) => $v !== 'null');

        $eventsQuery->where(function ($q) use ($ids, $wantsNull) {
            if (!empty($ids)) {
                $q->whereIn('event_type_id', $ids);
            }
            if ($wantsNull) {
                $q->orWhereNull('event_type_id');
            }
        });
    }

    // filtro por estado
    if (!empty($selectedStatus)) {
        $eventsQuery->where(function ($q) use ($selectedStatus, $now) {
            // Encerrado 
            if (in_array('past', $selectedStatus, true)) {
                $q->orWhere('end_at', '<', $now);
            }
            // A decorrer 
            if (in_array('ongoing', $selectedStatus, true)) {
                $q->orWhere(function ($qq) use ($now) {
                    $qq->where('start_at', '<=', $now)
                       ->where('end_at', '>=', $now);
                });
            }
            // Agendado 
            if (in_array('upcoming', $selectedStatus, true)) {
                $q->orWhere('start_at', '>', $now);
            }
        });
    }

    $events = $eventsQuery->get();

    return view('events.index', [
        'events'         => $events,
        'types'          => $types,
        'selected'       => $selected,        
        'selectedStatus' => $selectedStatus,  
    ]);
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
