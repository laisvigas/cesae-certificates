<x-app-layout>
@vite(['resources/js/certificates.js', 'resources/js/participants-list.js', 'resources/js/add-participant.js'])

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
        {{ $event->type->name ?? '—' }} - {{ $event->title }}
      </h2>
    </div>
  </x-slot>

  @php
    $hasParticipants = $participants->isNotEmpty();
    // abre "Adicionar participante" se houve validação falhada ou se há old()
    $openAdd = $errors->any() || old('name') || old('email') || old('phone') || old('address') || old('document_type') || old('document_number');
  @endphp

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">
        @if(session('success'))
          <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm">
            {{ session('success') }}
          </div>
        @endif

        {{-- Painel + Toolbar --}}
        <div class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
          <x-event-summary :event="$event" :templates="$templates"/>
          <x-participants.toolbar :event="$event" :hasParticipants="$hasParticipants"/>
        </div>

        {{-- Adicionar participante (collapsible) --}}
      <details class="rounded-lg border border-gray-200 bg-white p-4 sm:p-6 group" @if($openAdd) open @endif x-data="addParticipantForm('{{ route('participants.lookup') }}')">
        <summary class="flex items-center justify-between cursor-pointer select-none">
          <h3 class="text-base font-semibold text-gray-900">Adicionar participante</h3>
          <svg class="w-5 h-5 text-gray-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
          </svg>
        </summary>

        <div class="mt-4">
          @if ($errors->any())
            <div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
              <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('participants.storeAndAttach', $event->id) }}" method="POST"
                class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3" @submit="submitting=true">
            @csrf

            {{-- EMAIL (único obrigatório inicial) --}}
            <div class="sm:col-span-2">
              <label for="p_email" class="block text-sm font-medium text-gray-700">
                Email <span class="text-red-600">*</span>
              </label>
              <input id="p_email" type="email" name="email"
                    x-model="email"
                    @blur="lookup()"
                    @input.debounce.400ms="maybeLookup()"
                    required
                    value="{{ old('email') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="email@exemplo.com" autocomplete="email">
              <p class="text-xs mt-1" :class="statusClass()" x-text="statusMessage"></p>
            </div>

            {{-- NOME (libera só se novo cadastro OU se marcar atualizar) --}}
            <div>
              <label for="p_name" class="block text-sm font-medium text-gray-700">
                Nome <template x-if="!found"><span class="text-red-600">*</span></template>
              </label>
              <input id="p_name" type="text" name="name"
                    x-model="fields.name"
                    :disabled="isLocked"
                    :required="!found"
                    value="{{ old('name') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="Nome completo" autocomplete="name">
            </div>

            <div>
              <label for="p_phone" class="block text-sm font-medium text-gray-700">Telemóvel</label>
              <input id="p_phone" type="text" name="phone"
                    x-model="fields.phone"
                    :disabled="isLocked"
                    value="{{ old('phone') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="9 999 999 999" autocomplete="tel">
            </div>

            <div>
              <label for="p_address" class="block text-sm font-medium text-gray-700">Morada</label>
              <input id="p_address" type="text" name="address"
                    x-model="fields.address"
                    :disabled="isLocked"
                    value="{{ old('address') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="Rua, nº, cidade">
            </div>

            <div>
              <label for="p_doc_type" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
              <input id="p_doc_type" type="text" name="document_type"
                    x-model="fields.document_type"
                    :disabled="isLocked"
                    value="{{ old('document_type') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="BI / CC / Passaporte">
            </div>

            <div>
              <label for="p_doc_number" class="block text-sm font-medium text-gray-700">Nº do Documento</label>
              <input id="p_doc_number" type="text" name="document_number"
                    x-model="fields.document_number"
                    :disabled="isLocked"
                    value="{{ old('document_number') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="XXXXXXXXX">
            </div>

            <div>
              <label for="p_nationality" class="block text-sm font-medium text-gray-700">Nacionalidade</label>
              <input id="p_nationality" type="text" name="nationality"
                    x-model="fields.nationality"
                    :disabled="isLocked"
                    value="{{ old('nationality') }}"
                    class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                    placeholder="Portuguesa">
            </div>

            {{-- Opções quando já existe --}}
            <div class="sm:col-span-2 flex flex-col gap-2" x-show="found">
              <div class="flex items-center gap-2">
                <input id="update_existing" name="update_existing" type="checkbox" value="1"
                      x-model="updateExisting" @change="toggleLock()"
                      class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                <label for="update_existing" class="text-sm text-gray-700">Atualizar dados existentes deste participante</label>
              </div>

            </div>

            <div class="sm:col-span-2 flex justify-end pt-1">
              <button type="submit"
                      class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-white text-sm hover:bg-green-700 disabled:opacity-60"
                      :disabled="submitting || (!found && !canSubmitNew())">
                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-add" /></svg>
                <span x-text="found ? (updateExisting ? 'Anexar & Atualizar' : 'Anexar ao evento') : 'Criar & Anexar'"></span>
              </button>
            </div>
          </form>
        </div>
      </details>

        <div class="my-6 border-t border-gray-200"></div>

        {{-- Busca simples --}}
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-2">

          <form id="searchForm" class="flex items-center gap-2">
            <input type="text" id="searchInput" placeholder="Procurar por nome"
                   class="w-full sm:w-80 rounded border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900">
            <a href="{{ route('participants.view-edit', $event->id) }}" id="clearSearch"
               class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
              Limpar
            </a>
          </form>
        </div>

        {{-- Lista de participantes --}}
        @if($participants->isEmpty())
          <div class="rounded border border-gray-200 p-6 text-center">
            <p class="text-gray-600">Não há nenhum participante cadastrado neste evento ainda.</p>
          </div>
        @else
          <div class="space-y-3">
            @foreach($participants as $participant)
              <x-participants.card :participant="$participant" :event="$event"/>
            @endforeach
          </div>

          @if(method_exists($participants, 'links'))
            <div class="mt-4">{{ $participants->links() }}</div>
          @endif
        @endif

        {{-- Form oculto para envio de certificado por email (único) --}}
        <form id="certificate-email-form" action="{{ route('certificates.send') }}" method="POST" class="hidden">
          @csrf
          <input type="hidden" name="participant_id">
          <input type="hidden" name="event_id">
        </form>

      </div>
    </div>
  </div>

  {{-- Modal importar participantes --}}
  <x-participants.import-modal :event="$event" :events="$events"/>
</x-app-layout>
