<x-app-layout>
    @vite(['resources/js/filters.js'])
    <x-slot name="header">
        <div class="flex items-center justify-between min-w-0">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                Lista de Eventos
            </h2>
            <a href="{{ route('events.create') }}" class="shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded bg-green-500 text-white text-sm hover:bg-green-800">
                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                    <use href="#ms-add" />
                </svg>
                <span class="sm:inline hidden">Novo Evento</span>
                <span class="sr-only">Novo Evento</span>
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
