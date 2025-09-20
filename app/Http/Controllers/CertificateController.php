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

        // Monta dados e gera o PDF
        $pdfData = $this->buildCertificateData($event, $participant, null, $certificate);

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

        $pdfData = $this->buildCertificateData($event, $participant, null, $certificate);

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

            $pdfData = $this->buildCertificateData($event, $participant, null, $certificate);
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

        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate);

        $dompdf = Dompdf::loadView('certificates.pdf', $pdfData)->setPaper('A4', 'landscape');
        $pdf = $dompdf->output();

        return response()->streamDownload(
            fn() => print($pdf),
            "certificate-{$participant->name}.pdf"
        );
    }

    public function publicDownloadByPublicId(string $publicId)
    {
        $cert = Certificate::with(['event.type', 'participant'])
            ->where('public_id', $publicId)
            ->whereNotNull('published_at')
            ->firstOrFail();

        $pdfData = $this->buildCertificateData($cert->event, $cert->participant, null, $cert);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', $pdfData)
            ->setPaper('A4', 'landscape')
            ->output();

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

        // garantir public_id/published_at (para o link público no e-mail)
        if (empty($certificate->public_id)) {
            $certificate->public_id = (string) Str::uuid();
        }
        if (empty($certificate->published_at)) {
            $certificate->published_at = now();
        }
        $certificate->save();

        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate);
        $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->setPaper('A4', 'landscape')->output();

        // passar $certificate
        Mail::to($request->input('email'))->send(new CertificateMail($pdf, $participant->name, $certificate));

        return back()->with('success', 'Certificado enviado com sucesso para ' . $request->input('email'));
    }

    // Preview (HTML no iframe)
    public function previewCustom(Request $request)
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

        $pdfData = $this->buildCertificateData($event, $participant, $request, $certificate);
        $pdfData['preview_mode'] = true;

        return view('certificates.pdf', $pdfData);
    }

    // =========================
    // HELPERS
    // =========================

    /**
     * Monta todos os dados necessários para a Blade do certificado conforme o layout novo.
     * - frase "Concluiu com êxito…" com tipo, título, instituição, horas e datas
     * - assinatura do evento (ou ad-hoc do form), watermark, cor, logo etc.
     */
    private function buildCertificateData(Event $event, Participant $participant, ?Request $request, Certificate $certificate): array
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
            $durationPhrase = 'com uma carga horária total de ' . $h . ' ' . ($h === 1 ? 'hora' : 'horas');
        }

        // Datas (mesmo dia vs intervalo)
        $datePhrase = null;
        $start = $event->start_at;
        $end   = $event->end_at;
        if ($start && $end) {
            if ($start->isSameDay($end)) {
                $datePhrase = 'em ' . $end->format('d/m/Y');
            } else {
                $datePhrase = 'iniciado em ' . $start->format('d/m/Y') . ' até ' . $end->format('d/m/Y');
            }
        }

        // Assinatura: prioriza a enviada no form; senão usa a do evento (storage público)
        $signatureBase64 = null;
        if ($request && $request->hasFile('signature')) {
            $signatureBase64 = $this->fileToBase64($request->file('signature'));
        } elseif (!empty($event->issuer_signature_path) && Storage::disk('public')->exists($event->issuer_signature_path)) {
            $signatureBase64 = $this->filePathToBase64(Storage::disk('public')->path($event->issuer_signature_path));
        }

        // Logo opcional via form
        $logoBase64 = ($request && $request->hasFile('logo')) ? $this->fileToBase64($request->file('logo')) : null;

        // Cor e watermark
        $primaryColor = $request && $request->filled('primary_color') ? (string)$request->input('primary_color') : '#000000';
        $watermark    = $request && $request->filled('watermark') ? trim((string)$request->input('watermark')) : null;

        return [
            // Cabeçalho e identificação
            'name'             => $participant->name,
            'event_title'      => $eventTitle,
            'event_type_name'  => $eventTypeName,
            'institution_name' => $institutionName,

            // Frases calculadas para o bloco principal
            'course_line_prefix' => $request?->input('course_line_prefix', ''), // Texto que o usuário pode definir. Por default: "Concluiu com êxito o/a..."
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

    // Converte UploadedFile em data URI base64
    protected function fileToBase64(?\Illuminate\Http\UploadedFile $file): ?string
    {
        if (!$file) return null;
        $data = file_get_contents($file->getRealPath());
        if ($data === false) return null;
        $mime = $file->getMimeType() ?: 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    // Converte um caminho absoluto (ex.: do storage público) em data URI base64
    protected function filePathToBase64(string $absolutePath): ?string
    {
        if (!is_file($absolutePath)) return null;
        $data = file_get_contents($absolutePath);
        if ($data === false) return null;

        $mime = function_exists('mime_content_type') ? mime_content_type($absolutePath) : null;
        if (!$mime) $mime = 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }
}
