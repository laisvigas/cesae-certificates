<x-app-layout>

    @vite(['resources/js/certificates.js', 'resources/js/participants-list.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                {{ $event->type->name ?? '—' }} - {{ $event->title }}
            </h2>
        </div>
    </x-slot>

    @php
        $now = now();
        $hasParticipants = $participants->isNotEmpty();
        $status = $event->end_at->lt($now) ? 'Encerrado' : ($event->start_at->gt($now) ? 'Agendado' : 'A decorrer');
        $badgeClass = [
            'Encerrado' => 'bg-gray-100 text-gray-700',
            'Agendado'  => 'bg-blue-100 text-blue-800',
            'A decorrer'=> 'bg-green-100 text-green-800',
        ][$status];

        // Abre o "Adicionar participante" se houve validação falhada ou se há old() preenchido
        $openAdd = $errors->any() || old('name') || old('email') || old('phone') || old('address') || old('document_type') || old('document_number');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Full-bleed no mobile; arredondado e espaçoso em ≥sm --}}
            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

                {{-- Success --}}
                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ===================== Painel do evento + Toolbar ===================== --}}
                <div class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {{-- Meta do evento --}}
                    <div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-xs text-gray-500">Evento</div>
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $event->title }}</h3>
                                    <p class="text-xs text-gray-500">
                                    {!! $event->description ? nl2br(e($event->description)) : '—' !!}
                                    </p>
                                <div class="mt-2 text-sm text-gray-600 flex flex-wrap gap-x-4 gap-y-1">
                                    <span><span class="font-medium">Tipo:</span> {{ optional($event->type)->name ?? '—' }}</span>
                                    <span><span class="font-medium">Início:</span> {{ $event->start_at->format('d/m/Y H:i') }}</span>
                                    <span><span class="font-medium">Fim:</span> {{ $event->end_at->format('d/m/Y H:i') }}</span>
                                    <span><span class="font-medium">Horas:</span> {{ $event->hours ?? '—' }}</span>
                                    <span><span class="font-medium">Instituição:</span> {{ $event->issuer_institution ?? config('app.name') ?? '—' }}</span>
                                    <span><span class="font-medium">Responsável:</span> {{ $event->issuer_name ?? '—' }}</span>
                                    <span><span class="font-medium">Cargo:</span> {{ $event->issuer_role ?? '—' }}</span>
                                    <span class="font-medium">Template:</span>
                                    <span>
                                        {{-- Formulário para associar ou remover um template de um evento --}}
                                        <form
                                            id="event-template-form-{{ $event->id }}"
                                            method="POST"
                                            class="flex items-center gap-2"
                                            data-event-id="{{ $event->id }}"
                                            data-unassign-url="{{ route('templates.unassignFromEvent', $event->id) }}"
                                            data-assign-base-url="{{ url('/templates') }}"
                                        >
                                            @csrf
                                            {{-- Este input hidden garante que sabemos a qual evento o template será associado --}}
                                            <input type="hidden" name="event_id" value="{{ $event->id }}">

                                            <label for="template_id_{{ $event->id }}" class="sr-only">Selecionar template</label>

                                            {{-- Dropdown de templates --}}
                                            <select
                                                name="template_id"
                                                id="template_id_{{ $event->id }}"
                                                class="rounded border border-gray-300 px-7 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600"
                                                data-template-select
                                            >
                                            {{-- Opção para "remover" o template associado --}}
                                                <option value="">Nenhum</option>

                                            {{-- Lista todos os templates disponíveis --}}
                                                @foreach($templates as $template)
                                                    <option
                                                        value="{{ $template->id }}"
                                                        @selected($event->template_id == $template->id)
                                                    >
                                                        {{ $template->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </span>

                                </div>
                            </div>
                            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </div>
                    </div>

                    {{-- Toolbar principal (CSV + Enviar todos) --}}
                    <div class="rounded-lg border border-gray-200 bg-white p-4 flex flex-wrap items-center justify-between gap-2">
                        <!-- upload a csv file with participants data and attachs each one of them to this event in data base -->
                        <form action="/import-csv/{{ $event->id }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="cursor-pointer inline-flex items-center gap-2 rounded bg-purple-600 px-3 py-2 text-white text-sm hover:bg-purple-700"
                            title="Importar participantes de um ficheiro CSV"
                            aria-label="Importar participantes de um ficheiro csv">
                                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-upload_file"/></svg>
                                <span>Carregar</span>
                                <input type="file" name="csv_file" accept=".csv" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>

                            <!-- Botão para abrir o modal que importa participantes de um outro evento existente -->
                            <button id="openImportModal"
                                class="inline-flex items-center gap-2 rounded px-3 py-2 text-sm bg-green-600 text-white hover:bg-green-700"
                                title="Importar participantes de outro evento"
                                aria-label="Importar participantes de outro evento">
                                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true" viewBox="0 -960 960 960">
                                    <path d="M440-120v-480H120v-160q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H440Zm80-80h240v-160H520v160Zm0-240h240v-160H520v160ZM200-680h560v-80H200v80ZM120-80v-80h102q-48-23-77.5-68T115-330q0-79 55.5-134.5T305-520v80q-45 0-77.5 32T195-330q0 39 24 69t61 38v-97h80v240H120Z"/>
                                </svg>
                                <span>Importar</span>
                            </button>

                        <!-- Send certificates by email to each participant button -->
                        <form action="{{ route('certificates.sendAll', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                @class([
                                    'inline-flex items-center gap-2 rounded px-3 py-2 text-sm',
                                    'bg-blue-600 text-white hover:bg-blue-700' => $hasParticipants,
                                    'bg-gray-200 text-gray-500 cursor-not-allowed' => ! $hasParticipants,
                                ]) @disabled(! $hasParticipants)
                                title="Enviar certificado para todos os participantes"
                                aria-label="Enviar certificado para todos os participantes">
                                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ic-mail"/></svg>
                                <span>Enviar</span>
                            </button>
                        </form>

                    </div>
                </div>

                {{-- ===================== Form: Adicionar participante (colapsável) ===================== --}}
                <details class="rounded-lg border border-gray-200 bg-white p-4 sm:p-6 group" @if($openAdd) open @endif>
                    <summary class="flex items-center justify-between cursor-pointer select-none">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Adicionar participante</h3>
                        </div>
                        {{-- Chevron que gira quando aberto --}}
                        <svg class="w-5 h-5 text-gray-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>
                        </svg>
                    </summary>

                    {{-- Conteúdo colapsável --}}
                    <div class="mt-4">
                        {{-- Erros de validação p/ feedbakc --}}
                        @if ($errors->any())
                          <div class="mb-3 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            <ul class="list-disc pl-5">
                              @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                              @endforeach
                            </ul>
                          </div>
                        @endif

                        {{-- Nome e Email obrigatórios; restantes opcionais --}}
                        <form action="{{ route('participants.storeAndAttach', $event->id) }}"
                              method="POST"
                              class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                            @csrf

                            {{-- Nome (obrigatório) --}}
                            <div>
                                <label for="p_name" class="block text-sm font-medium text-gray-700">
                                    Nome <span class="text-red-600">*</span>
                                </label>
                                <input id="p_name" type="text" name="name" value="{{ old('name') }}" required
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="Nome completo" autocomplete="name">
                            </div>

                            {{-- Email (obrigatório) --}}
                            <div>
                                <label for="p_email" class="block text-sm font-medium text-gray-700">
                                    Email <span class="text-red-600">*</span>
                                </label>
                                <input id="p_email" type="email" name="email" value="{{ old('email') }}" required
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="email@exemplo.com" autocomplete="email">
                            </div>

                            {{-- Telemóvel (opcional) --}}
                            <div>
                                <label for="p_phone" class="block text-sm font-medium text-gray-700">Telemóvel</label>
                                <input id="p_phone" type="text" name="phone" value="{{ old('phone') }}"
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="9 999 999 999" autocomplete="tel">
                            </div>

                            {{-- Morada (opcional) --}}
                            <div>
                                <label for="p_address" class="block text-sm font-medium text-gray-700">Morada</label>
                                <input id="p_address" type="text" name="address" value="{{ old('address') }}"
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="Rua, nº, cidade">
                            </div>

                            {{-- Tipo de Documento (opcional) --}}
                            <div>
                                <label for="p_doc_type" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                                <input id="p_doc_type" type="text" name="document_type" value="{{ old('document_type') }}"
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="BI / CC / Passaporte">
                            </div>

                            {{-- Nº do Documento (opcional) --}}
                            <div>
                                <label for="p_doc_number" class="block text-sm font-medium text-gray-700">Nº do Documento</label>
                                <input id="p_doc_number" type="text" name="document_number" value="{{ old('document_number') }}"
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="XXXXXXXXX">
                            </div>


                            {{-- Nacionalidade (opcional)--}}
                            <div>
                                <label for="p_nationality" class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                                <input id="p_nationality" type="text" name="nationality" value="{{ old('nationality') }}"
                                       class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                       placeholder="Portuguesa">
                            </div>

                            <div class="sm:col-span-2 flex justify-end pt-1">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-white text-sm hover:bg-green-700">
                                    <svg class="w-5 h-5" fill="currentColor" aria-hidden="true"><use href="#ms-add" /></svg>
                                    <span>Adicionar</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </details>

                {{-- separador entre seções --}}
                <div class="my-6 border-t border-gray-200"></div>

                {{-- ===================== Barra de busca simples ===================== --}}
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-2">
                    <form method="GET" class="flex items-center gap-2">
                        {{-- mantém outros parâmetros (se houver) ao buscar --}}
                        <input type="hidden" name="keep" value="1">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Procurar por nome ou email"
                               class="w-full sm:w-80 rounded border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900">
                        <button class="rounded bg-gray-900 px-3 py-2 text-white text-sm hover:bg-gray-800">Buscar</button>
                        @if(request('q'))
                            <a href="{{ route('participants.view-edit', $event->id) }}" class="rounded border px-3 py-2 text-sm hover:bg-gray-50">Limpar</a>
                        @endif
                    </form>
                    <div class="text-sm text-gray-500 sm:ml-auto">
                        {{ method_exists($participants,'total') ? $participants->total() : $participants->count() }} participante(s)
                    </div>
                </div>

                {{-- ===================== Lista de participantes ===================== --}}
                @if($participants->isEmpty())
                    <div class="rounded border border-gray-200 p-6 text-center">
                        <p class="text-gray-600">Não há nenhum participante cadastrado neste evento ainda.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($participants as $participant)
                            {{-- ===== Card de participante com layout 3x2 + ações verticais ===== --}}
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm participant-item"
                            data-preview-url="{{ route('certificates.preview', ['participant' => $participant->id, 'event' => $event->id]) }}"
                            data-participant-id="{{ $participant->id }}"
                            data-event-id="{{ $event->id }}">

                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                    {{-- Campos: em lg ocupam 3 colunas; 3 colunas x 2 linhas (6 campos) --}}
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
                                                @else
                                                    -
                                                @endif
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="font-semibold text-gray-700">Telemóvel</dt>
                                            <dd class="text-gray-900">
                                                @if($participant->phone)
                                                    <a href="tel:{{ preg_replace('/\s+/', '', $participant->phone) }}" class="hover:underline">{{ $participant->phone }}</a>
                                                @else
                                                    -
                                                @endif
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
                                    </dl>

                                    {{-- Coluna de ações: horizontal no mobile; VERTICAL a partir de lg (ícones tamanho fixo) --}}
                                    <div class="lg:col-span-1">
                                        <div class="mt-1 flex flex-wrap items-center justify-end gap-2 lg:flex-col lg:items-end lg:justify-start">
                                            <!-- Download -->
                                            <a href="{{ route('certificates.download', [$event->id, $participant->id]) }}"
                                               class="inline-flex h-10 w-10 items-center justify-center rounded bg-blue-600 text-white hover:bg-blue-700"
                                               title="Descarregar certificado" aria-label="Descarregar certificado">
                                                <svg class="h-5 w-5" fill="currentColor" aria-hidden="true">
                                                    <use href="#ic-download" />
                                                </svg>
                                            </a>

                                            <!-- Enviar por email -->
                                            <button type="button"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded bg-blue-600 text-white hover:bg-blue-700"
                                                    data-cert-email
                                                    data-participant-id="{{ $participant->id }}"
                                                    data-participant-name="{{ $participant->name }}"
                                                    data-participant-email="{{ $participant->email }}"
                                                    data-event-id="{{ $event->id }}"
                                                    data-event-title="{{ $event->title }}"
                                                    data-event-end="{{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y') }}"
                                                    title="Enviar certificado por email" aria-label="Enviar certificado por email">
                                                <svg class="h-5 w-5" fill="currentColor" aria-hidden="true">
                                                    <use href="#ic-single-mail" />
                                                </svg>
                                            </button>

                                            <!-- Remover -->
                                            <form action="{{ route('participants.detach', [$event->id, $participant->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Remover este participante do evento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex h-10 w-10 items-center justify-center rounded bg-red-600 text-white hover:bg-red-700"
                                                        title="Remover" aria-label="Remover">
                                                    <svg class="h-5 w-5" fill="currentColor" aria-hidden="true">
                                                        <use href="#ic-trash" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <!-- Editar (toggle) -->
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
                                </div>

                                <!-- Form de edição (toggle) ocupa largura total abaixo -->
                                <div id="edit-{{ $participant->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                                    <form action="{{ route('participants.update', [$participant->id]) }}" method="POST" class="space-y-3 bg-gray-50 rounded-md p-4">
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
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                                                <input type="text" name="nationality" value="{{ $participant->nationality }}"
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
                                </div>

                                    {{-- Floating Preview frame --}}
                                    <div class="floating-preview-container">
                                        <iframe class="floating-preview-frame" sandbox="allow-scripts allow-same-origin"></iframe>
                                    </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- paginação (se $participants for paginator) || not working --}}
                    @if(method_exists($participants, 'links'))
                        <div class="mt-4">{{ $participants->links() }}</div>
                    @endif
                @endif

                {{-- Form oculto para envio de certificado por email (único) --}}
                <form id="certificate-email-form" action="{{ route('certificates.send') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="participant_id">
                    <input type="hidden" name="event_id">
                </form>


                <!-- Modal -->
                <div id="importModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">

                        <!-- Botão fechar (X) -->
                        <button id="closeImportModal"
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">
                            &times;
                        </button>

                        <h2 class="text-lg font-semibold mb-4">Importar Participantes</h2>

                        <form id="importForm" method="POST" action="{{ route('participants.importFromEventSimple') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="target_event_id" value="{{ $event->id }}">

                            <div>
                                <label for="source_event_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Escolha o Evento de Origem:
                                </label>
                                <select name="source_event_id" id="source_event_id" required
                                    class="w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300">
                                    <option value="" disabled selected>-- Selecione um evento --</option>
                                    @foreach ($events->where('id', '!=', $event->id) as $otherEvent)
                                        <option value="{{ $otherEvent->id }}">{{ $otherEvent->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end space-x-2 pt-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Importar
                                </button>
                                <button type="button" id="cancelBtn"
                                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
