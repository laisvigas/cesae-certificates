@props(['event', 'hasParticipants' => false])

<div class="rounded-lg border border-gray-200 bg-white p-4 flex flex-wrap items-center justify-between gap-2">
  {{-- CSV --}}
  <form action="/import-csv/{{ $event->id }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label class="cursor-pointer inline-flex items-center gap-2 rounded bg-purple-600 px-3 py-2 text-white text-sm hover:bg-purple-700"
           title="Importar participantes de um ficheiro CSV"
           aria-label="Importar participantes de um ficheiro csv">
      <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-upload_file"/></svg>
      <span>Carregar CSV</span>
      <input type="file" name="csv_file" accept=".csv" class="hidden" onchange="this.form.submit()">
    </label>
  </form>

  {{-- Importar de outro evento (abre modal) --}}
  <button id="openImportModal"
          class="inline-flex items-center gap-2 rounded px-3 py-2 text-sm bg-green-600 text-white hover:bg-green-700"
          title="Importar participantes de outro evento"
          aria-label="Importar participantes de outro evento">
    <svg class="w-5 h-5" fill="currentColor" aria-hidden="true" viewBox="0 -960 960 960">
      <path d="M440-120v-480H120v-160q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H440Zm80-80h240v-160H520v160Zm0-240h240v-160H520v160ZM200-680h560v-80H200v80ZM120-80v-80h102q-48-23-77.5-68T115-330q0-79 55.5-134.5T305-520v80q-45 0-77.5 32T195-330q0 39 24 69t61 38v-97h80v240H120Z"/>
    </svg>
    <span>Importar participantes de outro evento</span>
  </button>

  {{-- Enviar todos --}}
  <form action="{{ route('certificates.sendAll', $event->id) }}" method="POST">
    @csrf
    <button type="submit"
      @class([
        'inline-flex items-center gap-2 rounded px-3 py-2 text-sm',
        'bg-blue-600 text-white hover:bg-blue-700' => $hasParticipants,
        'bg-gray-200 text-gray-500 cursor-not-allowed' => ! $hasParticipants,
      ]) @disabled(! $hasParticipants)
      title="Enviar certificado para todos os participantes"
      aria-label="Enviar certificado para todos os participantes">
      <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ic-mail"/></svg>
      <span>Enviar certificado para todos</span>
    </button>
  </form>
</div>
