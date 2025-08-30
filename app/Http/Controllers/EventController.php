<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_at', 'desc')->get();

        return view('events.index', compact('events'));
    }
}
