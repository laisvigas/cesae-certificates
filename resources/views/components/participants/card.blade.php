@props(['participant', 'event'])

<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm participant-item"
     data-preview-url="{{ route('certificates.preview', ['participant' => $participant->id, 'event' => $event->id]) }}"
     data-participant-id="{{ $participant->id }}"
     data-event-id="{{ $event->id }}">

  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    {{-- Campos --}}
    <dl class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 text-sm">
      <div>
        <dt class="font-semibold text-gray-700">Nome</dt>
        <dd class="text-gray-900 break-words">{{ $participant->name ?? '-' }}</dd>
      </div>
      <div>
        <dt class="font-semibold text-gray-700">Email</dt>
        <dd class="text-gray-900 break-words">
          @if($participant->email)
            <a href="mailto:{{ $participant->email }}" class="hover:underline">{{ $participant->email }}</a>
          @else
            -
          @endif
        </dd>
      </div>
      <div>
        <dt class="font-semibold text-gray-700">Telemóvel</dt>
        <dd class="text-gray-900">
          @if($participant->phone)
            <a href="tel:{{ preg_replace('/\s+/', '', $participant->phone) }}" class="hover:underline">{{ $participant->phone }}</a>
          @else
            -
          @endif
        </dd>
      </div>
      <div>
        <dt class="font-semibold text-gray-700">Morada</dt>
        <dd class="text-gray-900 break-words">{{ $participant->address ?? '-' }}</dd>
      </div>
      <div>
        <dt class="font-semibold text-gray-700">Tipo documento</dt>
        <dd class="text-gray-900">{{ $participant->document_type ?? '-' }}</dd>
      </div>
      <div>
        <dt class="font-semibold text-gray-700">Nº documento</dt>
        <dd class="text-gray-900">{{ $participant->document_number ?? '-' }}</dd>
      </div>
      <div>
        <dt class="font-semibold text-gray-700">Nacionalidade</dt>
        <dd class="text-gray-900">{{ $participant->nationality ?? '-' }}</dd>
      </div>
    </dl>

    {{-- Ações --}}
    <div class="lg:col-span-1">
      <div class="mt-1 flex flex-wrap items-center justify-end gap-2 lg:flex-col lg:items-end lg:justify-start">
        {{-- Download --}}
        <a href="{{ route('certificates.download', [$event->id, $participant->id]) }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded bg-blue-600 text-white hover:bg-blue-700"
           title="Descarregar certificado" aria-label="Descarregar certificado">
          <svg class="h-5 w-5" fill="currentColor" aria-hidden="true"><use href="#ic-download" /></svg>
        </a>

        {{-- Enviar por email --}}
        <button type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded bg-blue-600 text-white hover:bg-blue-700"
                data-cert-email
                data-participant-id="{{ $participant->id }}"
                data-participant-name="{{ $participant->name }}"
                data-participant-email="{{ $participant->email }}"
                data-event-id="{{ $event->id }}"
                data-event-title="{{ $event->title }}"
                data-event-end="{{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y') }}"
                title="Enviar certificado por email" aria-label="Enviar certificado por email">
          <svg class="h-5 w-5" fill="currentColor" aria-hidden="true"><use href="#ic-single-mail" /></svg>
        </button>

        {{-- Remover --}}
        <form action="{{ route('participants.detach', [$event->id, $participant->id]) }}"
              method="POST"
              onsubmit="return confirm('Remover este participante do evento?');">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="inline-flex h-10 w-10 items-center justify-center rounded bg-red-600 text-white hover:bg-red-700"
                  title="Remover" aria-label="Remover">
            <svg class="h-5 w-5" fill="currentColor" aria-hidden="true"><use href="#ic-trash" /></svg>
          </button>
        </form>

        {{-- Editar (toggle) --}}
        <button type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-gray-700 hover:bg-gray-50"
                data-toggle-edit
                data-target="#edit-{{ $participant->id }}"
                aria-expanded="false"
                aria-controls="edit-{{ $participant->id }}">
          <svg class="h-5 w-5" fill="currentColor" aria-hidden="true"><use href="#ms-edit" /></svg>
          <span class="sr-only">Editar</span>
        </button>
      </div>
    </div>
  </div>

  {{-- Form de edição (toggle) --}}
  <div id="edit-{{ $participant->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
    <form action="{{ route('participants.update', [$participant->id]) }}" method="POST" class="space-y-3 bg-gray-50 rounded-md p-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-sm font-medium text-gray-700">Nome</label>
        <input type="text" name="name" value="{{ $participant->name }}"
               class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ $participant->email }}"
               class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Telemóvel</label>
        <input type="text" name="phone" value="{{ $participant->phone }}"
               class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Morada</label>
        <input type="text" name="address" value="{{ $participant->address }}"
               class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
          <input type="text" name="document_type" value="{{ $participant->document_type }}"
                 class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nº do Documento</label>
          <input type="text" name="document_number" value="{{ $participant->document_number }}"
                 class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
          <input type="text" name="nationality" value="{{ $participant->nationality }}"
                 class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
        </div>
      </div>

      <div class="flex items-center gap-2 pt-1">
        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-white text-sm hover:bg-blue-700"
                onclick="this.disabled=true; this.closest('form').submit();">
          <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-edit"/></svg>
          <span>Guardar</span>
        </button>
        <button type="button"
                class="inline-flex items-center justify-center rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                data-cancel-edit
                data-target="#edit-{{ $participant->id }}">
          Cancelar
        </button>
      </div>
    </form>
  </div>

  {{-- Preview flutuante --}}
  <div class="floating-preview-container">
    <iframe class="floating-preview-frame" sandbox="allow-scripts allow-same-origin"></iframe>
  </div>
</div>
