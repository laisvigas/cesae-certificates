<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function indexEvent()
    {
        $events = Event::orderBy('start_at', 'desc')->get();

        return view('events.index', compact('events'));
    }

    public function createEvent()
    {
        return view('events.create-event');
    }

    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_at'    => ['required','date'],
            'end_at'      => ['required','date','after_or_equal:start_at'],
            'hours'       => ['nullable','integer','min:0'],
        ]);

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    public function deleteEvent($id){

        Event::destroy($id);

        return back();
    }

    public function viewEditEvent($id) {

        $myEvent = Event:: where('id',$id) -> first();


        return view('events.view-edit-event', compact('myEvent'));
    }

    public function updateEvent(Request $request){

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_at' => 'required|date',
        'end_at' => 'required|date|after_or_equal:start_at',
        'hours' => 'nullable|integer|min:0',
    ]);


    $eventId = $request->input('id');
    $event = Event::findOrFail($eventId);


    $event->update([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'start_at' => $validated['start_at'],
        'end_at' => $validated['end_at'],
        'hours' => $validated['hours'],
    ]);


    return redirect()->route('events.index')
                     ->with('success', 'Evento atualizado com sucesso!');
    }



}
