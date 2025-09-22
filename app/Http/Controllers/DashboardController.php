<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Note;

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

        // Eventos a decorrer (começaram antes de agora e ainda não terminaram)
        $currentEventsCount = $this->currentEventsCount($now);

        // tipos de eventos oferecidos
        $eventTypeCount = $this->eventTypeCount();

        // Agrupar notas por prioridade
        $notesByPriority = Note::orderByDesc('created_at')->get()->groupBy('priority');

        return view('dashboard', [
            'allParticipantsCount' => $allParticipantsCount,
            'certificatesCount'    => $certificatesCount,
            'pastEventsCount'      => $pastEventsCount,
            'futureEventsCount'    => $futureEventsCount,
            'currentEventsCount'   => $currentEventsCount,
            'eventTypeCount'       => $eventTypeCount,
            'notesByPriority' => $notesByPriority,
        ]);
    }

    // certificados emitidos: já foram entregues
    private function certificatesIssuedCount(Carbon $now): int
    {
        return Certificate::whereNotNull('issued_at')
            ->where('issued_at', '<=', $now)
            ->count();
    }

    // eventos Realizados: já terminaram
    private function eventsUntilTodayCount(Carbon $now): int
    {
        return Event::whereNotNull('end_at')
            ->where('end_at', '<', $now)
            ->count();
    }

    // eventos marcados / futuros: ainda vão começar
    private function futureEventsCount(Carbon $now): int
    {
        return Event::whereNotNull('start_at')
            ->where('start_at', '>=', $now)
            ->count();
    }

    // eventos a decorrer
    private function currentEventsCount(Carbon $now): int
    {
        return Event::whereNotNull('start_at')
            ->where('start_at', '<=', $now)  
            ->where('end_at', '>=', $now)     
            ->count();
    }

    // tipos de eventos oferecidos
    private function eventTypeCount(): int
    {
        return EventType::whereNotNull('name')->count();
    }


    // participantes registados (total)
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