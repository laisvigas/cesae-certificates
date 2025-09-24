<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use App\Models\Event;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    // 1. List all templates
    public function index()
    {
        try {
            $templates = CertificateTemplate::all();
            return response()->json($templates);
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Error loading certificate templates: ' . $e->getMessage());

            // Return a generic JSON error response to the client
            return response()->json([
                'success' => false,
                'message' => 'Failed to load templates. Please try again later.'
            ], 500);
        }
    }

    // 2. Store a new template
    // Obs: Sobre os números HTTP:
    // 201 Created → sucesso, o recurso foi criado no servidor (por isso usamos no store).
    // 422 Unprocessable Entity → erros de validação (campos obrigatórios faltando ou inválidos).
    // 500 Internal Server Error → erro inesperado no servidor (ex.: problema no banco, coluna inexistente, JSON mal formado, etc.).
    // 200 OK → sucesso padrão para respostas que não criam recursos (GET, PUT, etc.).
    public function store(Request $request)
    {
        try {
            // Validação
            // Obs:
            // No validate(), o Laravel dispara exceções com mensagens padrão (mensagens de erro).
            // Para personalizar as mensagems de erro, basta passar o segundo argumento para validate()
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:certificate_templates,name',
                'event_id' => 'nullable|exists:events,id',
                'logo' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:2048',
                'signature' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:2048',
            ], [
                'name.unique' => 'Já existe um template com este nome. Escolha outro.',
                'name.required' => 'O campo nome é obrigatório.',
                'name.max' => 'O nome do template não pode ter mais de 255 caracteres.',
            ]);

            // ---------- Coleta todas as opções personalizadas do certificado:

            // Coleta de strings
            $options = $request->only([
                'primary_color',
                'watermark',
                'course_line_prefix',
            ]);

            // Uploads de logo e assinatura
            if ($request->hasFile('logo')) {
                // Guarda o ficheiro em 'storage/app/public/certificate_logos'
                $options['logo_path'] = $request->file('logo')->store('certificate_logos', 'public');
            }

            if ($request->hasFile('signature')) {
                // Guarda o ficheiro em 'storage/app/public/certificate_signatures'
                $options['signature_path'] = $request->file('signature')->store('certificate_signatures', 'public');
            }

            // Criação do template
            $template = CertificateTemplate::create([
                'name'    => $validated['name'],
                'options' => $options, // Laravel automatically handles JSON encoding here due to the $casts property on Model
            ]);

            // Se houver um evento selecionado, vincula o template
            if (!empty($validated['event_id'])) {
                $event = Event::find($validated['event_id']);
                if ($event) {
                    $event->template_id = $template->id;
                    $event->save();
                }
            }

            return response()->json([
                'success' => true,
                'template' => $template
            ], 201);


        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorna erros de validação como JSON
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Qualquer outro erro
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar o template',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    // 3. Show a template
    public function show(CertificateTemplate $template)
    {
        return response()->json($template->options); // need to convert options back to json so JS can handle it
    }

    // 4. Assign a template to an event
    public function assignToEvent(Request $request, $templateId)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $event = Event::findOrFail($request->event_id);
        $event->template_id = $templateId;
        $event->save();

        return redirect()
        ->back()
        ->with('success', 'Template vinculado ao evento com sucesso.');
    }

    // 5. Unassign template from an event
    public function unassignFromEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $event->template_id = null;
        $event->save();

        return redirect()
        ->back()
        ->with('success', 'Template desvinculado ao evento com sucesso.');
    }


    // 6. Delete a template
    public function destroy(CertificateTemplate $template)
    {
        try {
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template apagado com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao apagar o template',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
