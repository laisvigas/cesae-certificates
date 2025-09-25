<x-app-layout>
    @vite(['resources/js/certificates.js'])

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buscar Certificado por Código
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="py-1 w-full sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 min-h-[80vh]">

                {{-- Barra de busca --}}
                <div class="flex flex-col sm:flex-row gap-2 mb-6 justify-center">
                    <input
                        type="text"
                        id="certificateCode"
                        placeholder="Digite o código do certificado"
                        class="w-full sm:w-80 rounded border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900"
                    >
                    <button
                        id="btnSearchCertificate"
                        class="rounded border px-3 py-2 text-sm bg-gray-900 text-white hover:bg-gray-800"
                    >
                        Buscar
                    </button>
                </div>

                {{-- Mensagem de sucesso --}}
                <div id="successMessage" class="text-center text-green-600 font-bold mb-4 hidden">
                </div>

                {{-- Mensagem de erro --}}
                <div id="searchError" class=" text-center text-red-600 text-sm mb-4 hidden"></div>

                {{-- Preview do certificado --}}
                <div id="certificatePreviewContainer" class="rounded-lg border bg-white" style="display:none;">
                    <div class="px-3 py-2 border-b bg-gray-50 text-xs text-gray-600">
                        Pré-visualização do certificado
                    </div>
                    <div class="p-3 relative" style="min-height: 200px;">
                        <iframe
                            id="certificatePreviewFrame"
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
