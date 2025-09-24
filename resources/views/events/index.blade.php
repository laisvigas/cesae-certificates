<x-app-layout>
    @vite(['resources/js/filters.js'])
    <x-slot name="header">
        <div class="flex items-center justify-between min-w-0">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                Lista de Eventos
            </h2>
            <a href="{{ route('events.create') }}" class="shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded bg-gray-900 text-white text-sm hover:bg-gray-800">
                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                    <use href="#ms-add" />
                </svg>
                <span class="sm:inline hidden">Novo Evento</span>
                <span class="sr-only">Novo Evento</span>
            </a>
        </div>
    </x-slot>

    @php
        $now = now();
        $selected = request('types', []); // array de ids ou 'null'
        $typesById = collect($types)->keyBy('id'); // para chips
    @endphp

    {{-- Filtros por Tipo + Estado --}}
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form id="filters-form" method="GET" action="{{ route('events.index') }}">
          <div class="border border-gray-200 bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div class="min-w-0">
                <h3 class="text-base font-semibold text-gray-900">Filtros</h3>
                <p class="mt-1 text-sm text-gray-600">Selecione tipos e/ou estado. O filtro aplica automaticamente.</p>
              </div>

              {{-- Ações topo (desktop) --}}
              <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('events.index') }}"
                  class="inline-flex items-center px-3 py-2 rounded border text-sm hover:bg-gray-50">
                  Limpar filtros
                </a>
              </div>
            </div>

            @php
              // Selecionados vindos da query
              $selectedTypes = request('types', []);      // ids ou 'null'
              $selectedStatus = request('status', []);    // 'past','ongoing','upcoming'
              // Labels do estado
              $statusLabels = [
                  'past'    => 'Encerrado',
                  'ongoing' => 'A decorrer',
                  'upcoming'=> 'Agendado',
              ];
            @endphp

            {{-- Dropdowns + chips --}}
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">

              {{-- ===== TIPOS ===== --}}
              <div class="md:col-span-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de evento</label>

                {{-- SELECT oculto (fonte da verdade para o backend) --}}
                <select id="types-hidden" name="types[]" multiple class="hidden">
                  <option value="null" @selected(in_array('null', $selectedTypes ?? []))>— Sem tipo —</option>
                  @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected(in_array((string)$type->id, $selectedTypes ?? []))>
                      {{ $type->name }}
                    </option>
                  @endforeach
                </select>

                {{-- Botão do dropdown + limpar --}}
                <div class="flex items-center gap-2">
                  <div class="relative" id="types-dd">
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded border px-3 py-2 text-sm hover:bg-gray-50"
                            data-dd-toggle>
                      <svg class="w-4 h-4" fill="currentColor" aria-hidden="true"><use href="#ms-list"/></svg>
                      <span>Selecionar tipos</span>
                      <svg class="w-4 h-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                      </svg>
                    </button>

                    {{-- Menu --}}
                    <div class="absolute z-10 mt-2 w-64 max-h-72 overflow-auto rounded-md border border-gray-200 bg-white shadow-lg p-2 hidden"
                        data-dd-menu>


                      <div class="my-2 border-t"></div>

                      {{-- Tipos --}}
                      @foreach ($types as $type)
                        @php $isOn = in_array((string)$type->id, $selectedTypes ?? []); @endphp
                        <button type="button"
                                class="w-full flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 text-sm"
                                data-type-id="{{ $type->id }}">
                          <input type="checkbox" class="pointer-events-none" {{ $isOn ? 'checked' : '' }}>
                          <span>{{ $type->name }}</span>
                        </button>
                      @endforeach
                    </div>
                  </div>

                  <a href="{{ route('events.index') }}"
                    class="inline-flex items-center px-3 py-2 rounded border text-sm hover:bg-gray-50">
                    Limpar
                  </a>
                </div>
              </div>

              {{-- ===== ESTADO ===== --}}
              <div class="md:col-span-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>

                {{-- SELECT oculto (fonte da verdade para o backend) --}}
                <select id="status-hidden" name="status[]" multiple class="hidden">
                  @foreach (['ongoing','upcoming','past'] as $key)
                    <option value="{{ $key }}" @selected(in_array($key, $selectedStatus ?? []))>
                      {{ $statusLabels[$key] }}
                    </option>
                  @endforeach
                </select>

                {{-- Botão do dropdown + limpar --}}
                <div class="flex items-center gap-2">
                  <div class="relative" id="status-dd">
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded border px-3 py-2 text-sm hover:bg-gray-50"
                            data-dd-toggle>
                      <svg class="w-4 h-4" fill="currentColor" aria-hidden="true"><use href="#ms-list"/></svg>
                      <span>Selecionar estado</span>
                      <svg class="w-4 h-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
                      </svg>
                    </button>

                    {{-- Menu --}}
                    <div class="absolute z-10 mt-2 w-64 max-h-72 overflow-auto rounded-md border border-gray-200 bg-white shadow-lg p-2 hidden"
                        data-dd-menu>
                      @foreach (['ongoing','upcoming','past'] as $key)
                        @php $on = in_array($key, $selectedStatus ?? []); @endphp
                        <button type="button"
                                class="w-full flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 text-sm"
                                data-status-id="{{ $key }}">
                          <input type="checkbox" class="pointer-events-none" {{ $on ? 'checked' : '' }}>
                          <span>{{ $statusLabels[$key] }}</span>
                        </button>
                      @endforeach
                    </div>
                  </div>

                  {{-- limpar só status (mantém tipos) --}}
                  @php
                    $qsKeepTypes = request()->except('status', 'page');
                  @endphp
                  <a href="{{ route('events.index', $qsKeepTypes) }}"
                    class="inline-flex items-center px-3 py-2 rounded border text-sm hover:bg-gray-50">
                    Limpar estado
                  </a>
                </div>
              </div>

              {{-- ===== CHIPS (Tipos + Estado) ===== --}}
              <div class="md:col-span-1 xl:col-span-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filtros ativos</label>

                @php
                  $chips = [];

                  // Chips de tipos
                  if (!empty($selectedTypes)) {
                    foreach ($selectedTypes as $sel) {
                      $label = $sel === 'null' ? 'Sem tipo' : ($typesById[(int)$sel]->name ?? $sel);
                      $rest = array_values(array_diff($selectedTypes, [$sel]));
                      $chips[] = [
                        'label' => $label,
                        'href'  => route('events.index', array_merge(request()->except('page'), ['types' => $rest, 'status' => $selectedStatus])),
                      ];
                    }
                  }

                  // Chips de estado
                  if (!empty($selectedStatus)) {
                    foreach ($selectedStatus as $st) {
                      $lab = $statusLabels[$st] ?? $st;
                      $restS = array_values(array_diff($selectedStatus, [$st]));
                      $chips[] = [
                        'label' => $lab,
                        'href'  => route('events.index', array_merge(request()->except('page'), ['types' => $selectedTypes, 'status' => $restS])),
                      ];
                    }
                  }
                @endphp

                @if(!empty($chips))
                  <div class="flex flex-wrap gap-2">
                    @foreach($chips as $chip)
                      <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800">
                        {{ $chip['label'] }}
                        <a href="{{ $chip['href'] }}"
                          class="ml-1 rounded-full hover:bg-gray-200 p-0.5" title="Remover">
                          <svg class="w-3.5 h-3.5" fill="currentColor" aria-hidden="true"><use href="#ms-delete"/></svg>
                        </a>
                      </span>
                    @endforeach
                  </div>
                @else
                  <div class="text-sm text-gray-500">Nenhum filtro aplicado.</div>
                @endif

                {{-- Botões em mobile (limpar tudo) --}}
                <div class="mt-3 sm:hidden flex items-center gap-2">
                  <a href="{{ route('events.index') }}"
                    class="inline-flex items-center px-3 py-2 rounded border text-sm hover:bg-gray-50">
                    Limpar filtros
                  </a>
                </div>
              </div>

            </div>
          </div>
        </form>
      </div>
    </div>


    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

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
                    {{-- ======== CARDS até <xl ======== --}}
                    {{-- antes: lg:hidden -> agora: xl:hidden --}}
                    <div class="xl:hidden space-y-3">
                        @foreach($events as $event)
                            @php
                                $status = $event->end_at->lt($now) ? 'Encerrado' : ($event->start_at->gt($now) ? 'Agendado' : 'A decorrer');
                                $statusStyles = [
                                    'Encerrado' => 'bg-gray-100 text-gray-700',
                                    'Agendado'  => 'bg-blue-100 text-blue-800',
                                    'A decorrer'=> 'bg-green-100 text-green-800',
                                ];
                            @endphp
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
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $statusStyles[$status] }}">
                                        {{ $status }}
                                    </span>
                                </div>

                                <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                                    <div><span class="font-medium">Início:</span> {{ $event->start_at->format('d/m/Y H:i') }}</div>
                                    <div><span class="font-medium">Fim:</span> {{ $event->end_at->format('d/m/Y H:i') }}</div>
                                    <div><span class="font-medium">Horas:</span> {{ $event->hours ?? '-' }}</div>
                                </div>

                                <div class="mt-3 flex items-center justify-between min-w-0">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-900">
                                        {{ $event->participants->count() }}
                                        <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                            <use href="#ic-group" />
                                        </svg>
                                        <span class="sr-only">Participantes</span>
                                    </span>

                                    {{-- Ações (editar / remover) --}}
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('events.view-edit', $event->id) }}"
                                           class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
                                           title="Editar" aria-label="Editar">
                                            <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                <use href="#ms-edit" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('events.delete', $event->id) }}"
                                           class="inline-flex items-center justify-center rounded bg-red-600 p-2 text-white hover:bg-red-700"
                                           title="Remover" aria-label="Remover">
                                            <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                <use href="#ms-delete" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ======== TABELA ≥ xl  ======== --}}
                    {{-- antes: hidden lg:block -> agora: hidden xl:block --}}
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
                                @foreach($events as $event)
                                    @php
                                        $status = $event->end_at->lt($now) ? 'Encerrado' : ($event->start_at->gt($now) ? 'Agendado' : 'A decorrer');
                                        $statusStyles = [
                                            'Encerrado' => 'bg-gray-100 text-gray-700',
                                            'Agendado'  => 'bg-blue-100 text-blue-800',
                                            'A decorrer'=> 'bg-green-100 text-green-800',
                                        ];
                                    @endphp
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
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusStyles[$status] }}">
                                                {{ $status }}
                                            </span>
                                        </td>

                                        {{-- Participantes: apenas número --}}
                                        <td class="py-2 px-3">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-900">
                                                {{ $event->participants->count() }}
                                                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                    <use href="#ic-group" />
                                                </svg>
                                                <span class="sr-only">Participantes</span>
                                            </span>
                                        </td>

                                        <td class="py-2 px-3">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('events.view-edit', $event->id) }}"
                                                   class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
                                                   title="Editar" aria-label="Editar">
                                                    <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                        <use href="#ms-edit" />
                                                    </svg>
                                                </a>

                                                <a href="{{ route('events.delete', $event->id) }}"
                                                   class="inline-flex items-center justify-center rounded bg-red-600 p-2 text-white hover:bg-red-700"
                                                   title="Remover" aria-label="Remover">
                                                    <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                        <use href="#ms-delete" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- paginação (se $events for paginator) || Não está ativo --}}
                        @if(method_exists($events, 'links'))
                            <div class="mt-4">{{ $events->links() }}</div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
