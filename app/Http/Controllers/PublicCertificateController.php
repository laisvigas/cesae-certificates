<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class PublicCertificateController extends Controller
{

public function show(string $publicId)
{
    // Carrega certificado + evento(+type + template) + participante
    $cert = Certificate::with(['event.type', 'event.template', 'participant'])
        ->where('public_id', $publicId)
        ->whereNotNull('published_at')
        ->firstOrFail();

    $event       = $cert->event;
    $participant = $cert->participant;
    $template    = $event->template; // pode ser null
    $options     = $template?->options ?? [];

    // ==== Dados do evento / frases (mesma lógica do buildCertificateData) ====
    $eventTypeName   = optional($event->type)->name ?? 'evento';
    $institutionName = $event->issuer_institution ?: config('app.name');
    $eventTitle      = $event->title;

    $durationPhrase = null;
    $h = $event->hours;
    if (is_numeric($h) && (int)$h > 0) {
        $h = (int)$h;
        $durationPhrase = 'com carga horária total de ' . $h . ' ' . ($h === 1 ? 'hora' : 'horas');
    }

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

    // ==== Opções do template (não há $request aqui, então usamos apenas template/options) ====
    $primaryColor = $options['primary_color'] ?? ($template->primary_color ?? '#000000');
    $watermark    = $options['watermark'] ?? ($template->watermark ?? null);
    $courseLinePrefix = $options['course_line_prefix'] ?? ($template->course_line_prefix ?? 'Concluiu com êxito o/a ');

    // ==== Logo e assinatura (prioridade: template.options -> event) ====
    $logoBase64 = null;
    if (!empty($options['logo_path']) && Storage::disk('public')->exists($options['logo_path'])) {
        $content = Storage::disk('public')->get($options['logo_path']);
        $mime = Storage::disk('public')->mimeType($options['logo_path']) ?? 'image/png';
        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    $signatureBase64 = null;
    if (!empty($options['signature_path']) && Storage::disk('public')->exists($options['signature_path'])) {
        $content = Storage::disk('public')->get($options['signature_path']);
        $mime = Storage::disk('public')->mimeType($options['signature_path']) ?? 'image/png';
        $signatureBase64 = 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    // fallback: assinatura ligada ao evento (issuer_signature_path)
    if (!$signatureBase64 && !empty($event->issuer_signature_path) && Storage::disk('public')->exists($event->issuer_signature_path)) {
        $content = Storage::disk('public')->get($event->issuer_signature_path);
        $mime = Storage::disk('public')->mimeType($event->issuer_signature_path) ?? 'image/png';
        $signatureBase64 = 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    // monta o array coerente com buildCertificateData()
    $pdfData = [
        'name'             => $participant->name,
        'event_title'      => $eventTitle,
        'event_type_name'  => $eventTypeName,
        'institution_name' => $institutionName,

        'course_line_prefix' => $courseLinePrefix,
        'duration_phrase'  => $durationPhrase,
        'date_phrase'      => $datePhrase,
        'ref'              => $cert->ref,

        'primary_color'    => $primaryColor,
        'logoBase64'       => $logoBase64,
        'signatureBase64'  => $signatureBase64,
        'signer_name'      => $event->issuer_name,
        'signer_role'      => $event->issuer_role,
        'watermark'        => $watermark,

        // para o preview web (barra de ações etc.)
        'preview_mode'     => true,
        'public_id'        => $cert->public_id,
    ];

    return view('certificates.pdf', $pdfData);
}


}
