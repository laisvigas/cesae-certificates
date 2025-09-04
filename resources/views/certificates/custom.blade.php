<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gerar Certificado
            </h2>
        </div>
    </x-slot>

    <div class="p-6">
        <form action="{{ route('certificates.download.custom') }}" method="GET" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Nome</label>
                <input type="text" name="name" class="border rounded w-full p-2" placeholder="Nome do participante" required>
            </div>

            <div>
                <label class="block font-medium">Curso/Programa</label>
                <input type="text" name="course" class="border rounded w-full p-2" placeholder="Nome do curso" required>
            </div>

            <div>
                <label class="block font-medium">Data</label>
                <input type="date" name="date" class="border rounded w-full p-2" required>
            </div>

            <div>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded">
                    Baixar certificado
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
