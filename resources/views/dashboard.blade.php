{{-- resources/views/dashboard.blade.php --}}
@php use Illuminate\Support\Str; @endphp

<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-2xl text-gray-900">Dashboard</h2>
      </div>
      <div class="flex gap-2">
        <a href="{{ url('/events/create-event') }}" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black/90 transition">
          <svg class="w-4 h-4" fill="currentColor"><use href="#ms-add"/></svg>
          Novo evento
        </a>
        <a href="{{ route('participants.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50 transition">
          Participantes
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- HERO --}}
      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-50 via-white to-amber-50 ring-1 ring-gray-200 mb-8">
        <div class="p-6 sm:p-8 lg:p-10">
          <div class="flex flex-col lg:flex-row lg:justify-between gap-6">

            {{-- Saudação + dicas centralizadas vertical e horizontalmente --}}
            <div class="flex flex-col items-center justify-center text-center">
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Olá! 👋</h3>

            <div x-data="tipsWidget" class="mt-2">
                <p class="text-gray-700 max-w-2xl flex items-center justify-start gap-2">
                <span class="leading-relaxed" x-text="tip.text"></span>
                </p>
            </div>
            </div>


            {{-- LEMBRETES (compacto, máx. 5) --}}
            <div class="w-full lg:w-[520px]">
              <div class="flex items-end justify-end">
                <h4 class="text-sm font-semibold text-gray-900">Lembretes</h4>
              </div>

              {{-- lista de chips --}}
              <ul class="mt-3 flex flex-wrap gap-2 justify-end">
                @php $dot = ['high'=>'bg-red-500','medium'=>'bg-yellow-500','low'=>'bg-emerald-500']; @endphp
                @forelse($topReminders as $reminder)
                  <li class="inline-flex items-center gap-2 rounded-full ring-1 ring-gray-200 bg-white px-3 py-1.5 text-xs shadow-sm">
                    <span class="h-2.5 w-2.5 rounded-full {{ $dot[$reminder->priority] ?? 'bg-emerald-500' }}"></span>
                    <span class="max-w-[290px] truncate" title="{{ $reminder->mensagem }}">
                      {{ Str::limit($reminder->mensagem, 40) }}
                    </span>
                    <form method="POST" action="{{ route('notes.destroy', $reminder) }}" onsubmit="return confirm('Apagar este lembrete?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="ml-1 opacity-60 hover:opacity-100" aria-label="Excluir">&times;</button>
                    </form>
                  </li>
                @empty
                  <li class="text-xs text-gray-600 justify-end">Sem lembretes. Adicione um abaixo.</li>
                @endforelse
              </ul>

              {{-- Form: sempre renderiza; desativa ao atingir limite --}}
              <form method="POST" action="{{ route('notes.store') }}" class="mt-3 flex items-center justify-end gap-2">
                @csrf
                <input type="hidden" name="priority" id="rem-priority" value="medium">

                {{-- seletor de prioridade por bolinhas (Alpine opcional) --}}
                <div class="flex items-center gap-1"
                     x-data="{p:'medium'}"
                     x-init="$watch('p', v => document.getElementById('rem-priority').value = v)">
                  <button type="button" @click="p='high'"   class="h-4 w-4 rounded-full ring-2" :class="p==='high'   ? 'ring-red-500 bg-red-500'       : 'ring-red-300 bg-red-300'"     aria-label="Alta"></button>
                  <button type="button" @click="p='medium'" class="h-4 w-4 rounded-full ring-2" :class="p==='medium' ? 'ring-yellow-500 bg-yellow-500' : 'ring-yellow-300 bg-yellow-300'" aria-label="Média"></button>
                  <button type="button" @click="p='low'"    class="h-4 w-4 rounded-full ring-2" :class="p==='low'    ? 'ring-emerald-500 bg-emerald-500' : 'ring-emerald-300 bg-emerald-300'" aria-label="Baixa"></button>
                </div>

                <input name="mensagem"
                       placeholder="{{ ($limitReached ?? false) ? 'Já há muitos lembretes!' : 'Novo lembrete...' }}"
                       {{ ($limitReached ?? false) ? 'disabled' : '' }}
                       required maxlength="40"
                       class="rounded-lg border-gray-300 text-sm px-3 py-2 w-56 {{ ($limitReached ?? false) ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : '' }}">

                <button class="rounded-lg bg-gray-900 text-white text-xs px-3 py-2 hover:bg-black/90 {{ ($limitReached ?? false) ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ ($limitReached ?? false) ? 'disabled aria-disabled=true' : '' }}>
                  Adicionar
                </button>
              </form>

              {{-- Mensagem do servidor ao tentar exceder o limite --}}
              @if(session('reminders_full'))
                <div class="mt-3 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                  {{ session('reminders_full') }}
                </div>
              @endif
            </div>
            {{-- /LEMBRETES --}}
          </div>
        </div>
      </div>

      {{-- KPIs --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
          $kpi = fn($label,$value,$icon,$delta=0)=>compact('label','value','icon','delta');
          $kpis = [
            $kpi('Certificados emitidos', $certificatesCount, '#ic-certificate', $certificatesDelta ?? 0),
            $kpi('Participantes (total)', $allParticipantsCount, '#ic-group', $participantsDelta ?? 0),
            $kpi('Eventos realizados', $pastEventsCount, '#ic-event', $pastEventsDelta ?? 0),
            $kpi('Eventos a decorrer', $currentEventsCount, '#ic-ongoing', $currentEventsDelta ?? 0),
          ];
        @endphp

        @foreach($kpis as $item)
          <div class="group rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 transition hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center gap-3">
              <div class="rounded-xl bg-gray-100 p-3 group-hover:bg-gray-200 transition">
                <svg class="w-5 h-5 text-gray-700" fill="currentColor" aria-hidden="true"><use href="{{ $item['icon'] }}"/></svg>
              </div>
              <div class="min-w-0">
                <p class="text-xs text-gray-500">{{ $item['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ $item['value'] }}</p>
                @php $d = $item['delta']; @endphp
                <p class="mt-0.5 text-xs {{ $d>=0 ? 'text-green-600' : 'text-red-600' }}">
                  {{ $d>=0?'+':'' }}{{ $d }}%
                </p>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- COLUNA ESQUERDA: 2 CARDS (linha + inscrições) --}}
  <div class="lg:col-span-2 flex flex-col gap-6">

    {{-- Certificados por mês --}}
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-4 sm:p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900">Certificados gerados nos últimos 12 meses</h3>
        <div class="text-xs text-gray-500">por mês</div>
      </div>
      <div class="mt-4">
        <canvas id="certificatesLine"
                data-labels='@json($months)'
                data-series='@json($certsPerMonth)'></canvas>
      </div>
    </div>

    {{-- NOVO: Inscrições (vínculos) por mês --}}
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-4 sm:p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900">Inscrições nos últimos 12 meses</h3>
        <div class="text-xs text-gray-500">vínculos por mês</div>
      </div>
      <div class="mt-4">
        <canvas id="enrollmentsLine"
                data-labels='@json($enrollMonths)'
                data-series='@json($enrollsPerMonth)'></canvas>
      </div>
    </div>

  </div>

  {{-- COLUNA DIREITA: 2 CARDS (donut + tipos preferidos) --}}
  <div class="flex flex-col gap-6">

      {{-- Barras horizontais: tipos de evento preferidos --}}
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-4 sm:p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900">Tipos de evento preferidos</h3>
        <div class="text-xs text-gray-500">por inscrições</div>
      </div>
      <div class="mt-4">
        <canvas id="eventTypeBar"
                data-labels='@json($typeLabels)'
                data-values='@json($typeValues)'></canvas>
      </div>
    </div>

    {{-- Donut de estado dos eventos --}}
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-4 sm:p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-gray-900">Estado dos eventos</h3>
        <div class="text-xs text-gray-500">atual</div>
      </div>
      <div class="mt-4">
        <canvas id="eventsDonut"
                data-values='@json([$futureEventsCount, $currentEventsCount, $pastEventsCount])'></canvas>
      </div>
      <div class="mt-5 grid grid-cols-3 gap-2 text-xs">
        <div class="rounded-lg bg-gray-50 p-2"><p class="text-gray-500">Futuros</p><p class="font-semibold text-gray-900">{{ $futureEventsCount }}</p></div>
        <div class="rounded-lg bg-gray-50 p-2"><p class="text-gray-500">A decorrer</p><p class="font-semibold text-gray-900">{{ $currentEventsCount }}</p></div>
        <div class="rounded-lg bg-gray-50 p-2"><p class="text-gray-500">Passados</p><p class="font-semibold text-gray-900">{{ $pastEventsCount }}</p></div>
      </div>
    </div>
  </div>
</div>



  @vite(['resources/js/dashboard.js'])
</x-app-layout>
