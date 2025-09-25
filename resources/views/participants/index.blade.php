<x-app-layout>

    @vite(['resources/js/certificates.js', 'resources/js/participants-list.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                Lista de todos os participantes registados
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

                {{-- Success --}}
                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <h1 class="font-semibold text-xl text-gray-800 leading-tight truncate">Nº de registos: {{ $totalParticipants }} </h1>

                {{-- separador --}}
                <div class="my-6 border-t border-gray-200"></div>

                {{-- ===================== Barra de busca ===================== --}}
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-2">
                    <form id="searchForm" class="flex items-center gap-2" method="GET" action="{{ route('participants.index') }}">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Procurar por nome, email ou telemóvel"
                            class="w-full sm:w-80 rounded border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900"
                        >
                        <button type="submit"
                                class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
                            Buscar
                        </button>

                        @if(request()->filled('q'))
                            <a href="{{ route('participants.index') }}"
                            class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
                            Limpar
                            </a>
                        @endif
                    </form>
                </div>

                {{-- ===================== Lista de participantes ===================== --}}
                @if($participants->isEmpty())
                    <div class="rounded border border-gray-200 p-6 text-center">
                        <p class="text-gray-600">Não há nenhum participante cadastrado.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($participants as $participant)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm participant-item"
                                data-participant-id="{{ $participant->id }}">

                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                    {{-- Dados principais do participante --}}
                                    <dl class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 text-sm">
                                        <div>
                                            <dt class="font-semibold text-gray-700">Nome</dt>
                                            <dd class="text-gray-900 break-words">{{ $participant->name ?? '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-gray-700">Email</dt>
                                            <dd class="text-gray-900 break-words">
                                                @if($participant->email)
                                                    <a href="mailto:{{ $participant->email }}" class="hover:underline">{{ $participant->email }}</a>
                                                @else - @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-gray-700">Telemóvel</dt>
                                            <dd class="text-gray-900">
                                                @if($participant->phone)
                                                    <a href="tel:{{ preg_replace('/\s+/', '', $participant->phone) }}" class="hover:underline">{{ $participant->phone }}</a>
                                                @else - @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-gray-700">Morada</dt>
                                            <dd class="text-gray-900 break-words">{{ $participant->address ?? '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-gray-700">Tipo documento</dt>
                                            <dd class="text-gray-900">{{ $participant->document_type ?? '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-gray-700">Nº documento</dt>
                                            <dd class="text-gray-900">{{ $participant->document_number ?? '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="font-semibold text-gray-700">Nacionalidade</dt>
                                            <dd class="text-gray-900">{{ $participant->nationality ?? '-' }}</dd>
                                        </div>

                                        {{-- Eventos atuais --}}
                                        <div class="lg:col-span-3 mt-2">
                                            <dt class="font-semibold text-gray-700">Eventos em que está inscrito atualmente </dt>
                                            <dd class="text-gray-900">
                                                @if($participant->events->isEmpty())
                                                    Nenhum evento
                                                @else
                                                    <ul class="list-disc list-inside space-y-1">
                                                        @foreach($participant->events as $event)
                                                            @php
                                                                $hasCertificate = $participant->certificates->contains('event_id', $event->id);
                                                            @endphp
                                                            <li>
                                                                {{ $event->title }} ({{ $event->start_at->format('d/m/Y') }} - {{ $event->end_at->format('d/m/Y') }})
                                                                -
                                                                <span class="font-medium {{ $hasCertificate ? 'text-green-600' : 'text-red-600' }}">
                                                                    {{ $hasCertificate ? 'Certificado emitido' : 'Sem certificado' }}
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </dd>
                                        </div>
                            

                                        {{-- Eventos passados (não vinculado, mas com certificado) --}}
                                        <div class="lg:col-span-3 mt-2">
                                            <dt class="font-semibold text-gray-700">Eventos em que o participante já esteve vinculado e atualmente não está mais </dt>
                                            <dd class="text-gray-900">
                                                @php
                                                    $pastCertificates = $participant->certificates->filter(fn($c) => !$participant->events->contains('id', $c->event_id));
                                                @endphp
                                                @if($pastCertificates->isEmpty())
                                                    Nenhum evento passado
                                                @else
                                                    <ul class="list-disc list-inside space-y-1">
                                                        @foreach($pastCertificates as $certificate)
                                                            <li>
                                                                {{ $certificate->event->title ?? 'Evento excluído' }}
                                                                -
                                                                <span class="font-medium text-green-600">Certificado emitido</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>

                                    {{-- Coluna de ações | CHECAR --}}
                                    <div class="lg:col-span-1">
                                        <div class="mt-1 flex flex-wrap items-center justify-end gap-2 lg:flex-col lg:items-end lg:justify-start">
                                            <button type="button"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded border border-gray-200 text-gray-700 hover:bg-gray-50"
                                                    data-toggle-edit
                                                    data-target="#edit-{{ $participant->id }}"
                                                    aria-expanded="false"
                                                    aria-controls="edit-{{ $participant->id }}">
                                                <svg class="h-5 w-5" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-edit" />
                                                </svg>
                                                <span class="sr-only">Editar</span>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Coluna de ações | CHECAR --}}

                                </div>

                                {{-- Form de edição toggle --}}
                                <div id="edit-{{ $participant->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                                    <x-participants.edit-form :participant="$participant"/>
                                </div>
                            </div>
                        @endforeach
                        @if(method_exists($participants, 'links'))
                        <div class="mt-4">
                            {{ $participants->onEachSide(1)->links() }}
                        </div>
                        @endif

                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
