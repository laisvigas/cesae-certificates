@props(['event', 'events' => collect()])

<div id="importModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
    <button id="closeImportModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
    <h2 class="text-lg font-semibold mb-4">Importar Participantes</h2>

    <form id="importForm" method="POST" action="{{ route('participants.importFromEventSimple') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="target_event_id" value="{{ $event->id }}">

      <div>
        <label for="source_event_id" class="block text-sm font-medium text-gray-700 mb-1">
          Escolha o Evento de Origem:
        </label>
        <select name="source_event_id" id="source_event_id" required
                class="w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300">
          <option value="" disabled selected>-- Selecione um evento --</option>
          @foreach ($events->where('id', '!=', $event->id) as $otherEvent)
            <option value="{{ $otherEvent->id }}">{{ $otherEvent->title }}</option>
          @endforeach
        </select>
      </div>

      <div class="flex justify-end space-x-2 pt-2">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Importar</button>
        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Cancelar</button>
      </div>
    </form>
  </div>
</div>
