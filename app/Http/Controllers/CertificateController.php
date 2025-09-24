<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Certificate;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\CertificateMail;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as Dompdf; // alias Dompdf

class CertificateController extends Controller
{
    // =========================
    // VIEWS
    // =========================

    // Show custom form blade
    public function custom()
    {
        $events = Event::with('participants')->orderBy('start_at', 'desc')->get();
        $templates = CertificateTemplate::all();

        return view('certificates.custom', compact('events', 'templates'));
    }

    // =========================
    // PARTICIPANTS (outras telas)
    // =========================

    // Generate/find and download PDF for a participant in an event
    public function certificateDownload(Event $event, Participant $participant)
    {
        // Garante que o participante realmente está vinculado a este evento
        if (!$event->participants()->whereKey($participant->id)->exists()) {
            abort(403, 'Participante não está vinculado a este evento.');
        }

        // Cria (ou busca) o certificado
        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        // (Opcional) manter consistência e já garantir public_id/published_at
        if (empty($certificate->public_id)) {
            $certificate->public_id = (string) Str::uuid();
        }
        if (empty($certificate->published_at)) {
            $certificate->published_at = now();
        }
        $certificate->save();

        // Busca o template do evento para usar suas opções
        $template = $event->template;

        // Monta dados e gera o PDF
        $pdfData = $this->buildCertificateData($event, $participant, null, $certificate, $template);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', $pdfData)
            ->setPaper('A4', 'landscape')
            ->output();

        return response()->streamDownload(
            fn() => print($pdf),
            "certificate-{$participant->name}.pdf"
        );
    }


