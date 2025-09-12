<x-app-layout>

    @vite(['resources/js/certificates.js', 'resources/js/participants-list.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Participantes: {{ $event->type->name }} - {{ $event->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Full-bleed no mobile; arredondado e espaçoso em ≥sm --}}
            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($participants->isEmpty())
                    <div class="rounded border border-gray-200 p-6 text-center">
                        <p class="text-gray-600">Não há nenhum participante cadastrado neste evento ainda.</p>
                    </div>
                @else

                    {{-- MOBILE e DESKTOP: cards com leitura → editar sob demanda --}}

                    <!-- Add Participant Form -->
                    <div id="add-form" class="mt-6 mb-2">
                        <h3 class="text-lg font-semibold mb-2">Adicionar Novo Participante ao evento:</h3>
                        <form action="{{ route('participants.storeAndAttach', $event->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="text" name="name" placeholder="Nome" class="w-full sm:w-auto flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                            <input type="email" name="email" placeholder="Email" class="w-full sm:w-auto flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-white text-sm hover:bg-green-700">
                                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                    <use href="#ms-add" />
                                </svg>
                                <span>Adicionar</span>
                            </button>
                        </form>
                    </div>

                    {{-- Separador para dar sensação de bloco independente --}}
                    <div class="my-6 border-t border-gray-200" role="separator" aria-hidden="true"></div>

                    <div class="flex items-center gap-2 mb-6">
                        <!-- upload a csv file with participants data and attachs each one of them to this event in data base -->
                        <!-- USING A TEST ROUTE TO CHECK IF THE FUNCTION IS WORKING (IT IS).
                        There must be a problem caused by the middleware/security token that is preventing the original 'participants.importCsv' route to work -->
                        <form action="/test-import-csv/{{ $event->id }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="inline-block">
                            @csrf
                            <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded bg-purple-600 text-white text-sm hover:bg-purple-700">
                                   
                                <!-- csv icon -->
                                    <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                                        <use href="#ms-upload_file" />
                                    </svg>

                                <span>Carregar CSV</span>
                                <input type="file" name="csv_file" accept=".csv" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>

                        <!-- Send certificates by email to each participant button -->
                        <form action="{{ route('certificates.sendAll', $event->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700"
                                    title="Enviar certificado para todos os participantes"
                                    aria-label="Enviar certificado para todos os participantes">

                                    <!-- mail icon -->
                                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                                            <use href="#ic-mail" />
                                        </svg>

                                <span>Enviar todos os certificados</span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        @foreach($participants as $participant)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                                {{-- Cabeçalho do card --}}
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $participant->name ?? 'Sem nome' }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            Participante #{{ $participant->id }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Download certificate  -->
                                        <a href="{{ route('certificates.download', [$event->id, $participant->id]) }}"
                                           class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
                                           title="Descarregar certificado" aria-label="Descarregar certificado">
                                                <!-- download icon -->
                                                <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                                                    <use href="#ic-download" />
                                                </svg>
                                        </a>

                                        <!-- Send certificate by email (uses js) -->
                                        <!-- Obs: data-cert-email is a custom HTML attribute, often called a data- attribute.
                                             In HTML5, you can define custom attributes that start with data- to store extra
                                             information on html elements. The browser ignores them by default,
                                             but JavaScript can read them easily. -->
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded bg-blue-600 p-2 text-white hover:bg-blue-700"
                                            data-cert-email
                                            data-participant-id="{{ $participant->id }}"
                                            data-participant-name="{{ $participant->name }}"
                                            data-participant-email="{{ $participant->email }}"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ $event->title }}"
                                            data-event-end="{{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y') }}"
                                            title="Enviar certificado por email" aria-label="Enviar certificado por email">
                                            <!-- single email icon -->
                                                <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                                                    <use href="#ic-single-mail" />
                                                </svg>
                                        </button>

                                        <!-- Remover do evento -->
                                        <form action="{{ route('participants.detach', [$event->id, $participant->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center rounded bg-red-600 p-2 text-white hover:bg-red-700"
                                                    title="Remover" aria-label="Remover">
                                            <!-- delete icon -->
                                                <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                                                    <use href="#ic-trash" />
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Alternar edição -->
                                        <button type="button"
                                                class="inline-flex items-center justify-center rounded border border-gray-200 p-2 text-gray-700 hover:bg-gray-50"
                                                data-toggle-edit
                                                data-target="#edit-{{ $participant->id }}"
                                                aria-expanded="false"
                                                aria-controls="edit-{{ $participant->id }}">
                                            
                                                <!-- edit icon -->
                                                <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-edit" />
                                                </svg>

                                            <span class="sr-only">Editar</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Leitura: campos empilhados --}}
                                <dl class="mt-3 grid grid-cols-1 gap-y-2 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-700">Nome</dt>
                                        <dd class="text-gray-900 break-words">{{ $participant->name ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-700">Email</dt>
                                        <dd class="text-gray-900 break-words">{{ $participant->email ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-700">Telemóvel</dt>
                                        <dd class="text-gray-900">{{ $participant->phone ?? '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-700">Morada</dt>
                                        <dd class="text-gray-900 break-words">{{ $participant->address ?? '-' }}</dd>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <dt class="font-medium text-gray-700">Tipo documento</dt>
                                            <dd class="text-gray-900">{{ $participant->document_type ?? '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-medium text-gray-700">Nº documento</dt>
                                            <dd class="text-gray-900">{{ $participant->document_number ?? '-' }}</dd>
                                        </div>
                                    </div>
                                </dl>

                                <!-- Update Participant Form (inicialmente oculto; aparece ao clicar em Editar) -->
                                <div id="edit-{{ $participant->id }}" class="mt-4 hidden">
                                    <form action="{{ route('participants.update', [$participant->id]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                                            <input type="text" name="name" value="{{ $participant->name }}"
                                                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                            <input type="email" name="email" value="{{ $participant->email }}"
                                                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Telemóvel</label>
                                            <input type="text" name="phone" value="{{ $participant->phone }}"
                                                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Morada</label>
                                            <input type="text" name="address" value="{{ $participant->address }}"
                                                   class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                                                <input type="text" name="document_type" value="{{ $participant->document_type }}"
                                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Nº do Documento</label>
                                                <input type="text" name="document_number" value="{{ $participant->document_number }}"
                                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 pt-1">
                                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-white text-sm hover:bg-blue-700">
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
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @endif

                    <!-- Hidden form for sending certificate by email -->
                    <form id="certificate-email-form" action="{{ route('certificates.send') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="participant_id">
                        <input type="hidden" name="event_id">
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
