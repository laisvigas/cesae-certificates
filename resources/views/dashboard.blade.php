<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Cards container --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                {{-- Card: Certificados emitidos --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                            <use href="#ic-certificate" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900"> {{ $certificatesCount }} </div>
                        <div class="text-sm text-gray-600 truncate">certificados emitidos</div>
                    </div>
                </div>

                {{-- Card: Participantes --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                            <use href="#ic-group" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $allParticipantsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">participantes até hoje</div>
                    </div>
                </div>

                {{-- Card: Eventos realizados --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                        <use href="#ic-event" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $pastEventsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">eventos realizados</div>
                    </div>
                </div>

                {{-- Card: Eventos futuros --}}
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-4 sm:p-5 flex items-center gap-4">
                    <div class="rounded-lg bg-gray-100 p-3">
                        <svg class="w-6 h-6" fill="currentColor" aria-hidden="true">
                        <use href="#ic-event" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $futureEventsCount }}</div>
                        <div class="text-sm text-gray-600 truncate">eventos marcados</div>
                    </div>
                </div>
            </div>
        </div>
</div>

</x-app-layout>
