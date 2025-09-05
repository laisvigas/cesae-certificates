<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Eventos
            </h2>
            <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-gray-900 text-white text-sm hover:bg-gray-800">
                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                    <use href="#ms-add" />
                </svg>
                <span class="sm:inline hidden">Novo Evento</span>
                <span class="sr-only">Novo Evento</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Full-bleed on mobile; rounded & roomier on ≥sm --}}
                <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

                    @if(session('success'))
                        <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5" fill="currentColor" aria-hidden="true">
                                <use href="#ms-add" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($events->isEmpty())
                        <p class="text-gray-600">Nenhum evento cadastrado ainda.</p>
                    @else
                        {{-- MOBILE: cards --}}
                        <div class="sm:hidden space-y-3">
                            @foreach($events as $event)
                                <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
                                    <div class="text-sm font-semibold text-gray-900 truncate" title="{{ $event->title }}">
                                        {{ $event->title }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-600">
                                        <div><span class="font-medium">Início:</span> {{ $event->start_at->format('d/m/Y H:i') }}</div>
                                        <div><span class="font-medium">Fim:</span> {{ $event->end_at->format('d/m/Y H:i') }}</div>
                                        <div><span class="font-medium">Horas:</span> {{ $event->hours ?? '-' }}</div>
                                    </div>

                                    <div class="mt-2 flex items-center justify-between">
                                        {{-- Participantes --}}
                                        <a href="{{ route('participants.view-edit', $event, $event->id) }}"
                                           class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-900 hover:bg-gray-200">
                                            {{ $event->participants->count() }}
                                            <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                <use href="#ms-list" />
                                            </svg>
                                            <span class="sr-only">Ver participantes</span>
                                        </a>

                                        {{-- Ações (ícones) --}}
                                        <div class="flex items-center gap-1.5">
                                            {{-- Adicionar participante (placeholder) --}}
                                            <button type="button" title="Adicionar participante" aria-label="Adicionar participante"
                                                    class="inline-flex items-center justify-center rounded border border-gray-200 p-2 text-gray-700 hover:bg-gray-50">
                                                <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-add" />
                                                </svg>
                                            </button>

                                            {{-- Importar CSV (placeholder) --}}
                                            <button type="button" title="Importar CSV" aria-label="Importar CSV"
                                                    class="inline-flex items-center justify-center rounded border border-gray-200 p-2 text-gray-700 hover:bg-gray-50">
                                                <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-upload_file" />
                                                </svg>
                                            </button>

                                            {{-- Editar --}}
                                            <a href="{{ route('events.view-edit', $event->id) }}"
                                               class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
                                               title="Editar" aria-label="Editar">
                                                <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-edit" />
                                                </svg>
                                            </a>

                                            {{-- Remover --}}
                                            <a href="{{ route('events.delete', $event->id) }}"
                                               class="inline-flex items-center justify-center rounded bg-red-600 p-2 text-white hover:bg-red-700"
                                               title="Remover" aria-label="Remover">
                                                <svg class="w-4 h-4" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-delete" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- DESKTOP/TABLET: table --}}
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                    <tr class="border-b bg-gray-50 text-gray-700">
                                        <th class="text-left py-2 px-3">Título</th>
                                        <th class="text-left py-2 px-3">Início</th>
                                        <th class="text-left py-2 px-3">Fim</th>
                                        <th class="text-left py-2 px-3">Horas</th>
                                        <th class="text-left py-2 px-3">Participantes</th>
                                        <th class="py-2 px-3 w-px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-2 px-3 truncate" title="{{ $event->title }}">{{ $event->title }}</td>
                                            <td class="py-2 px-3">{{ $event->start_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-2 px-3">{{ $event->end_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-2 px-3">{{ $event->hours ?? '-' }}</td>

                                            {{-- Participantes --}}
                                            <td class="py-2 px-3">
                                                <a href="{{ route('participants.view-edit', $event, $event->id) }}"
                                                   class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-900 hover:bg-gray-200">
                                                    {{ $event->participants->count() }}
                                                    <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                        <use href="#ms-list" />
                                                    </svg>
                                                    <span class="sr-only">Ver participantes</span>
                                                </a>
                                            </td>

                                            {{-- Ações (todos os botões juntos) --}}
                                            <td class="py-2 px-3">
                                                <div class="flex justify-end gap-2">
                                                    {{-- Adicionar participante (placeholder) --}}
                                                    <button type="button" title="Adicionar participante" aria-label="Adicionar participante"
                                                            class="inline-flex items-center justify-center rounded border border-gray-200 p-2 text-gray-700 hover:bg-gray-50">
                                                        <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                            <use href="#ms-add" />
                                                        </svg>
                                                    </button>

                                                    {{-- Importar CSV (placeholder) --}}
                                                    <button type="button" title="Importar CSV" aria-label="Importar CSV"
                                                            class="inline-flex items-center justify-center rounded border border-gray-200 p-2 text-gray-700 hover:bg-gray-50">
                                                        <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                            <use href="#ms-upload_file" />
                                                        </svg>
                                                    </button>

                                                    {{-- Editar --}}
                                                    <a href="{{ route('events.view-edit', $event->id) }}"
                                                       class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
                                                       title="Editar" aria-label="Editar">
                                                        <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                            <use href="#ms-edit" />
                                                        </svg>
                                                    </a>

                                                    {{-- Remover --}}
                                                    <a href="{{ route('events.delete', $event->id) }}"
                                                       class="inline-flex items-center justify-center rounded bg-red-600 p-2 text-white hover:bg-red-700"
                                                       title="Remover" aria-label="Remover">
                                                        <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                            <use href="#ms-delete" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
        </div>
    </div>
</x-app-layout>
