@props(['event'])
{{-- MOBILE < xl--}}
<div class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
  <div class="flex items-start justify-between gap-2">
    <div class="min-w-0">
      <a href="{{ route('participants.view-edit', $event->id) }}"
         class="text-sm font-semibold text-blue-600 hover:underline break-words whitespace-normal"
         title="Ver participantes de {{ $event->title }}">
        {{ $event->title }}
      </a>
      <div class="mt-1 text-xs text-gray-600">
        <span class="font-medium">Tipo:</span> {{ optional($event->type)->name ?? '—' }}
      </div>
    </div>
    <x-status-badge :event="$event" class="text-[11px] px-2 py-0.5"/>
  </div>

  <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
    <div><span class="font-medium">Início:</span> {{ $event->start_at->format('d/m/Y H:i') }}</div>
    <div><span class="font-medium">Fim:</span> {{ $event->end_at->format('d/m/Y H:i') }}</div>
    <div><span class="font-medium">Horas:</span> {{ $event->hours ?? '-' }}</div>
  </div>

  <div class="mt-3 flex items-center justify-between min-w-0">
    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-900">
      {{ $event->participants_count }}
      <svg class="w-4 h-4" fill="currentColor" aria-hidden="true"><use href="#ic-group"/></svg>
      <span class="sr-only">Participantes</span>
    </span>
    <x-event-actions :event="$event"/>
  </div>
</div>
