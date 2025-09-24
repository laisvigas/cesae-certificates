<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Evento</h2>
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

                @php
                    $event = $event ?? new \App\Models\Event(); // garante objeto vazio
                @endphp

                @include('events._form', [
                    'action'       => route('events.store'),
                    'method'       => 'POST',
                    'submitLabel'  => 'Criar Evento',
                    'types'        => $types,
                    'event'        => $event,
                    'typeRequired' => true, // se quiser alinhar com o required do HTML
                ])
            </div>
        </div>
    </div>
</x-app-layout>
