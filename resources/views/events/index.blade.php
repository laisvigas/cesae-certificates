<x-app-layout>
    @vite(['resources/js/filters.js'])
    <x-slot name="header">
        <div class="flex items-center justify-between min-w-0">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                Lista de Eventos
            </h2>
              <a href="{{ url('/events/create-event') }}" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black/90 transition">
              <svg class="w-4 h-4" fill="currentColor"><use href="#ms-add"/></svg>
              Novo evento
            </a>
        </div>
    </x-slot>

    {{-- FILTROS PARA EVENTOS --}}
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('events._filters', [
          'types' => $types,
          'selectedTypes' => request('types', []),
          'selectedStatus' => request('status', []),
        ])
      </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

              {{-- BUSCA POR NOME (agora dentro da caixa) --}}
              <div class="mb-4">
                <form id="searchForm" class="flex flex-col sm:flex-row sm:items-center gap-2" method="GET" action="{{ route('events.index') }}">
                  <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Procurar por nome do evento"
                    class="w-full sm:w-80 rounded border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900"
                  >
                  <div class="flex items-center gap-2">
                    <button type="submit"
                            class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
                      Buscar
                    </button>
                    @if(request()->filled('q'))
                      <a href="{{ route('events.index', request()->except('q','page')) }}"
                         class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
                        Limpar
                      </a>
                    @endif
                  </div>

                  {{-- Preserva filtros ao buscar --}}
                  @foreach((array) request('types', []) as $t)
                    <input type="hidden" name="types[]" value="{{ $t }}">
                  @endforeach
                  @foreach((array) request('status', []) as $s)
                    <input type="hidden" name="status[]" value="{{ $s }}">
                  @endforeach
                </form>
              </div>

              <p class="font-semibold text-sm text-gray-800 leading-tight truncate">Nº total de Eventos: {{ $totalAllEvents }}</p>
              {{-- separador --}}
              <div class="my-6 border-t border-gray-200"></div>

                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5" fill="currentColor" aria-hidden="true">
                            <use href="#ms-add" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($events->isEmpty())
                    <p class="text-gray-600">Nenhum evento cadastrado ainda.</p>
                @else
                    {{-- CARDS até <`xl --}}
                    <div class="xl:hidden space-y-3">
                      @foreach($events as $event)
                        <x-event-card :event="$event"/>
                      @endforeach
                    </div>

                    {{-- TABELA ≥ xl --}}
                    <div class="hidden xl:block overflow-x-auto">
                      <table class="w-full border-collapse text-sm">
                        <thead>
                          <tr class="border-b bg-gray-50 text-gray-700">
                            <th class="text-left py-2 px-3">Título</th>
                            <th class="text-left py-2 px-3">Tipo</th>
                            <th class="text-left py-2 px-3">Início</th>
                            <th class="text-left py-2 px-3">Fim</th>
                            <th class="text-left py-2 px-3">Horas</th>
                            <th class="text-left py-2 px-3">Estado</th>
                            <th class="text-left py-2 px-3">Participantes</th>
                            <th class="py-2 px-3 w-px">Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                          @each('components.event-row', $events, 'event')
                        </tbody>
                      </table>

                      @if(method_exists($events, 'links'))
                        <div class="mt-4">{{ $events->links() }}</div>
                      @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
