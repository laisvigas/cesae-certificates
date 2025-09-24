@props(['participant'])

<form action="{{ route('participants.update', [$participant->id]) }}" method="POST" class="space-y-3 bg-gray-50 rounded-md p-4">
  @csrf
  @method('PUT')

  <div>
    <label class="block text-sm font-medium text-gray-700">Nome</label>
    <input type="text" name="name" value="{{ old('name', $participant->name) }}"
           class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Email</label>
    <input type="email" name="email" value="{{ old('email', $participant->email) }}"
           class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Telemóvel</label>
    <input type="text" name="phone" value="{{ old('phone', $participant->phone) }}"
           class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Morada</label>
    <input type="text" name="address" value="{{ old('address', $participant->address) }}"
           class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <div>
      <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
      <input type="text" name="document_type" value="{{ old('document_type', $participant->document_type) }}"
             class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Nº do Documento</label>
      <input type="text" name="document_number" value="{{ old('document_number', $participant->document_number) }}"
             class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
      <input type="text" name="nationality" value="{{ old('nationality', $participant->nationality) }}"
             class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
    </div>
  </div>

  <div class="flex items-center gap-2 pt-1">
    <button type="submit"
            class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-white text-sm hover:bg-blue-700"
            onclick="this.disabled=true; this.closest('form').submit();">
      <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-edit"/></svg>
      <span>Guardar</span>
    </button>
    <button type="button"
            class="inline-flex items-center justify-center rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
            data-cancel-edit
            data-target="#edit-{{ $participant->id }}">
      Cancelar
    </button>
  </div>
</form>
