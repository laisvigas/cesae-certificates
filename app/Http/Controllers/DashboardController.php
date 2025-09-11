<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Certificate;
use App\Models\Event;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $now = now(); 

        // Participantes cadastrados na base
        $allParticipantsCount = $this->participantsRegisteredCount();

        // Certificados emitidos até hoje
        $certificatesCount = $this->certificatesIssuedCount($now);

        // Total de eventos até hoje
        $eventsCount = $this->eventsUntilTodayCount($now);

        return view('dashboard', [
            'allParticipantsCount' => $allParticipantsCount,
            'certificatesCount' => $certificatesCount,
            'eventsCount' => $eventsCount,
        ]);
    }



    /**
     * Total de certificados emitidos até hoje.
     */
    private function certificatesIssuedCount(Carbon $now): int
    {
        return Certificate::whereNotNull('issued_at')
            ->where('issued_at', '<=', $now)
            ->count();
    }

    private function eventsUntilTodayCount(Carbon $now): int
    {
        return Event::whereNotNull('end_at')
        ->where('end_at', '<=', $now)
        ->count();
    }


    /**
     * Total de pessoas cadastradas (independe de participação).
     */
    private function participantsRegisteredCount(): int
    {
        return Participant::count();
    }


    /**
     * Lista de quem já participou – útil para uma tela futura. | NÃO ESTÁ SENDO USADO AINDA
     */
    private function participantsWithIssuedCertificatesList(Carbon $now)
    {
        return Participant::whereHas('certificates', function ($q) use ($now) {
                $q->whereNotNull('issued_at')
                  ->where('issued_at', '<=', $now);
            })
            ->with(['certificates:id,participant_id,issued_at'])
            ->get(['id','name','email']);
    }
}
