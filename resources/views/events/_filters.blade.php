@props([
  'types',                 // collection/lista de tipos
  'selectedTypes' => [],   // array de ids (string) e/ou 'null'
  'selectedStatus' => [],  // array com 'past','ongoing','upcoming'
])

@php
  $typesById = collect($types)->keyBy('id');
  $statusLabels = [
      'past'    => 'Encerrado',
      'ongoing' => 'A decorrer',
      'upcoming'=> 'Agendado',
  ];
@endphp

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
                    data-dd-toggle
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    aria-controls="types-menu">
              <svg class="w-4 h-4" fill="currentColor" aria-hidden="true"><use href="#ms-list"/></svg>
              <span>Selecionar tipos</span>
              <svg class="w-4 h-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
              </svg>
            </button>

            {{-- Menu --}}
            <div id="types-menu" role="listbox" tabindex="-1"
                 class="absolute z-10 mt-2 w-64 max-h-72 overflow-auto rounded-md border border-gray-200 bg-white shadow-lg p-2 hidden"
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

        {{-- Fallback sem JS (opcional) --}}
        <noscript>
          <div class="mt-2">
            <select name="types[]" multiple class="w-full border rounded px-3 py-2">
              <option value="null" @selected(in_array('null', $selectedTypes ?? []))>— Sem tipo —</option>
              @foreach ($types as $type)
                <option value="{{ $type->id }}" @selected(in_array((string)$type->id, $selectedTypes ?? []))>
                  {{ $type->name }}
                </option>
              @endforeach
            </select>
          </div>
        </noscript>
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
                    data-dd-toggle
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    aria-controls="status-menu">
              <svg class="w-4 h-4" fill="currentColor" aria-hidden="true"><use href="#ms-list"/></svg>
              <span>Selecionar estado</span>
              <svg class="w-4 h-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
              </svg>
            </button>

            {{-- Menu --}}
            <div id="status-menu" role="listbox" tabindex="-1"
                 class="absolute z-10 mt-2 w-64 max-h-72 overflow-auto rounded-md border border-gray-200 bg-white shadow-lg p-2 hidden"
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
          @php $qsKeepTypes = request()->except('status', 'page'); @endphp
          <a href="{{ route('events.index', $qsKeepTypes) }}"
             class="inline-flex items-center px-3 py-2 rounded border text-sm hover:bg-gray-50">
            Limpar estado
          </a>
        </div>

        {{-- Fallback sem JS (opcional) --}}
        <noscript>
          <div class="mt-2">
            <select name="status[]" multiple class="w-full border rounded px-3 py-2">
              @foreach (['ongoing','upcoming','past'] as $key)
                <option value="{{ $key }}" @selected(in_array($key, $selectedStatus ?? []))>
                  {{ $statusLabels[$key] }}
                </option>
              @endforeach
            </select>
          </div>
        </noscript>
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
              $rest  = array_values(array_diff($selectedTypes, [$sel]));
              $chips[] = [
                'label' => $label,
                'href'  => route('events.index', array_merge(request()->except('page'), [
                  'types'  => $rest,
                  'status' => $selectedStatus,
                ])),
              ];
            }
          }

          // Chips de estado
          if (!empty($selectedStatus)) {
            foreach ($selectedStatus as $st) {
              $lab   = $statusLabels[$st] ?? $st;
              $restS = array_values(array_diff($selectedStatus, [$st]));
              $chips[] = [
                'label' => $lab,
                'href'  => route('events.index', array_merge(request()->except('page'), [
                  'types'  => $selectedTypes,
                  'status' => $restS,
                ])),
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
