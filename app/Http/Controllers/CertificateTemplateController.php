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
        $templates = CertificateTemplate::all();
        return response()->json($templates);
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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'event_id' => 'nullable|exists:events,id',
            ]);

            // Coleta todas as opções personalizadas do certificado
            $options = $request->only([
                'primary_color',
                'watermark',
                'course_line_prefix',
                'logo',
                'signature',
            ]);

            // Uploads de logo e assinatura
            if ($request->hasFile('logo')) {
                $options['logo_path'] = $request->file('logo')->store('certificate_logos', 'public');
            }

            if ($request->hasFile('signature')) {
                $options['signature_path'] = $request->file('signature')->store('certificate_signatures', 'public');
            }

            // Criação do template
            $template = CertificateTemplate::create([
                'name'    => $validated['name'],
                'options' => $options,
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
        return response()->json(json_decode($template->options, true));
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

        return response()->json([
            'message' => 'Template assigned to event successfully',
            'event' => $event,
        ]);
    }

    // 5. Unassign template from an event
    public function unassignFromEvent($eventId)
    {
        $event = Event::findOrFail($eventId);
        $event->template_id = null;
        $event->save();

        return response()->json([
            'message' => 'Template unassigned from event successfully',
            'event' => $event,
        ]);
    }
}
