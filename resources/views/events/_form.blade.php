@php
    /** @var \App\Models\Event|null $event */
    $isEdit = isset($event) && $event?->exists;
@endphp

<form method="POST"
      action="{{ $action }}"
      class="space-y-4"
      enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @if($isEdit)
        <input type="hidden" name="id" value="{{ $event->id }}">
    @endif

    <div>
        <label class="block text-sm font-medium mb-1">Título *</label>
        <input type="text" name="title"
               value="{{ old('title', $event->title ?? '') }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Descrição</label>
        <textarea name="description" rows="4"
                  class="w-full border rounded px-3 py-2">{{ old('description', $event->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Início *</label>
            <input type="datetime-local" name="start_at"
                   value="{{ old('start_at', isset($event->start_at) ? $event->start_at->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fim *</label>
            <input type="datetime-local" name="end_at"
                   value="{{ old('end_at', isset($event->end_at) ? $event->end_at->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Tipo de Evento{{ $typeRequired ?? false ? ' *' : '' }}</label>
        <select name="event_type_id" class="w-full border rounded px-3 py-2" {{ ($typeRequired ?? false) ? 'required' : '' }}>
            <option value="">— selecione —</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}"
                    @selected(old('event_type_id', $event->event_type_id ?? null) == $t->id)>
                    {{ $t->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="max-w-xs">
        <label class="block text-sm font-medium mb-1">Horas (opcional)</label>
        <input type="number" min="0" name="hours"
               value="{{ old('hours', $event->hours ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <fieldset class="mt-6 border rounded-lg p-4">
        <legend class="px-2 text-sm font-semibold text-gray-700">Dados do emissor</legend>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium mb-1">Instituição emissora</label>
                <input type="text" name="issuer_institution"
                       value="{{ old('issuer_institution', $event->issuer_institution ?? '') }}"
                       class="w-full border rounded px-3 py-2" placeholder="Ex.: ACME Treinamentos">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nome do responsável</label>
                <input type="text" name="issuer_name"
                       value="{{ old('issuer_name', $event->issuer_name ?? '') }}"
                       class="w-full border rounded px-3 py-2" placeholder="Ex.: Maria Silva">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Cargo do responsável</label>
                <input type="text" name="issuer_role"
                       value="{{ old('issuer_role', $event->issuer_role ?? '') }}"
                       class="w-full border rounded px-3 py-2" placeholder="Ex.: Coordenadora Pedagógica">
            </div>
        </div>
    </fieldset>

    <div class="pt-2">
        <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white">
            {{ $submitLabel }}
        </button>
    </div>
</form>
