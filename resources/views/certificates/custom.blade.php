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
            {{-- Full-bleed no mobile; arredondado e espaçoso em ≥sm --}}
            <div class="bg-white shadow rounded-none sm:rounded-lg p-4 sm:p-6">

                <!-- Success message -->
                @if (session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm flex items-start gap-2" role="alert">
                        <svg class="w-5 h-5 mt-0.5" fill="currentColor" aria-hidden="true">
                            <use href="#ms-add" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Error message -->
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-700 text-sm" role="alert">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Download / send by email form -->
                <!-- Obs:
                    -> method on <form> sets the default HTTP method for the whole form.
                        Every submit button will use that method, unless overridden.
                    -> formmethod on <button> let us override the method per button, without duplicating the form.
                        The same is true for formaction.
                        This way we can have one form, two buttins with two different methods (and avoid having to duplicate the input data) -->
                <form id="certificateForm" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Event -->
                        <!-- Obs: the data-participants attribute takes and converts the selected event data
                                (including its participants) into a JSON file,
                                which is then passed to the JavaScript EventListener.
                                The EventListener then filters the participants that
                                will be shown in the "Selecione um participante" dropdown menu below  -->
                        <div class="sm:col-span-2">
                            <label for="event_id" class="block text-sm font-medium text-gray-700">Evento</label>
                            <select
                                name="event_id"
                                id="event_id"
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

                        <!-- Participant -->
                        <div>
                            <label for="participant_id" class="block text-sm font-medium text-gray-700">Participante</label>
                            <select
                                name="participant_id"
                                id="participant_id"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                required>
                                <option value="">Selecione um participante</option>
                                <!-- Options populated dynamically via JS -->
                            </select>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email do destinatário</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                placeholder="Digite o email do participante">
                            <p class="mt-1 text-xs text-gray-500">Opcional — necessário apenas se for enviar por email.</p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="pt-2 flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <button
                            type="submit"
                            formaction="{{ route('certificates.download.custom') }}"
                            formmethod="GET"
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
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
