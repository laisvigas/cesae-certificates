@props(['event'])
<div class="flex justify-end gap-2">
  <a href="{{ route('events.view-edit', $event->id) }}"
     class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
     title="Editar" aria-label="Editar">
    <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-edit"/></svg>
  </a>

  <form action="{{ route('events.delete', $event->id) }}" method="POST"
        onsubmit="return confirm('Remover este evento?');" class="inline">
    @csrf @method('DELETE')
    <button type="submit" class="inline-flex items-center justify-center rounded bg-red-600 p-2 text-white hover:bg-red-700"
            title="Remover" aria-label="Remover">
      <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-delete"/></svg>
    </button>
  </form>
</div>
