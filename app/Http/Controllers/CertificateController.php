<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;

class CertificateController extends Controller
{

    // Show custom form blade
    public function custom()
    {

        return view('certificates.custom');
    }

    // Generate PDF from custom form
    public function certificateDownloadCustom(Request $request)
    {
        $data = [
            'name' => $request->input('name', 'Nome Exemplo'),
            'course' => $request->input('course', 'Curso Exemplo'),
            'date' => $request->input('date', now()->format('d/m/Y')),
            'ref' => strtoupper(uniqid()), // gera um numero único para autenticação (podemos melhorar isso no futuro com um QR code)
        ];



        return Pdf::view('certificates.pdf', $data)
            ->name('custom_certificate.pdf')
            ->download();
    }

    // Generate PDF for event/participant
    public function certificateDownload(Event $event, Participant $participant)
    {
        // garante que o participante pertence ao evento
        if (! $event->participants()->whereKey($participant->id)->exists()) {
            abort(403, 'Participante não está vinculado a este evento.');
        }

        // cria ou pega certificado existente
        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        $data = [
            'name'   => $participant->name,
            'course' => $event->title,
            'date'   => $certificate->issued_at->format('d/m/Y'), // MUDAR PARA A DATA DE CONCLUSÃO DO EVENTO
            'ref'    => $certificate->ref,
        ];

        return Pdf::view('certificates.pdf', $data)
            ->name("certificate-{$participant->name}.pdf")
            ->download();
    }
}
