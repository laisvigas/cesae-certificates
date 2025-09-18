<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificate as CertificateModel;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $name;
    public $certificate;

    public function __construct($pdf, $name, CertificateModel $certificate)
    {
        $this->pdf = $pdf;
        $this->name = $name;
        $this->certificate = $certificate;
    }

    public function build()
    {
        $publicUrl = route('certificates.public.show', $this->certificate->public_id);

        return $this->subject('Seu Certificado')
            ->view('emails.certificate', [
                'name' => $this->name,
                'publicUrl' => $publicUrl,
            ])
            ->attachData($this->pdf, "certificate-{$this->name}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
