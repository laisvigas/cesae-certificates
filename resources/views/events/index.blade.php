<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Eventos
            </h2>
            <a href="{{ route('events.create') }}" class="px-3 py-2 rounded bg-gray-900 text-white text-sm">
                Novo Evento
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

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
                                <th class="text-left py-2">Participantes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr class="border-b">
                                    <td class="py-2">{{ $event->title }}</td>
                                    <td class="py-2">{{ $event->start_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">{{ $event->end_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2">{{ $event->hours ?? '-' }}</td>
                                    <!-- Campo da tabela com numero de participantes e ícones clicáveis -->
                                    <td class="py-2">
                                        <a href="{{ route('participants.view-edit', $event, $event->id) }}"><span class="inline-flex items-center gap-1">
                                            {{ $event->participants->count() }}
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor">
                                                <path d="M280-600v-80h560v80H280Zm0 160v-80h560v80H280Zm0 160v-80h560v80H280ZM160-600q-17 0-28.5-11.5T120-640q0-17 11.5-28.5T160-680q17 0 28.5 11.5T200-640q0 17-11.5 28.5T160-600Zm0 160q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520q17 0 28.5 11.5T200-480q0 17-11.5 28.5T160-440Zm0 160q-17 0-28.5-11.5T120-320q0-17 11.5-28.5T160-360q17 0 28.5 11.5T200-320q0 17-11.5 28.5T160-280Z"/>
                                            </svg>
                                        </span></a>
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M440-200h80v-167l64 64 56-57-160-160-160 160 57 56 63-63v167ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg>
                                    </td>
                                    <!-- Botões de editar e remover eventos -->
                                    <td class="py-2 flex gap-2">  <a href="{{ route('events.view-edit', $event->id) }}"
                                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Editar </a>
                                        <a href="{{ route('events.delete', $event->id) }}"
                                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                        Remover</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
