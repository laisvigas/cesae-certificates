@props(['event'])
{{-- DESKTOP ≥ xl --}}
<tr class="border-b hover:bg-gray-50 align-top">
  <td class="py-2 px-3 max-w-[22rem] whitespace-normal break-words" title="{{ $event->title }}">
    <a href="{{ route('participants.view-edit', $event->id) }}" class="text-blue-600 hover:underline">
      {{ $event->title }}
    </a>
  </td>
  <td class="py-2 px-3">
    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
      {{ optional($event->type)->name ?? '—' }}
    </span>
  </td>
  <td class="py-2 px-3 whitespace-nowrap">{{ $event->start_at->format('d/m/Y H:i') }}</td>
  <td class="py-2 px-3 whitespace-nowrap">{{ $event->end_at->format('d/m/Y H:i') }}</td>
  <td class="py-2 px-3">{{ $event->hours ?? '-' }}</td>
  <td class="py-2 px-3">
    <x-status-badge :event="$event"/>
  </td>
  <td class="py-2 px-3">
    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-900">
      {{ $event->participants_count }}
      <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ic-group"/></svg>
      <span class="sr-only">Participantes</span>
    </span>
  </td>
  <td class="py-2 px-3">
    <x-event-actions :event="$event"/>
  </td>
</tr>
