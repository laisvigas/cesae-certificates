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

        $allParticipantsCount = $this->participantsRegisteredCount();
        $certificatesCount    = $this->certificatesIssuedCount($now);

        // Eventos já realizados (terminaram antes de agora)
        $pastEventsCount = $this->eventsUntilTodayCount($now);

        // Eventos futuros (ainda vão começar)
        $futureEventsCount = $this->futureEventsCount($now);

        return view('dashboard', [
            'allParticipantsCount' => $allParticipantsCount,
            'certificatesCount'    => $certificatesCount,
            'pastEventsCount'      => $pastEventsCount,
            'futureEventsCount'    => $futureEventsCount,
        ]);
    }

    private function certificatesIssuedCount(Carbon $now): int
    {
        return Certificate::whereNotNull('issued_at')
            ->where('issued_at', '<=', $now)
            ->count();
    }

    // Realizados: já terminaram
    private function eventsUntilTodayCount(Carbon $now): int
    {
        return Event::whereNotNull('end_at')
            ->where('end_at', '<', $now)
            ->count();
    }

    // Marcados / futuros: ainda vão começar
    private function futureEventsCount(Carbon $now): int
    {
        return Event::whereNotNull('start_at')
            ->where('start_at', '>=', $now)
            ->count();
    }

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