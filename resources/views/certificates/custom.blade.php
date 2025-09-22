<x-app-layout>

    @vite(['resources/js/certificates.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gerar Certificado
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

                @if (session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm flex items-start gap-2" role="alert">
                        <svg class="w-5 h-5 mt-0.5" fill="currentColor" aria-hidden="true"><use href="#ms-add" /></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-700 text-sm" role="alert">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORMULÁRIO --}}
                <!-- data-preview-url,  data-store-url e data-show-url passam as rotas
                    para as funções no js -->
                <form
                    id="certificateForm"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-6"
                    data-preview-url="{{ route('certificates.preview.custom') }}"
                    data-store-url="{{ route('certificate-templates.store') }}"
                    data-show-url="/certificate-templates"
                >
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Evento -->
                        <div class="sm:col-span-2">
                            <label for="event_id" class="block text-sm font-medium text-gray-700">Evento</label>
                            <select
                                name="event_id" id="event_id"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                required
                                data-participants='@json($events->mapWithKeys(fn($e) => [$e->id => $e->participants->map(fn($p) => ["id"=>$p->id,"name"=>$p->name])]))'>
                                <option value="">Selecione um evento</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->end_at->format('d/m/Y') }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Ao selecionar um evento, a lista de participantes ficará disponível.</p>
                        </div>

                        <!-- Participante -->
                        <div>
                            <label for="participant_id" class="block text-sm font-medium text-gray-700">Participante</label>
                            <select
                                name="participant_id" id="participant_id"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                required>
                                <option value="">Selecione um participante</option>
                            </select>
                        </div>

                        <!-- Email (opcional) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email do destinatário</label>
                            <input
                                type="email" id="email" name="email"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                placeholder="Digite o email do participante">
                            <p class="mt-1 text-xs text-gray-500">Opcional — necessário apenas se for enviar por email.</p>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Customizações ad-hoc (opcionais) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Cor da moldura -->
                        <fieldset class="sm:col-span-1">
                        <legend class="block text-sm font-medium text-gray-700">Cor da moldura</legend>

                        @php
                            // Paleta de cores prontas
                            $presets = [
                            '#111111' => 'Preto',
                            '#1e3a8a' => 'Azul',
                            '#047857' => 'Verde',
                            '#6d28d9' => 'Roxo',
                            '#f59e0b' => 'Amarelo',
                            '#b79200' => 'Dourado',
                            ];
                            $defaultColor = old('primary_color', '#6d28d9'); // Padrão roxo
                        @endphp

                        <!-- Valor real enviado no POST -->
                        <input type="hidden" name="primary_color" id="primary_color" value="{{ $defaultColor }}">

                        <div id="colorPresets" class="mt-2 flex flex-wrap items-center gap-2">
                            @foreach($presets as $hex => $label)
                            <button
                                type="button"
                                class="w-8 h-8 rounded-full border border-gray-300 ring-offset-2"
                                data-color="{{ $hex }}"
                                title="{{ $label }}"
                                style="background: {{ $hex }}"></button>
                            @endforeach

                            <!-- Seletor nativo -->
                            <label class="ml-1 inline-flex items-center gap-2 text-sm">
                            <input id="colorPicker" type="color" value="{{ $defaultColor }}" class="h-8 w-12 border rounded">
                            <span>Escolher</span>
                            </label>

                            <!-- HEX manual (opcional) -->
                            <input
                            id="primary_color_text"
                            type="text"
                            class="ml-2 block w-36 rounded border border-gray-300 px-3 py-2 text-sm"
                            placeholder="#6d28d9"
                            value="{{ $defaultColor }}">
                        </div>

                        <p class="mt-1 text-xs text-gray-500">
                            Clique numa cor pronta, escolha no seletor ou digite um HEX. (Ex.: #6d28d9)
                        </p>
                        </fieldset>

                        <!-- Logo (opcional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Logo (PNG/JPG)</label>
                            <input type="file" name="logo" accept="image/png,image/jpeg" class="mt-1 block w-full text-sm">
                            <p class="mt-1 text-xs text-gray-500">Opcional. PNG com fundo transparente fica melhor.</p>
                        </div>

                        <!-- Assinatura (opcional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assinatura (PNG/JPG)</label>
                            <input type="file" name="signature" accept="image/png,image/jpeg" class="mt-1 block w-full text-sm">
                            <p class="mt-1 text-xs text-gray-500">Opcional. PNG com transparência fica ótimo.</p>
                        </div>
                    </div>

                    <!-- Marca d'água (opcional) -->
                    <div>
                        <label for="watermark" class="block text-sm font-medium text-gray-700">
                            Marca d'água (opcional)
                        </label>
                        <input
                            type="text"
                            id="watermark"
                            name="watermark"
                            class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            placeholder="Ex.: D.L Inc."
                            value="{{ old('watermark') }}">
                        <p class="mt-1 text-xs text-gray-500">Deixe em branco para não exibir.</p>
                    </div>

                    <!-- Texto do tipo de evento -->
                    <div>
                        <label for="course_line_prefix" class="block text-sm font-medium text-gray-700">
                            Texto antes do tipo de evento (opcional)
                        </label>
                        <input
                            type="text"
                            id="course_line_prefix"
                            name="course_line_prefix"
                            class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            placeholder="Ex.: Concluiu com êxito o/a "
                            value="{{ old('course_line_prefix') }}">
                        <p class="mt-1 text-xs text-gray-500">Deixe em branco para usar “pela conclusão do”.</p>
                    </div>

                    <!-- Selecionar/Salvar Template -->
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="template_id" class="block text-sm font-medium text-gray-700">Selecionar Template</label>
                            <select name="template_id" id="template_id"
                                    class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900">
                                <option value="">-- Nenhum --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ old('template_id', $event->template_id ?? '') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="button" id="btnSaveTemplate"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                                Salvar como Template
                            </button>
                        </div>
                    </div>

                    <!-- Botões -->
                    <!-- Obs: -> method on <form> sets the default HTTP method for the whole form.
                        Every submit button will use that method, unless overridden.
                        -> formmethod on <button> let us override the method per button, without duplicating the form.
                            The same is true for formaction. This way we can have one form,
                            two buttons with two different methods (and avoid having to duplicate the input data) -->
                    <div class="pt-2 flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <button
                            type="submit"
                            formaction="{{ route('certificates.download.custom') }}"
                            formmethod="POST"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded bg-gray-900 text-white text-sm hover:bg-gray-800">
                            <span>Baixar certificado</span>
                        </button>

                        <button
                            type="submit"
                            formaction="{{ route('certificates.send.custom') }}"
                            formmethod="POST"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded bg-green-600 text-white text-sm hover:bg-green-700">
                            <span>Enviar certificado por email</span>
                        </button>

                        <!-- Preview manual -->
                        <button
                            type="button" id="btnPreview"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded border text-sm hover:bg-gray-50">
                            Pré-visualizar
                        </button>

                        <!-- Toggle auto-preview -->
                        <label class="inline-flex items-center gap-2 text-sm sm:ml-auto">
                            <input id="chkAutoPreview" type="checkbox" class="rounded border-gray-300" checked>
                            <span>Atualizar preview automaticamente</span>
                        </label>
                    </div>
                </form>

                {{-- PREVIEW ABAIXO — escala proporcional (fonte incluída) --}}
                <div id="previewContainer" class="mt-8 rounded-lg border bg-white">
                    <div class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600">
                        Pré-visualização do certificado
                    </div>
                    <div class="p-3 relative" style="min-height: 200px;">
                        <iframe
                        id="previewFrame"
                        title="Pré-visualização do certificado"
                        class="mx-auto block"
                        style="width:1123px;height:794px;max-width:100%;"
                        scrolling="no"
                        ></iframe>
                        <div class="w-full text-center mt-2">
                        <p class="text-xs text-gray-500">
                            O preview se ajusta proporcionalmente à tela.
                        </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
