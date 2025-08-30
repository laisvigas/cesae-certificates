<x-guest-layout>
    <div class="flex items-center justify-center bg-gray-50 py-20">
        <div class="rounded-xl p-8 text-center">
            <h1 class="text-2xl font-bold mb-4">Certificados CESAE</h1>
            <p class="text-gray-600 mb-6">
                Plataforma interna para emissão e partilha de certificados
            </p>

            @auth
                <a href="{{ route('dashboard') }}" class="inline-block px-4 py-2 rounded-lg bg-gray-900 text-white">
                    Ir para o Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-block px-4 py-2 rounded-lg bg-gray-900 text-white">
                    Fazer Login
                </a>
            @endauth
        </div>
    </div>
</x-guest-layout>
