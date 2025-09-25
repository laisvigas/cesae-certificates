<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Evento</h2>
            <a href="{{ url()->previous() }}" class="text-sm underline">Voltar</a>
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

                @include('events._form', [
                    'action'      => route('events.update'),
                    'method'      => 'PUT',
                    'submitLabel' => 'Salvar alterações',
                    'types'       => $types,
                    'event'       => $event,
                    'typeRequired'=> false,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
