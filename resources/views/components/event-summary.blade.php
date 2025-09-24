@props(['event', 'templates' => collect()])

<div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-4">
  <div class="flex items-start justify-between gap-4">
    <div class="min-w-0">
      <div class="text-xs text-gray-500">Evento</div>
      <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $event->title }}</h3>

      <p class="text-xs text-gray-500">
        {!! $event->description ? nl2br(e($event->description)) : '—' !!}
      </p>

      <div class="mt-2 text-sm text-gray-600 flex flex-wrap gap-x-4 gap-y-1">
        <span><span class="font-medium">Tipo:</span> {{ optional($event->type)->name ?? '—' }}</span>
        <span><span class="font-medium">Início:</span> {{ $event->start_at->format('d/m/Y H:i') }}</span>
        <span><span class="font-medium">Fim:</span> {{ $event->end_at->format('d/m/Y H:i') }}</span>
        <span><span class="font-medium">Horas:</span> {{ $event->hours ?? '—' }}</span>
        <span><span class="font-medium">Instituição:</span> {{ $event->issuer_institution ?? config('app.name') ?? '—' }}</span>
        <span><span class="font-medium">Responsável:</span> {{ $event->issuer_name ?? '—' }}</span>
        <span><span class="font-medium">Cargo:</span> {{ $event->issuer_role ?? '—' }}</span>

        <span class="font-medium">Template:</span>
        <span>
          <form
            id="event-template-form-{{ $event->id }}"
            method="POST"
            class="flex items-center gap-2"
            data-event-id="{{ $event->id }}"
            data-unassign-url="{{ route('templates.unassignFromEvent', $event->id) }}"
            data-assign-base-url="{{ url('/templates') }}"
          >
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <label for="template_id_{{ $event->id }}" class="sr-only">Selecionar template</label>
            <select
              name="template_id"
              id="template_id_{{ $event->id }}"
              class="rounded border border-gray-300 px-7 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600"
              data-template-select
            >
              <option value="">Nenhum</option>
              @foreach($templates as $template)
                <option value="{{ $template->id }}" @selected($event->template_id == $template->id)">
                  {{ $template->name }}
                </option>
              @endforeach
            </select>
          </form>
        </span>
      </div>
    </div>

    <x-status-badge :event="$event" />
  </div>
</div>
