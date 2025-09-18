<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class PublicCertificateController extends Controller
{
    public function show(string $publicId)
    {
        $cert = Certificate::with(['event.type', 'participant'])
            ->where('public_id', $publicId)
            ->whereNotNull('published_at')
            ->firstOrFail();

        $event = $cert->event;
        $participant = $cert->participant;

        // ====== Montagem dos mesmos dados da buildCertificateData ======
        $eventTypeName   = optional($event->type)->name ?? 'evento';
        $institutionName = $event->issuer_institution ?: config('app.name');
        $eventTitle      = $event->title;

        $durationPhrase = null;
        $h = $event->hours;
        if (is_numeric($h) && (int)$h > 0) {
            $h = (int)$h;
            $durationPhrase = 'com uma carga horária total de ' . $h . ' ' . ($h === 1 ? 'hora' : 'horas');
        }

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

        // Assinatura (se existir no storage público)
        $signatureBase64 = null;
        if (!empty($event->issuer_signature_path) && \Storage::disk('public')->exists($event->issuer_signature_path)) {
            $absolutePath = \Storage::disk('public')->path($event->issuer_signature_path);
            $data = @file_get_contents($absolutePath);
            if ($data !== false) {
                $mime = function_exists('mime_content_type') ? mime_content_type($absolutePath) : 'image/png';
                $signatureBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }

        $logoBase64 = null; // se tiver, pode popular

        $pdfData = [
            'name'             => $participant->name,
            'event_title'      => $eventTitle,
            'event_type_name'  => $eventTypeName,
            'institution_name' => $institutionName,
            'duration_phrase'  => $durationPhrase,
            'date_phrase'      => $datePhrase,
            'ref'              => $cert->ref,
            'primary_color'    => '#000000',
            'logoBase64'       => $logoBase64,
            'signatureBase64'  => $signatureBase64,
            'signer_name'      => $event->issuer_name,
            'signer_role'      => $event->issuer_role,
            'watermark'        => null,
            'preview_mode'     => true,
            'public_id'        => $cert->public_id,
        ];

        return view('certificates.pdf', $pdfData);
    }
}
