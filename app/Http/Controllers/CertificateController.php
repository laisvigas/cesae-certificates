<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
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
        $data = [
            'name' => $participant->name,
            'course' => $event->title,
            'date' => now()->format('d/m/Y'), // MUDAR PARA A DATA DE CONCLUSAO DO CURSO!
            'ref' => strtoupper(uniqid()),
        ];

        return Pdf::view('certificates.pdf', $data)
            ->name("certificate-{$participant->name}.pdf")
            ->download();
    }

}
