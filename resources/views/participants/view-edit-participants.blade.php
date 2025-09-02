<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Participantes
            </h2>
            <!-- Add Participant Button (scrolls to Add Form) -->
            <a href="#add-form" class="px-3 py-2 rounded bg-gray-900 text-white text-sm">
                Novo Participante
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Participants Table -->
                @if($participants->isEmpty())
                    <p class="text-gray-600">Nenhum participante cadastrado ainda.</p>
                @else
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">#</th>
                                <th class="text-left py-2">Nome</th>
                                <th class="text-left py-2">Email</th>
                                <th class="text-left py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                                <tr class="border-b">
                                    <td class="py-2">{{ $participant->id }}</td>
                                    <td class="py-2">
                                        <!-- action: route('participants.update', $participant->id) }} -->
                                        <form action="" method="POST" class="flex gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $participant->name }}" class="border rounded p-1">
                                            <input type="email" name="email" value="{{ $participant->email }}" class="border rounded p-1">
                                            <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Salvar
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-2">
                                        <form action="{{ route('participants.destroy', $participant->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                                Remover
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <!-- Add Participant Form -->
                <div id="add-form" class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Adicionar Novo Participante</h3>
                    <form action="{{ route('participants.store') }}" method="POST" class="flex gap-2">
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
