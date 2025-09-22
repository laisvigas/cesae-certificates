<?php
namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'   => 'nullable|string|max:255',
            'mensagem' => 'required|string',
            'priority' => 'required|in:high,medium,low',
        ]);

        Note::create($data);
        return back()->with('success', 'Nota criada!');
    }

    public function edit(Note $note)
    {
        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'titulo'   => 'nullable|string|max:255',
            'mensagem' => 'required|string',
            'priority' => 'required|in:high,medium,low',
        ]);

        $note->update($data);
        return redirect()->route('dashboard')->with('success', 'Nota atualizada!');
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return back()->with('success', 'Nota excluída!');
    }
}
