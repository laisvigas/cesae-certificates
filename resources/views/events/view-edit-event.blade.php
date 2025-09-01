<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Evento</h2>
            <a href="{{ route('events.index') }}" class="text-sm underline">Voltar à lista</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-700 text-sm">
                        <ul class="list-disc ms-5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('events.update') }}" class="space-y-4">
                    @csrf

                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $myEvent->id }}">

                    <div>
                        <label class="block text-sm font-medium mb-1">Título *</label>
                        <input type="text" name="title"
                            value="{{ old('title', $myEvent->title) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Descrição</label>
                        <textarea name="description" rows="4"
                                class="w-full border rounded px-3 py-2">{{ old('description', $myEvent->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Início *</label>
                            <input type="datetime-local" name="start_at"
                                value="{{ old('start_at', $myEvent->start_at->format('Y-m-d\TH:i')) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Fim *</label>
                            <input type="datetime-local" name="end_at"
                                value="{{ old('end_at', $myEvent->end_at->format('Y-m-d\TH:i')) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>

                    <div class="max-w-xs">
                        <label class="block text-sm font-medium mb-1">Horas (opcional)</label>
                        <input type="number" min="0" name="hours"
                            value="{{ old('hours', $myEvent->hours) }}"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white">
                            Salvar alterações
                        </button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</x-app-layout>
