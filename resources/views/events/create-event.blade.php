<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-2xl text-gray-900">Novo Evento</h2>
        </div>

      <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
        <svg class="w-4 h-4" fill="currentColor"><use href="#ms-arrow-back"/></svg>
        Voltar à lista
      </a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
          {{ session('status') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <p class="font-semibold mb-1">Por favor, corrija os erros abaixo:</p>
          <ul class="list-disc ps-5 space-y-0.5">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div x-data class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Card principal --}}
        <div class="lg:col-span-2 rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm">
          <div class="border-b border-gray-200 p-5">
            <h2 class="text-lg font-semibold text-gray-900">Detalhes do evento</h2>
            <p class="text-sm text-gray-500 mt-1">Preencha as informações principais. Campos com * são obrigatórios.</p>
          </div>

          <div class="p-5">
            @php $event = $event ?? new \App\Models\Event(); @endphp
            @include('events._form', [
              'action'       => route('events.store'),
              'method'       => 'POST',
              'submitLabel'  => 'Publicar',
              'types'        => $types,
              'event'        => $event,
              'typeRequired' => true,
            ])
          </div>
        </div>

        {{-- Sidebar (resumo em tempo real) --}}
        <aside class="lg:sticky lg:top-6 h-fit rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm" x-data="eventComposer">
          <div class="p-5">
            <h3 class="text-sm font-semibold text-gray-900">Resumo do evento</h3>
            <p class="text-xs text-gray-500">Atualiza em tempo real.</p>

            <div class="mt-4 space-y-3 text-sm">
              <div class="flex items-start gap-3">
                <div class="shrink-0 rounded-lg bg-gray-100 p-2"><svg class="w-4 h-4" fill="currentColor"><use href="#ic-event"/></svg></div>
                <div>
                  <p class="text-gray-500">Título</p>
                  <p class="font-medium text-gray-900" x-text="state.title || '—'"></p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="shrink-0 rounded-lg bg-gray-100 p-2"><svg class="w-4 h-4" fill="currentColor"><use href="#ic-calendar"/></svg></div>
                <div>
                  <p class="text-gray-500">Quando</p>
                  <p class="font-medium text-gray-900" x-text="dateRange()"></p>
                </div>
              </div>

              <div class="flex items-start gap-3">
                <div class="shrink-0 rounded-lg bg-gray-100 p-2"><svg class="w-4 h-4" fill="currentColor"><use href="#ic-tag"/></svg></div>
                <div>
                  <p class="text-gray-500">Tipo</p>
                  <p class="font-medium text-gray-900" x-text="state.typeLabel || '—'"></p>
                </div>
              </div>

            <div class="flex items-start gap-3">
            <div class="shrink-0 rounded-lg bg-gray-100 p-2">
                <svg class="w-4 h-4" fill="currentColor"><use href="#ic-clock"/></svg>
            </div>
            <div>
                <p class="text-gray-500">Duração</p>
                {{-- mostra "8h" quando o usuário digitar; vazio quando não houver valor --}}
                <p class="font-medium text-gray-900" x-text="state.hours ? `${state.hours}h` : ''"></p>
            </div>
            </div>


            <div class="mt-5 grid grid-cols-2 gap-2 items-center">
              <button type="button" @click="$dispatch('submit-event-form')" class="rounded-xl bg-gray-900 text-white px-3 py-2 text-sm hover:bg-black/90">Publicar</button>
            </div>

            <p class="mt-3 text-[11px] text-gray-500">Você pode publicar agora e editar depois.</p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</x-app-layout>
