<x-app-layout>

    @vite(['resources/js/certificates.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Participantes do Evento: {{ $event->title }}
            </h2>

            <!-- upload a csv file with participants data and attachs each one of them to this event in data base -->
            <!-- USING A TEST ROUTE TO CHECK IF THE FUNCTION IS WORKING (IT IS).
            There must be a problem caused by the middleware/security token that is preventing the original 'participants.importCsv' route to work -->
            <form action="/test-import-csv/{{ $event->id }}"
                method="POST"
                enctype="multipart/form-data"
                class="inline-block">
                @csrf
                <label class="cursor-pointer inline-flex items-center px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" aria-hidden="true">
                        <use href="#ms-upload_file"/>
                    </svg>
                    <span>Carregar ficheiro CSV</span>
                    <input type="file" name="csv_file" accept=".csv" class="hidden" onchange="this.form.submit()">
                </label>
            </form>


            <!-- Send certificates by email to each participant button -->
            <form action="{{ route('certificates.sendAll', $event->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-green-600 flex items-center gap-1"
                    title="Enviar certificado para todos os participantes"
                    aria-label="Enviar certificado para todos os participantes">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                        viewBox="0 -960 960 960" fill="currentColor">
                        <path
                            d="M480-480Zm0-40 320-200H160l320 200ZM160-160q-33 0-56.5-23.5T80-240v-480q0-33
                            23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v280h-80v-200L480-440
                            160-640v400h360v80H160ZM715-42l-70-40 46-78h-91v-80h91l-46-78
                            70-40 45 78 45-78 70 40-46 78h91v80h-91l46 78-70
                            40-45-78-45 78Z" />
                    </svg>
                        Enviar certificados
                </button>
            </form>






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

                @if($participants->isEmpty())
                    <p class="text-gray-600">Não há nenhum participante cadastrado neste evento ainda.</p>
                @else
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Nome</th>
                                <th class="text-left py-2">Email</th>
                                <th class="text-left py-2">Telemóvel</th>
                                <th class="text-left py-2">Morada</th>
                                <th class="text-left py-2">Tipo documento</th>
                                <th class="text-left py-2">Nº documento</th>
                                <th class="text-left py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                                <tr class="border-b">

                                    <!-- Update Participant Form -->
                                    <td class="py-2" colspan="6">
                                        <form action="{{ route('participants.update', [$participant->id]) }}" method="POST" class="flex gap-2 flex-wrap">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $participant->name }}" class="border rounded p-1" placeholder="Nome">
                                            <input type="email" name="email" value="{{ $participant->email }}" class="border rounded p-1" placeholder="Email">
                                            <input type="text" name="phone" value="{{ $participant->phone }}" class="border rounded p-1" placeholder="Telemóvel">
                                            <input type="text" name="address" value="{{ $participant->address }}" class="border rounded p-1" placeholder="Morada">
                                            <input type="text" name="document_type" value="{{ $participant->document_type }}" class="border rounded p-1" placeholder="Tipo de Documento">
                                            <input type="text" name="document_number" value="{{ $participant->document_number }}" class="border rounded p-1" placeholder="Número do Documento">
                                            <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Atualizar
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Ações -->
                                    <td class="py-2 flex gap-2">

                                        <!-- Detach Participant Form  -->
                                        <form action="{{ route('participants.detach', [$event->id, $participant->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600" title="Remover" aria-label="Remover">
                                                <svg class="w-5 h-5" fill="currentColor" aria-hidden="true">
                                                    <use href="#ms-delete" />
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Download certificate  -->
                                        <a href="{{ route('certificates.download', [$event->id, $participant->id]) }}" title="Descarregar certificado" aria-label="Descarregar certificado">
                                            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 -960 960 960" fill="currentColor" aria-hidden="true"><path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
                                            </button>
                                        </a>

                                        <!-- Send certificate by email (uses js) -->
                                        <!-- Obs: data-cert-email is a custom HTML attribute, often called a data- attribute.
                                            In HTML5, you can define custom attributes that start with data- to store extra
                                            information on html elements. The browser ignores them by default,
                                            but JavaScript can read them easily. -->
                                        <button
                                            type="button"
                                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                            data-cert-email
                                            data-participant-id="{{ $participant->id }}"
                                            data-participant-name="{{ $participant->name }}"
                                            data-participant-email="{{ $participant->email }}"
                                            data-event-id="{{ $event->id }}"
                                            data-event-title="{{ $event->title }}"
                                            data-event-end="{{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y') }}"

                                            title="Enviar certificado por email" aria-label="Enviar certificado por email"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 -960 960 960" fill="currentColor" aria-hidden="true""><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/></svg>
                                        </button>

                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Hidden form for sending certificate by email -->
                    <form id="certificate-email-form" action="{{ route('certificates.send') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="participant_id">
                        <input type="hidden" name="event_id">
                    </form>

                @endif

                <!-- Add Participant Form -->
                <div id="add-form" class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Adicionar Novo Participante ao evento:</h3>
                    <form action="{{ route('participants.storeAndAttach', $event->id) }}" method="POST" class="flex gap-2 flex-wrap">
                        @csrf
                        <input type="text" name="name" placeholder="Nome" class="border rounded p-1">
                        <input type="email" name="email" placeholder="Email" class="border rounded p-1">
                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                            Adicionar
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
