<x-app-layout>

    @vite(['resources/js/certificates.js'])

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gerar Certificado
            </h2>
        </div>
    </x-slot>

    <div class="p-6 space-y-6">
        <!-- Download Form -->
        <form id="downloadForm" action="{{ route('certificates.download.custom') }}" method="GET" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Nome</label>
                <input type="text" id="name" name="name" class="border rounded w-full p-2" placeholder="Nome do participante" required>
            </div>

            <div>
                <label class="block font-medium">Curso/Programa</label>
                <input type="text" id="course" name="course" class="border rounded w-full p-2" placeholder="Nome do curso" required>
            </div>

            <div>
                <label class="block font-medium">Data</label>
                <input type="date" id="date" name="date" class="border rounded w-full p-2" required>
            </div>

            <div>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded">
                    Baixar certificado
                </button>
            </div>
        </form>

        <!-- Send Email Form -->
        <form id="emailForm" action="{{ route('certificates.send.custom') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium">Email do destinatário</label>
                <input type="email" id="email" name="email" class="border rounded w-full p-2" placeholder="Digite o email do participante" required>
            </div>

            <!-- Hidden inputs that will be auto-filled with javascrip (to copy the custom form’s fields into the email form automatically) -->
            <input type="hidden" id="email_name" name="name">
            <input type="hidden" id="email_course" name="course">
            <input type="hidden" id="email_date" name="date">

            <div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Enviar certificado por email
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
