 {{-- Barra de ações (só no modo web e quando houver public_id) --}}
  @if($showActionBar)
    @php
      $shareUrl  = urlencode(url()->current());
      $shareUrl_demo  = 'https://i.postimg.cc/4yQM0wRS/79.png';
      $shareText = "Certificado conquistado!%0AConcluí com sucesso o {$event_title}, promovido pelo "
        . ($institution_name ?? 'Cesae Digital')
        . ".%0AOrgulho de mais uma etapa concluída na minha jornada profissional!";

    @endphp

    <div class="fixed top-6 left-6 bg-white shadow-lg rounded-xl p-3 z-50">
        <div class="flex flex-wrap gap-2">

            {{-- Download PDF --}}
            <a href="{{ route('certificates.public.download', $resolvedPublicId) }}"
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition">
            Baixar PDF
            </a>

            {{-- LinkedIn --}}
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl_demo }}&text={{ $shareText }}"
            target="_blank" rel="noopener"
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-[#0077b5] hover:bg-[#005582] transition">
            LinkedIn
            </a>

            {{-- Facebook --}}
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
            target="_blank" rel="noopener"
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-[#1877f2] hover:bg-[#145dbf] transition">
            Facebook
            </a>

            {{-- Twitter/X --}}
            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}"
            target="_blank" rel="noopener"
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-[#1da1f2] hover:bg-[#0d95e8] transition">
            X/Twitter
            </a>

            {{-- WhatsApp --}}
            <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
            target="_blank" rel="noopener"
            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-[#25d366] hover:bg-[#1da851] transition">
            WhatsApp
            </a>

        </div>
</div>

  @endif
