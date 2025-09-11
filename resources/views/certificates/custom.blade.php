<x-app-layout>

    @vite(['resources/js/certificates.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gerar Certificado
            </h2>
        </div>
    </x-slot>

    <!-- Success message -->
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 border border-green-300" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error message -->
    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 border border-red-300" role="alert">
            <ul class="list-disc pl-5">
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
    <div class="p-6 space-y-6">
        <form id="certificateForm" method="POST">
            @csrf

            <!-- Event -->
            <!-- Obs: the data-participants attribute takes and converts the selected event data
                    (including its participants) into a JSON file,
                    which is then passed to the JavaScript EventListener.
                    The EventListener then filters the participants that
                    will be shown in the "Selecione um participante" dropdown menu below  -->
            <div>
                <label class="block font-medium">Evento</label>
                <select name="event_id" id="event_id" class="border rounded w-full p-2" required
                        data-participants='@json($events->mapWithKeys(fn($e) => [$e->id => $e->participants->map(fn($p) => ['id'=>$p->id,'name'=>$p->name])]))'>
                    <option value="">Selecione um evento</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->end_at->format('d/m/Y') }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Participant -->
            <div>
                <label class="block font-medium">Participante</label>
                <select name="participant_id" id="participant_id" class="border rounded w-full p-2" required>
                    <option value="">Selecione um participante</option>
                    <!-- Options populated dynamically via JS -->
                </select>
            </div>

            <!-- Email -->
            <div>
                <label class="block font-medium">Email do destinatário</label>
                <input type="email" name="email" class="border rounded w-full p-2" placeholder="Digite o email do participante">
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 mt-4">
                <button type="submit"
                        formaction="{{ route('certificates.download.custom') }}"
                        formmethod="GET"
                        class="px-4 py-2 bg-gray-900 text-white rounded">
                    Baixar certificado
                </button>

                <button type="submit"
                        formaction="{{ route('certificates.send.custom') }}"
                        formmethod="POST"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Enviar certificado por email
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
