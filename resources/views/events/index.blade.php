<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Eventos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                @if($events->isEmpty())
                    <p class="text-gray-600">Nenhum evento cadastrado ainda.</p>
                @else
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Título</th>
                                <th class="text-left py-2">Início</th>
                                <th class="text-left py-2">Fim</th>
                                <th class="text-left py-2">Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr class="border-b">
                                    <td class="py-2">{{ $event->title }}</td>
                                    <td class="py-2">{{ $event->start_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">{{ $event->end_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">{{ $event->hours ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
