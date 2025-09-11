<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Certificate;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\CertificateMail;
use Spatie\LaravelPdf\Facades\Pdf;
use Barryvdh\DomPDF\Facade\Pdf as Dompdf; // Using an alias for the Dompdf package to differentiate it from Spatie
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{

    // Show custom form blade
    public function custom()
    {

        $events = Event::with('participants')->orderBy('start_at', 'desc')->get(); // Pass all the events and participants in each event. Needed for the dropdown menu on the custom.blade-php

        return view('certificates.custom', compact('events'));
    }


    // ------------ FUNCTIONS USED IN THE VIEW-EDIT-PARTICIPANTS BLADE: ------------

    // Generate/find and download PDF for a participant in an event
    public function certificateDownload(Event $event, Participant $participant)
    {
        if (! $event->participants()->whereKey($participant->id)->exists()) {
            abort(403, 'Participante não está vinculado a este evento.');
        }

        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        $data = [
            'name' => $participant->name,
            'course' => $event->title,
            'date' => $event->end_at->format('d/m/Y'),
            'ref' => $certificate->ref,
        ];

        $pdf = Dompdf::loadView('certificates.pdf', $data)->output();

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
            'event_id' => 'required|exists:events,id',
        ]);

        $participant = Participant::findOrFail($data['participant_id']);
        $event = Event::findOrFail($data['event_id']);

        // Create or get existing certificate
        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        $pdfData = [
            'name' => $participant->name,
            'course' => $event->title,
            'date' => $event->end_at->format('d/m/Y'),
            'ref' => $certificate->ref,
        ];

        $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->output();

        Mail::to($participant->email)->send(new CertificateMail($pdf, $participant->name));

        return back()->with('success', 'Certificado enviado com sucesso para ' . $participant->email);
    }

    // Do a loop on the previous function and
    // send a certificate by email to each and all participants in the event
    // (there is a different, specific route do call for it)
    public function sendAll(Event $event)
    {
        $participants = $event->participants;

        if ($participants->isEmpty()) {
            return back()->with('error', 'Não há participantes para este evento.');
        }

        foreach ($participants as $participant) {
            // tries to find an existing certificate for the participant. If it cant find one, creates one
            $certificate = Certificate::firstOrCreate(
                ['event_id' => $event->id, 'participant_id' => $participant->id],
                ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
            );

            // Data for the PDF
            $pdfData = [
                'name'  => $participant->name,
                'course'=> $event->title,
                'date'  => $event->end_at->format('d/m/Y'),
                'ref'   => $certificate->ref,
            ];

            // Generate PDF
            $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->output();

            // Send email if participant has a valid email
            if (!empty($participant->email)) {
                Mail::to($participant->email)->send(new CertificateMail($pdf, $participant->name));
            }
        }

        return back()->with('success', 'Certificados enviados com sucesso para todos os participantes.');
    }


    // ------------ FUNCTIONS USED IN THE CUSTOM BLADE: ------------

    // Generate and download PDF from custom form
    public function certificateDownloadCustom(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'participant_id' => 'required|exists:participants,id',
        ]);

        $event = Event::findOrFail($data['event_id']);
        $participant = Participant::findOrFail($data['participant_id']);

        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        $pdfData = [
            'name' => $participant->name,
            'course' => $event->title,
            'date' => $event->end_at->format('d/m/Y'),
            'ref' => $certificate->ref,
        ];

        $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->output();

        return response()->streamDownload(
            fn() => print($pdf),
            "certificate-{$participant->name}.pdf"
        );
    }


    // Generates PDF and send it to the email provided on the custom form
    public function sendCustom(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'participant_id' => 'required|exists:participants,id',
            'email' => 'required|email',
        ]);

        $event = Event::findOrFail($data['event_id']);
        $participant = Participant::findOrFail($data['participant_id']);

        // Create or get existing certificate
        $certificate = Certificate::firstOrCreate(
            ['event_id' => $event->id, 'participant_id' => $participant->id],
            ['ref' => strtoupper(Str::random(12)), 'issued_at' => now()]
        );

        $pdfData = [
            'name' => $participant->name,
            'course' => $event->title,
            'date' => $event->end_at->format('d/m/Y'),
            'ref' => $certificate->ref,
        ];

        $pdf = Dompdf::loadView('certificates.pdf', $pdfData)->output();

        Mail::to($data['email'])->send(new CertificateMail($pdf, $participant->name));

        return back()->with('success', 'Certificado enviado com sucesso para ' . $data['email']);
    }



}
