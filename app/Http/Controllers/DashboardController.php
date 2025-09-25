<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $now = now();

        // KPIs
        $allParticipantsCount = $this->participantsRegisteredCount();
        $certificatesCount    = $this->certificatesIssuedCount($now);
        $pastEventsCount      = $this->eventsUntilTodayCount($now);
        $futureEventsCount    = $this->futureEventsCount($now);
        $currentEventsCount   = $this->currentEventsCount($now);
        $eventTypeCount       = $this->eventTypeCount();

        // Deltas
        $certificatesDelta  = $this->monthOverMonthCertificatesDelta($now);
        $participantsDelta  = $this->monthOverMonthParticipantsDelta($now);
        $pastEventsDelta    = $this->monthOverMonthPastEventsDelta($now);
        $currentEventsDelta = $this->currentEventsDeltaVsLastMonth($now);

        // Série 12 meses
        [$months, $certsPerMonth] = $this->certificatesLast12MonthsSeries($now);

        // Lembretes
        $notesByPriority = Note::orderByDesc('created_at')->get()->groupBy('priority');

        // Lembretes (compactos no topo)
        $reminderLimit = 5;

        // pega até 5, priorizando high > medium > low e mais recentes
        $topReminders = Note::orderByRaw("FIELD(priority, 'high','medium','low')")
            ->orderByDesc('created_at')
            ->take($reminderLimit)
            ->get();

        // total geral (pra saber se bateu o limite visual)
        $totalReminders = Note::count();
        $limitReached = $totalReminders > $reminderLimit;

        [$typeLabels, $typeValues] = $this->eventTypePopularity(8);
        [$enrollMonths, $enrollsPerMonth] = $this->enrollmentsLast12MonthsSeries($now);

        return view('dashboard', [
            'allParticipantsCount' => $allParticipantsCount,
            'certificatesCount'    => $certificatesCount,
            'pastEventsCount'      => $pastEventsCount,
            'futureEventsCount'    => $futureEventsCount,
            'currentEventsCount'   => $currentEventsCount,
            'eventTypeCount'       => $eventTypeCount,

            'certificatesDelta'    => $certificatesDelta,
            'participantsDelta'    => $participantsDelta,
            'pastEventsDelta'      => $pastEventsDelta,
            'currentEventsDelta'   => $currentEventsDelta,

            'months'               => $months,
            'certsPerMonth'        => $certsPerMonth,

            'topReminders'   => $topReminders,
            'reminderLimit'  => $reminderLimit,
            'totalReminders' => $totalReminders,
            'limitReached'   => $limitReached,
            'notesByPriority'=> $notesByPriority,
            'typeLabels' => $typeLabels,
            'typeValues' => $typeValues,
            'enrollMonths'      => $enrollMonths,
            'enrollsPerMonth'   => $enrollsPerMonth,
        ]);
    }


    /* ===================== KPIs básicos ===================== */

    private function certificatesIssuedCount(Carbon $now): int
    {
        return Certificate::whereNotNull('issued_at')
            ->where('issued_at', '<=', $now)
            ->count();
    }

    private function eventsUntilTodayCount(Carbon $now): int
    {
        return Event::whereNotNull('end_at')
            ->where('end_at', '<', $now)
            ->count();
    }

    private function futureEventsCount(Carbon $now): int
    {
        return Event::whereNotNull('start_at')
            ->where('start_at', '>=', $now)
            ->count();
    }

    private function currentEventsCount(Carbon $now): int
    {
        return Event::whereNotNull('start_at')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->count();
    }

    private function eventTypeCount(): int
    {
        return EventType::whereNotNull('name')->count();
    }

    private function participantsRegisteredCount(): int
    {
        return Participant::count();
    }

    /* ===================== Deltas (variação %) ===================== */

    private function percentDelta(int $current, int $previous): int
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }
        return (int) floor((($current - $previous) / $previous) * 100);
    }

    // certificados emitidos no mês atual vs mês anterior
    private function monthOverMonthCertificatesDelta(Carbon $now): int
    {
        $startCur = $now->copy()->startOfMonth();
        $endCur   = $now->copy()->endOfMonth();

        $startPrev = $startCur->copy()->subMonth()->startOfMonth();
        $endPrev   = $startCur->copy()->subMonth()->endOfMonth();

        $cur = Certificate::whereNotNull('issued_at')
            ->whereBetween('issued_at', [$startCur, $endCur])->count();

        $prev = Certificate::whereNotNull('issued_at')
            ->whereBetween('issued_at', [$startPrev, $endPrev])->count();

        return $this->percentDelta($cur, $prev);
    }

    // novos participantes criados mês atual vs anterior
    private function monthOverMonthParticipantsDelta(Carbon $now): int
    {
        $startCur = $now->copy()->startOfMonth();
        $endCur   = $now->copy()->endOfMonth();

        $startPrev = $startCur->copy()->subMonth()->startOfMonth();
        $endPrev   = $startCur->copy()->subMonth()->endOfMonth();

        $cur  = Participant::whereBetween('created_at', [$startCur, $endCur])->count();
        $prev = Participant::whereBetween('created_at', [$startPrev, $endPrev])->count();

        return $this->percentDelta($cur, $prev);
    }

    // eventos finalizados no mês atual vs mês anterior
    private function monthOverMonthPastEventsDelta(Carbon $now): int
    {
        $startCur = $now->copy()->startOfMonth();
        $endCur   = $now->copy()->endOfMonth();

        $startPrev = $startCur->copy()->subMonth()->startOfMonth();
        $endPrev   = $startCur->copy()->subMonth()->endOfMonth();

        $cur  = Event::whereNotNull('end_at')->whereBetween('end_at', [$startCur, $endCur])->count();
        $prev = Event::whereNotNull('end_at')->whereBetween('end_at', [$startPrev, $endPrev])->count();

        return $this->percentDelta($cur, $prev);
    }

    // eventos a decorrer agora vs há 1 mês (snapshot)
    private function currentEventsDeltaVsLastMonth(Carbon $now): int
    {
        $currentNow = $this->currentEventsCount($now);
        $oneMonthAgo = $now->copy()->subMonth();
        $currentThen = $this->currentEventsCount($oneMonthAgo);

        return $this->percentDelta($currentNow, $currentThen);
    }

    /* ===================== Série últimos 12 meses ===================== */

    /**
     * Retorna [labels, data] dos últimos 12 meses (baseado em issued_at de certificates).
     * labels: ["Out/2024", ..., "Set/2025"]
     * data: [int,...] 12 pontos.
     */
    private function certificatesLast12MonthsSeries(Carbon $now): array
    {
        $start = $now->copy()->startOfMonth()->subMonths(11);
        $end   = $now->copy()->endOfMonth();

        // mapa ym => total (ex.: "2025-09" => 42)
        $raw = Certificate::selectRaw('DATE_FORMAT(issued_at, "%Y-%m") as ym, COUNT(*) as total')
            ->whereNotNull('issued_at')
            ->whereBetween('issued_at', [$start, $end])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $series = [];

        $cursor = $start->copy();
        for ($i = 0; $i < 12; $i++) {
            $ym = $cursor->format('Y-m');
            // rótulo abreviado em PT-BR/PT: M/Y
            $labels[] = $cursor->translatedFormat('M/Y');
            $series[] = (int) ($raw[$ym] ?? 0);
            $cursor->addMonth();
        }

        return [$labels, $series];
    }

    private function eventTypePopularity(int $limit = 8): array
    {
        $rows = DB::table('events as e')
            ->leftJoin('event_types as t', 't.id', '=', 'e.event_type_id')
            ->leftJoin('event_participant as ep', 'ep.event_id', '=', 'e.id')
            ->selectRaw('COALESCE(t.name, "Sem tipo") as label, COUNT(ep.participant_id) as total')
            ->groupBy('e.event_type_id', 't.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $labels = $rows->pluck('label')->all();
        $values = $rows->pluck('total')->map(fn ($v) => (int)$v)->all();

        return [$labels, $values];
    }

    private function enrollmentsLast12MonthsSeries(Carbon $now): array
    {
        $start = $now->copy()->startOfMonth()->subMonths(11);
        $end   = $now->copy()->endOfMonth();

        // Mapa ym => total de vínculos criados nesse mês
        $raw = DB::table('event_participant')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $series = [];

        $cursor = $start->copy();
        for ($i = 0; $i < 12; $i++) {
            $ym = $cursor->format('Y-m');
            $labels[] = $cursor->translatedFormat('M/Y');
            $series[] = (int) ($raw[$ym] ?? 0);
            $cursor->addMonth();
        }

        return [$labels, $series];
    }


    /* ===================== NÃO USADO AINDA ===================== */

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
