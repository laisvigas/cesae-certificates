<x-app-layout>

    @vite(['resources/js/certificates.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Participantes do Evento: {{ $event->title }}
            </h2>
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
                                <th class="text-left py-2">Certificado</th>
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

                                    <!-- Detach Participant Form -->
                                    <td class="py-2">
                                        <form action="{{ route('participants.detach', [$event->id, $participant->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                                Remover
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Certificate Buttons -->
                                    <td class="py-2 flex gap-2">

                                        <!-- Download certificate  -->
                                        <a href="{{ route('certificates.download', [$event->id, $participant->id]) }}">
                                            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Baixar certificado
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
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/></svg>
                                        </button>

                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Hidden form for sending certificate by email -->
                    <form id="certificate-email-form" action="{{ route('certificates.send') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="name">
                        <input type="hidden" name="course" value="Course">
                        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="email">
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