    // Generate/find and send participant certificate by email
    public function sendCertificate(Request $request)
    {
        $data = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'event_id'       => 'required|exists:events,id',
        ]);

        $participant = Participant::findOrFail($data['participant_id']);
        $event       = Event::with('type')->findOrFail($data['event_id']);

        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        // Garantir que o certificado tem identificador público e está "publicado"
        if (empty($certificate->public_id)) {
            $certificate->public_id = (string) Str::uuid();
        }
        if (empty($certificate->published_at)) {
            $certificate->published_at = now();
        }
        $certificate->save();

        // Busca o template do evento para usar suas opções
        $template = $event->template;

        $pdfData = $this->buildCertificateData($event, $participant, null, $certificate, $template);

        // usar o facade Dompdf que já está importado
        $pdf = Dompdf::loadView('certificates.pdf', $pdfData)
            ->setPaper('A4', 'landscape')
            ->output();

        if (!empty($participant->email)) {
            // passar também o $certificate para compor o link público no e-mail
            Mail::to($participant->email)
                ->send(new CertificateMail($pdf, $participant->name, $certificate));
        }

        return back()->with('success', 'Certificado enviado com sucesso para ' . ($participant->email ?: 'o destinatário'));
    }

    // Send a certificate to all participants of an event
    public function sendAll(Event $event)
    {
        $participants = $event->participants;

        if ($participants->isEmpty()) {
            return back()->with('error', 'Não há participantes para este evento.');
        }

        // Busca o template do evento para usar suas opções
        $template = $event->certificateTemplate;

        foreach ($participants as $participant) {
            $certificate = Certificate::firstOrCreate(
                ['event_id' => $event->id, 'participant_id' => $participant->id],
                ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
            );

            // garantir public_id/published_at
            if (empty($certificate->public_id)) {
                $certificate->public_id = (string) Str::uuid();
            }
            if (empty($certificate->published_at)) {
                $certificate->published_at = now();
            }
            $certificate->save();

            $pdfData = $this->buildCertificateData($event, $participant, null, $certificate, $template);
            $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->setPaper('A4', 'landscape')->output();

            if (!empty($participant->email)) {
                // passar $certificate para ter o link público no corpo do e-mail
                Mail::to($participant->email)->send(new CertificateMail($pdf, $participant->name, $certificate));
            }
        }

        return back()->with('success', 'Certificados enviados com sucesso para todos os participantes.');
    }

    // =========================
    // CUSTOM (form custom.blade.php)
    // =========================

    // Generate and download PDF from custom form
    public function certificateDownloadCustom(Request $request)
    {
        $request->validate([
            'event_id'           => 'required|exists:events,id',
            'participant_id'     => 'required|exists:participants,id',
            'course_line_prefix' => ['nullable','string','max:120'], // mantido para compatibilidade
            'watermark'          => ['nullable','string','max:80'],
            'primary_color'      => ['nullable','regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'logo'               => ['nullable','image','mimes:png,jpg,jpeg','max:1024'],
            'signature'          => ['nullable','image','mimes:png,jpg,jpeg','max:1024'],
        ]);

        $event       = Event::with('type')->findOrFail($request->input('event_id'));
        $participant = Participant::findOrFail($request->input('participant_id'));

        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        // Busca o template selecionado
        $template = $request->filled('template_id') ? CertificateTemplate::findOrFail($request->input('template_id')) : null;

        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate, $template);

        $dompdf = Dompdf::loadView('certificates.pdf', $pdfData)->setPaper('A4', 'landscape');
        $pdf = $dompdf->output();

        return response()->streamDownload(
            fn() => print($pdf),
            "certificate-{$participant->name}.pdf"
        );
    }

    public function publicDownloadByPublicId(string $publicId)
    {
        $cert = Certificate::with(['event.type', 'event.template', 'participant'])
            ->where('public_id', $publicId)
            ->whereNotNull('published_at')
            ->firstOrFail();

        // pega as opções de customização do template ligado ao evento
        $template = $cert->event->template;
        $templateOptions = $template?->options ?? [];

        // gera os dados necessários para o blade
        $pdfData = $this->buildCertificateData(
            $cert->event,
            $cert->participant,
            null,
            $cert,
            $template
        );

        // injeta as opções dentro do $pdfData, se não estiver lá
        $pdfData['options'] = $templateOptions;

        // gera o PDF "on the fly"
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', $pdfData)
            ->setPaper('A4', 'landscape')
            ->output();

        // retorna direto sem salvar no disco
        return response()->streamDownload(
            fn() => print($pdf),
            "certificate-{$cert->participant->name}.pdf"
        );
    }



    // Generates PDF and send it to the email provided on the custom form
    public function sendCustom(Request $request)
    {
        $request->validate([
            'event_id'           => 'required|exists:events,id',
            'participant_id'     => 'required|exists:participants,id',
            'email'              => 'required|email',
            'course_line_prefix' => ['nullable','string','max:120'],
            'watermark'          => ['nullable','string','max:80'],
            'primary_color'      => ['nullable','regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'logo'               => ['nullable','image','mimes:png,jpg,jpeg','max:1024'],
            'signature'          => ['nullable','image','mimes:png,jpg,jpeg','max:1024'],
        ]);

        $event       = Event::with('type')->findOrFail($request->input('event_id'));
        $participant = Participant::findOrFail($request->input('participant_id'));

        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        // garantir public_id/published_at (para o link público no e-mail)
        if (empty($certificate->public_id)) {
            $certificate->public_id = (string) Str::uuid();
        }
        if (empty($certificate->published_at)) {
            $certificate->published_at = now();
        }
        $certificate->save();

        // Busca o template selecionado
        $template = $request->filled('template_id') ? CertificateTemplate::findOrFail($request->input('template_id')) : null;


        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate, $template);
        $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->setPaper('A4', 'landscape')->output();

        // passar $certificate
        Mail::to($request->input('email'))->send(new CertificateMail($pdf, $participant->name, $certificate));

        return back()->with('success', 'Certificado enviado com sucesso para ' . $request->input('email'));
    }

    // Preview na custom.blade (HTML no iframe)
    public function previewCustom(Request $request)
    {
        $request->validate([
            'event_id'           => 'required|exists:events,id',
            'participant_id'     => 'required|exists:participants,id',
            'course_line_prefix' => ['nullable','string','max:120'],
            'watermark'          => ['nullable','string','max:80'],
            'primary_color'      => ['nullable','regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'logo'               => ['nullable','image','mimes:png,jpg,jpeg','max:1024'],
            'signature'          => ['nullable','image','mimes:png,jpg,jpeg','max:1024'],
        ]);

        $event       = Event::with('type')->findOrFail($request->input('event_id'));
        $participant = Participant::findOrFail($request->input('participant_id'));

        // Fetch the selected template if one is provided
        $template = null;
        if ($request->filled('template_id')) {
            $template = CertificateTemplate::findOrFail($request->input('template_id'));
        }

        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate,  $template);
        $pdfData['preview_mode'] = true;

        return view('certificates.pdf', $pdfData);
    }

    // Preview na view-edit-participnat.blade
    public function preview(Request $request)
    {
        // 1. Validação dos dados de entrada
        $request->validate([
            'event_id'       => 'required|exists:events,id',
            'participant_id' => 'required|exists:participants,id',
        ]);

        // 2. Busca o evento e o participante
        $event       = Event::with('type')->findOrFail($request->input('event_id'));
        $participant = Participant::findOrFail($request->input('participant_id'));

        // 3. Busca o template associado ao evento. Se o evento não tiver um, a lógica pode usar um padrão.
        // Assumimos que a relação 'certificateTemplate' está configurada no modelo Event.
        $template = $event->template;

        // 4. Cria ou encontra um registro de certificado para o preview
        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        // 5. Constrói os dados para o PDF
        // Reutiliza a sua função 'buildCertificateData' para centralizar a lógica de construção dos dados.
        // Esta função deve ser responsável por juntar todas as informações do evento, participante e template.
        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate, $template);
        $pdfData['preview_mode'] = true;

        // 6. Retorna a view do certificado para o preview
        return view('certificates.pdf', $pdfData);
    }


    // =========================
    // HELPERS
    // =========================

    /**
     * Monta todos os dados necessários para a Blade do certificado conforme o layout novo (monta um array com os dados).
     * - frase "Concluiu com êxito…" com tipo, título, instituição, horas e datas
     * - assinatura do evento (ou ad-hoc do form), watermark, cor, logo etc.
     */
    private function buildCertificateData(Event $event, Participant $participant, ?Request $request, Certificate $certificate, ?CertificateTemplate $template = null): array
    {
        // Tipo e instituição
        $eventTypeName   = optional($event->type)->name ?? 'evento';
        $institutionName = $event->issuer_institution ?: config('app.name');
        $eventTitle      = $event->title;

        // Duração (horas)
        $durationPhrase = null;
        $h = $event->hours;
        if (is_numeric($h) && (int)$h > 0) {
            $h = (int)$h;
            $durationPhrase = 'com carga horária total de ' . $h . ' ' . ($h === 1 ? 'hora' : 'horas');
        }

        // Datas (mesmo dia vs intervalo)
        $datePhrase = null;
        $start = $event->start_at;
        $end   = $event->end_at;
        if ($start && $end) {
            if ($start->isSameDay($end)) {
                $datePhrase = 'realizado em ' . $end->format('d/m/Y');
            } else {
                $datePhrase = 'realizado no período de ' . $start->format('d/m/Y') . ' a ' . $end->format('d/m/Y');
            }
        }

        // Cor e watermark
        // Expressões ternárias aninhadas são como uma sequência de blocos if/else. No caso de $primaryColor,
        // primeiro verificamos se temos um $request e se ele contém um valor para 'primary_color'.
        // Se não, então verificamos se temos um $template. Se não houver, $primaryColor será igual a '#000000'.  
        $primaryColor = $request && $request->filled('primary_color') ? (string)$request->input('primary_color') : ($template ? (string)$template->options['primary_color'] : '#000000');
        $watermark = $request && $request->filled('watermark') ? trim((string)$request->input('watermark')) : ($template ? (string)$template->options['watermark'] : null);

        // A frase principal sobre o curso pode vir do request, do template ou usar o valor padrão.
        $courseLinePrefix = $request?->input('course_line_prefix') ?? $template?->course_line_prefix ?? 'Concluiu com êxito o/a ';

        // Logo e Assinatura (prioriza a enviada no form; senão usa a do template (storage público))
        $logoBase64 = $this->getCertificateImageBase64($request, $template, 'logo');
        $signatureBase64 = $this->getCertificateImageBase64($request, $template, 'signature', $event);


        return [
            // Cabeçalho e identificação
            'name'             => $participant->name,
            'event_title'      => $eventTitle,
            'event_type_name'  => $eventTypeName,
            'institution_name' => $institutionName,

            // Frases calculadas para o bloco principal
            'course_line_prefix' => $courseLinePrefix, // Texto que o usuário pode definir. Por default: "Concluiu com êxito o/a..."
            'duration_phrase'  => $durationPhrase, // "com uma carga horária total de N horas"
            'date_phrase'      => $datePhrase,     // "em DD/MM/AAAA" ou "iniciado em ... até ..."
            'ref'              => $certificate->ref,

            // Layout e mídias
            'primary_color'    => $primaryColor,
            'logoBase64'       => $logoBase64,
            'signatureBase64'  => $signatureBase64,
            'signer_name'      => $event->issuer_name,
            'signer_role'      => $event->issuer_role,
            'watermark'        => $watermark,
        ];
    }

    /**
     * Converte o conteúdo de um arquivo para uma string Base64 com o MIME type.
     *
     * @param string $fileContent
     * @param string $mimeType
     * @return string
     */
    private function fileContentToBase64(string $fileContent, string $mimeType): string
    {
        return 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
    }

    /**
     * Obtém o conteúdo de uma imagem de acordo com a hierarquia de prioridade.
     *
     * @param Request|null $request
     * @param CertificateTemplate|null $template
     * @param string $type ('logo' ou 'signature')
     * @param Event|null $event
     * @return string|null
     */
    private function getCertificateImageBase64(?Request $request, ?CertificateTemplate $template, string $type, ?Event $event = null): ?string
    {
        $fileContent = null;
        $mimeType = null;

        // Prioridade 1: Arquivo enviado na requisição
        if ($request && $request->hasFile($type)) {
            $uploadedFile = $request->file($type);
            $fileContent = $uploadedFile->get();
            $mimeType = $uploadedFile->getMimeType();
        }

        // Prioridade 2: Caminho do template
        if (!$fileContent && $template && !empty($template->options[$type . '_path'])) {
            $path = $template->options[$type . '_path'];
            if (Storage::disk('public')->exists($path)) {
                $fileContent = Storage::disk('public')->get($path);
                $mimeType = Storage::disk('public')->mimeType($path);
            }
        }

        // Prioridade 3: Caminho do evento (apenas para assinatura)
        if ($type === 'signature' && !$fileContent && !empty($event->issuer_signature_path)) {
            $path = $event->issuer_signature_path;
            if (Storage::disk('public')->exists($path)) {
                $fileContent = Storage::disk('public')->get($path);
                $mimeType = Storage::disk('public')->mimeType($path);
            }
        }

        return $fileContent ? $this->fileContentToBase64($fileContent, $mimeType) : null;
    }
    // OBS:
    // MIME, or Multipurpose Internet Mail Extensions, is a standard that extends the format of email
    // to support a much wider range of content than just plain text. It allows you to send and receive files like
    // images, audio, and video, as well as text in different character sets and with rich formatting.
    // A MIME type, also known as a media type, is a standardized label used to identify the format of a file.
    // It acts like a file extension for the internet, telling a web browser, email client, or other application
    // what kind of data it's dealing with and how to process it. For example, a web server sends a MIME type
    // along with a file so your browser knows if it's supposed to render an image, play a video, or open a PDF.
    // A Base64-encoded string is a way of representing binary data, such as images, audio files, or
    // executable files, in a text-only format. It's essentially a method for translating binary data
    // (which uses 0s and 1s) into a sequence of printable characters from the ASCII character set.
    // The name "Base64" comes from the fact that it uses a 64-character alphabet to represent the data.
}
