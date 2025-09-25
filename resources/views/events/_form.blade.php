@php
  /** @var \App\Models\Event|null $event */
  $isEdit = isset($event) && $event?->exists;
@endphp

<form id="event-form" method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-8">
  @csrf
  @if($method !== 'POST') @method($method) @endif
  @if($isEdit)
    <input type="hidden" name="id" value="{{ $event->id }}">
  @endif

  {{-- Básico --}}
  <section class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Título *</label>
        <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" required maxlength="120"
               class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900"
               placeholder="Ex.: Workshop de Python para Iniciantes">
        <p class="mt-1 text-xs text-gray-500">Seja claro e descritivo (até 120 caracteres).</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">
          Tipo de Evento{{ $typeRequired ?? false ? ' *' : '' }}
        </label>
        <select name="event_type_id"
                class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900"
                {{ ($typeRequired ?? false) ? 'required' : '' }}>
          <option value="">— selecione —</option>
          @foreach($types as $t)
            <option value="{{ $t->id }}"
              @selected(old('event_type_id', $event->event_type_id ?? null) == $t->id)>
              {{ $t->name }}
            </option>
          @endforeach
        </select>
      </div>
    </div> {{-- <-- FECHA o grid antes da descrição --}}

    <div x-data="descriptionCounter({ initial: @js(old('description', $event->description ?? '')) })">
      <label class="block text-sm font-medium text-gray-700">Descrição</label>
      <textarea name="description" rows="5" x-model="txt" maxlength="600"
                class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900"
                placeholder="Do que se trata o evento? Público-alvo, tópicos, requisitos…"></textarea>
      <div class="mt-1 flex items-center justify-between text-xs">
        <p class="text-gray-500">Uma boa descrição melhora as inscrições.</p>
        <p :class="txt.length>max-20 ? 'text-amber-600' : 'text-gray-400'" x-text="`${txt.length}/${max}`"></p>
      </div>
    </div>
  </section>

  {{-- Datas e duração --}}
  <section class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="dateLinker">
      <div>
        <label class="block text-sm font-medium text-gray-700">Início *</label>
        <input type="datetime-local" name="start_at"
               value="{{ old('start_at', isset($event->start_at) ? $event->start_at->format('Y-m-d\\TH:i') : '') }}"
               required
               class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Fim *</label>
        <input type="datetime-local" name="end_at"
               value="{{ old('end_at', isset($event->end_at) ? $event->end_at->format('Y-m-d\\TH:i') : '') }}"
               required
               class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="max-w-xs">
        <label class="block text-sm font-medium text-gray-700">Horas (opcional)</label>
        <input type="number" min="0" name="hours" value="{{ old('hours', $event->hours ?? '') }}"
               class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900" placeholder="Ex.: 8">
        <p class="mt-1 text-xs text-gray-500">Para certificados por carga horária.</p>
      </div>
    </div>
  </section>

  {{-- Emissor --}}
  <section class="space-y-4">
    <fieldset class="rounded-2xl border border-gray-200 p-4">
      <legend class="px-2 text-sm font-semibold text-gray-700">Dados do emissor</legend>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
        <div>
          <label class="block text-sm font-medium text-gray-700">Instituição emissora</label>
          <input type="text" name="issuer_institution" value="{{ old('issuer_institution', $event->issuer_institution ?? '') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900" placeholder="Ex.: Cesae Digital">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nome do responsável</label>
          <input type="text" name="issuer_name" value="{{ old('issuer_name', $event->issuer_name ?? '') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900" placeholder="Ex.: Marcia Santos">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Cargo do responsável</label>
          <input type="text" name="issuer_role" value="{{ old('issuer_role', $event->issuer_role ?? '') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-gray-900" placeholder="Ex.: Coord. Pedagógica">
        </div>
      </div>
    </fieldset>
  </section>
  {{-- Ações --}}
  <div class="flex items-center justify-end pt-2">
    <a href="{{ url()->previous() }}"
      class="mr-2 rounded-xl border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
      Cancelar
    </a>

    <button type="submit"
            class="rounded-xl bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black/90">
      {{ $submitLabel ?? ($isEdit ? 'Salvar alterações' : 'Criar Evento') }}
    </button>
  </div>

</form>
