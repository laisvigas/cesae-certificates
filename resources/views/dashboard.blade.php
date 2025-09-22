<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Cards container --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                {{-- Card: Certificados emitidos --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                            <use href="#ic-certificate" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900"> {{ $certificatesCount }} </div>
                        <div class="text-sm text-gray-600 truncate">certificados emitidos</div>
                    </div>
                </div>

                {{-- Card: Participantes --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                            <use href="#ic-group" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $allParticipantsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">participantes até hoje</div>
                    </div>
                </div>

                {{-- Card: Eventos realizados --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                        <use href="#ic-event" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $pastEventsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">eventos realizados</div>
                    </div>
                </div>

                {{-- Card: Eventos futuros --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                        <use href="#ic-scheduled" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $futureEventsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">eventos marcados</div>
                    </div>
                </div>

                
                {{-- Card: scheduled --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                        <use href="#ic-ongoing" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $currentEventsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">eventos a decorrer</div>
                    </div>
                </div>

                {{-- Card: xxxx --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                        <use href="#ic-event" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $eventTypeCount }}</div>
                        <div class="text-sm text-gray-600 truncate">tipos de eventos oferecidos</div>
                    </div>
                </div>
            </div>
            <hr class="mt-5 mb-5 border-gray-200">
        </div>



        <div class="py-3">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">Notas</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Prioridade Máxima -->
                    <div class="border rounded-xl shadow p-4">
                        <h3 class="text-lg font-bold text-red-700 mb-2">Prioridade Máxima</h3>

                        <ul class="space-y-2">
                            @forelse(($notesByPriority['high'] ?? collect()) as $note)
                            <li class="border rounded p-2">
                                <div class="flex justify-between items-start">
                                    <div class="text-sm text-gray-600 break-words whitespace-pre-line">
                                        {{ $note->mensagem }}
                                    </div>
                                    <form method="POST" action="{{ route('notes.destroy', $note) }}"
                                          onsubmit="return confirm('Apagar este recado?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm" aria-label="Apagar recado">x</button>
                                    </form>
                                </div>
                            </li>
                            @empty
                            <li class="text-gray-500">Sem notas.</li>
                            @endforelse
                        </ul>

                        {{-- Adicionar recado (HIGH) --}}
                        <details class="group rounded-lg border border-gray-200 p-4 mt-3"
                                 @if(session('open_note')==='high' || $errors->hasBag('note_high')) open @endif>
                            <summary class="flex items-center justify-between cursor-pointer select-none">
                                <h4 class="text-sm font-semibold text-gray-900">Adicionar recado</h4>
                                <svg class="w-5 h-5 text-gray-500 transition-transform group-open:rotate-180"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                                </svg>
                            </summary>

                            <div class="mt-4">
                                {{-- Erros desta coluna (bag: note_high) --}}
                                @if ($errors->hasBag('note_high'))
                                <div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->getBag('note_high')->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <form method="POST" action="{{ route('notes.store') }}" class="grid gap-2">
                                    @csrf
                                    <input type="hidden" name="priority" value="high">

                                    <label class="text-sm text-gray-700">Mensagem</label>
                                    <textarea name="mensagem" rows="3" required
                                              class="border rounded p-2" placeholder="Escreva o recado...">{{ old('mensagem') }}</textarea>

                                    <button class="self-start rounded bg-blue-600 px-3 py-1.5 text-white">Adicionar</button>
                                </form>
                            </div>
                        </details>
                    </div>

                    <!-- Prioridade Média -->
                    <div class="border rounded-xl shadow p-4">
                        <h3 class="text-lg font-bold text-yellow-600 mb-2">Prioridade Média</h3>

                        <ul class="space-y-2">
                            @forelse(($notesByPriority['medium'] ?? collect()) as $note)
                            <li class="border rounded p-2">
                                <div class="flex justify-between items-start">
                                    <div class="text-sm text-gray-600 break-words whitespace-pre-line">
                                        {{ $note->mensagem }}
                                    </div>
                                    <form method="POST" action="{{ route('notes.destroy', $note) }}"
                                          onsubmit="return confirm('Apagar este recado?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm" aria-label="Apagar recado">x</button>
                                    </form>
                                </div>
                            </li>
                            @empty
                            <li class="text-gray-500">Sem notas.</li>
                            @endforelse
                        </ul>

                        {{-- Adicionar recado (MEDIUM) --}}
                        <details class="group rounded-lg border border-gray-200 p-4 mt-3"
                                 @if(session('open_note')==='medium' || $errors->hasBag('note_medium')) open @endif>
                            <summary class="flex items-center justify-between cursor-pointer select-none">
                                <h4 class="text-sm font-semibold text-gray-900">Adicionar recado</h4>
                                <svg class="w-5 h-5 text-gray-500 transition-transform group-open:rotate-180"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                                </svg>
                            </summary>

                            <div class="mt-4">
                                {{-- Erros desta coluna (bag: note_medium) --}}
                                @if ($errors->hasBag('note_medium'))
                                <div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->getBag('note_medium')->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <form method="POST" action="{{ route('notes.store') }}" class="grid gap-2">
                                    @csrf
                                    <input type="hidden" name="priority" value="medium">

                                    <label class="text-sm text-gray-700">Mensagem</label>
                                    <textarea name="mensagem" rows="3" required
                                              class="border rounded p-2" placeholder="Escreva o recado...">{{ old('mensagem') }}</textarea>

                                    <button class="self-start rounded bg-blue-600 px-3 py-1.5 text-white">Adicionar</button>
                                </form>
                            </div>
                        </details>
                    </div>

                    <!-- Prioridade Baixa -->
                    <div class="border rounded-xl shadow p-4">
                        <h3 class="text-lg font-bold text-green-700 mb-2">Prioridade Baixa</h3>

                        <ul class="space-y-2">
                            @forelse(($notesByPriority['low'] ?? collect()) as $note)
                            <li class="border rounded p-2">
                                <div class="flex justify-between items-start">
                                    <div class="text-sm text-gray-600 break-words whitespace-pre-line">
                                        {{ $note->mensagem }}
                                    </div>
                                    <form method="POST" action="{{ route('notes.destroy', $note) }}"
                                          onsubmit="return confirm('Apagar este recado?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm" aria-label="Apagar recado">x</button>
                                    </form>
                                </div>
                            </li>
                            @empty
                            <li class="text-gray-500">Sem notas.</li>
                            @endforelse
                        </ul>

                        {{-- Adicionar recado (LOW) --}}
                        <details class="group rounded-lg border border-gray-200 p-4 mt-3"
                                 @if(session('open_note')==='low' || $errors->hasBag('note_low')) open @endif>
                            <summary class="flex items-center justify-between cursor-pointer select-none">
                                <h4 class="text-sm font-semibold text-gray-900">Adicionar recado</h4>
                                <svg class="w-5 h-5 text-gray-500 transition-transform group-open:rotate-180"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                                </svg>
                            </summary>

                            <div class="mt-4">
                                {{-- Erros desta coluna (bag: note_low) --}}
                                @if ($errors->hasBag('note_low'))
                                <div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->getBag('note_low')->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <form method="POST" action="{{ route('notes.store') }}" class="grid gap-2">
                                    @csrf
                                    <input type="hidden" name="priority" value="low">

                                    <label class="text-sm text-gray-700">Mensagem</label>
                                    <textarea name="mensagem" rows="3" required
                                              class="border rounded p-2" placeholder="Escreva o recado...">{{ old('mensagem') }}</textarea>

                                    <button class="self-start rounded bg-blue-600 px-3 py-1.5 text-white">Adicionar</button>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
