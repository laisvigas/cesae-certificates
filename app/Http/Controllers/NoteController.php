<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        // Limite duro de lembretes
        $LIMIT = 6;

        if (Note::count() >= $LIMIT) {
            return back()
                ->with('reminders_full', "Ops! Já há lembretes demais aqui!")
                ->withInput();
        }

        $data = $request->validate([
            'titulo'   => 'nullable|string|max:255',
            'mensagem' => 'required|string|max:120',
            'priority' => 'required|in:high,medium,low',
        ]);

        Note::create($data);

        return back()->with('success', 'Lembrete adicionado.');
    }

    public function edit(Note $note)
    {
        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'titulo'   => 'nullable|string|max:255',
            'mensagem' => 'required|string|max:120',
            'priority' => 'required|in:high,medium,low',
        ]);

        $note->update($data);

        return redirect()->route('dashboard')->with('success', 'Lembrete atualizado.');
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return back()->with('success', 'Lembrete excluído.');
    }
}
