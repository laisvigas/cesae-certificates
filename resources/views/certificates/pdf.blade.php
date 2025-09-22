<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <title>Certificado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @php
    // A barra só deve aparecer quando for preview web E houver public_id (página pública).
    $resolvedPublicId = $public_id ?? request()->route('publicId');
    $showActionBar = ($preview_mode ?? false) && !empty($resolvedPublicId);
  @endphp

  {{-- Assets e metas só no modo web (não entram no PDF) --}}
  @if($preview_mode ?? false)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta property="og:type" content="article">
    <meta property="og:title" content="Certificado – {{ $name ?? 'Participante' }}">
    <meta property="og:description" content="{{ $event_title ?? 'Evento' }} • Código: {{ $ref ?? '—' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    {{-- <meta property="og:image" content="{{ asset('img/certificate-og-default.jpg') }}"> --}}
    <meta name="robots" content="noindex, nofollow">
  @endif

  <style>
    /* Página A4 deitada */
    @page { size: 297mm 210mm; margin: 0; }

    html, body { margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }

    :root {
      --primary: {{ $primary_color ?? '#6d28d9' }};
      --primary-light: #000000ff;
    }

    /* Moldura */
    .frame {
      position: fixed;
      top: 10mm; left: 10mm; right: 10mm; bottom: 10mm;
      border: 1.5mm solid var(--primary);
      box-sizing: border-box;
    }

    /* ---- Área útil da moldura ---- */
    .content-area {
      position: fixed;
      top: 25mm; left: 12mm; right: 12mm; bottom: 13mm;
      box-sizing: border-box;
    }

    .vcenter { display: table; width: 100%; height: 100%; table-layout: fixed; }
    .vcenter-cell { display: table-cell; vertical-align: middle; text-align: center; padding: 0 10mm; }

    /* Watermark  */
    .watermark {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      font-size: 72pt; color: var(--primary); opacity: .06;
      white-space: nowrap; pointer-events: none;
    }

    /* Cabeçalho / Logo */
    .logo { margin-bottom: 6mm; }
    .logo img { max-height: 14mm; }

    /* Títulos e texto */
    .title { font-size: 20pt; letter-spacing: .5px; margin: 0 0 6mm 0; text-transform: uppercase; color: #111; }
    .subtitle { font-size: 11pt; color: #666; margin: 0 0 6mm 0; }
    .intro { font-size: 11pt; margin: 0 0 4mm 0; }
    .name { font-size: 28pt; font-weight: 700; margin: 2mm 0 6mm 0; line-height: 1.1; }
    .course-line { font-size: 12pt; margin: 0 0 2mm 0; }
    .course { font-weight: 700; }
    .date-line { font-size: 11pt; color: #444; margin-top: 2mm; }

    /* Divisor */
    .rule { margin: 8mm auto; border: 0; border-top: .4mm solid var(--primary-light); width: 70%; }

    /* Rodapé: assinatura + código/entidade */
    .footer-table { width: 100%; border-collapse: collapse; margin-top: 6mm; font-size: 10pt; color: #444; }
    .footer-table td { vertical-align: top; padding-top: 6mm; }

    .sig-block { text-align: center; width: 55%; }
    .sig-img { max-height: 16mm; display: block; margin: 0 auto 2mm auto; }
    .sig-line { border-top: .3mm solid #999; margin: 0 auto 2mm auto; width: 70%; height: 0; }

    .info-block { text-align: right; width: 45%; font-size: 9.5pt; line-height: 1.4; }
    .muted { color: #666; }
    .code  { font-family: "DejaVu Sans Mono", monospace; letter-spacing: .3px; }

    @if(($preview_mode ?? false) || request()->query('preview'))
    /* --- Estilos para o preview (web) --- */
    .frame {
      top: 2vw; left: 2vw; right: 2vw; bottom: 2vw;
      border-width: 0.25vw;
    }
    .content-area {
      top: 2.2vw; left: 2.2vw; right: 2.2vw; bottom: 2.2vw;
    }
    .vcenter-cell { padding: 0 4vw; }
    .logo img { max-height: 5vw; }
    .title { font-size: 2.2vw; margin-bottom: 0.8vw; }
    .subtitle { font-size: 1vw; margin-bottom: 0.8vw; }
    .intro { font-size: 1vw; margin-bottom: 0.6vw; }
    .name { font-size: 3.2vw; margin: 0.3vw 0 1vw 0; }
    .course-line { font-size: 1.2vw; }
    .date-line { font-size: 1vw; margin-top: 0.4vw; }
    .rule { margin: 2vw auto; border-top-width: 0.08vw; width: 70%; }
    .footer-table { margin-top: 1.5vw; font-size: 0.8vw; }
    .footer-table td { padding-top: 1.5vw; }
    .sig-img { max-height: 4vw; margin: 0 auto 0.5vw auto; }
    .sig-line { border-top-width: 0.05vw; margin-bottom: 0.5vw; }
    .info-block { font-size: 0.8vw; }

    /* Se existir barra de ações, empurra o certificado para baixo */
    @if($showActionBar)
      .frame { top: calc(2vw + 72px); }
      .content-area { top: calc(2.2vw + 72px); }
    @endif
    @endif
  </style>
</head>

<body>

  {{-- Barra de ações (só no modo web e quando houver public_id) --}}
  @if($showActionBar)
    @php
      $shareUrl  = urlencode(url()->current());
      $shareText = urlencode("Meu certificado: " . ($event_title ?? ''));
    @endphp

    <div class="max-w-5xl mx-auto mt-6 mb-4 px-4 font-sans">
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('certificates.public.download', $resolvedPublicId) }}"
           class="inline-flex items-center px-3 py-2 text-sm border rounded-md no-underline hover:bg-gray-50">
          Baixar PDF
        </a>

        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-3 py-2 text-sm border rounded-md no-underline hover:bg-gray-50">
          Compartilhar no LinkedIn
        </a>

        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-3 py-2 text-sm border rounded-md no-underline hover:bg-gray-50">
          Facebook
        </a>

        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-3 py-2 text-sm border rounded-md no-underline hover:bg-gray-50">
          X/Twitter
        </a>

        <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-3 py-2 text-sm border rounded-md no-underline hover:bg-gray-50">
          WhatsApp
        </a>
      </div>

      <hr class="mt-4 border-t border-gray-200">
    </div>
  @endif

  <!-- Moldura -->
  <div class="frame"></div>

  <!-- Área útil -->
  <div class="content-area">

    <!-- Watermark opcional -->
    @if(!empty($watermark))
      <div class="watermark">{{ $watermark }}</div>
    @endif

    <!-- Bloco central (vertical + horizontal center) -->
    <div class="vcenter">
      <div class="vcenter-cell">

        {{-- LOGO opcional --}}
        @if(!empty($logoBase64))
          <div class="logo">
            <img src="{{ $logoBase64 }}" alt="Logo">
          </div>
        @endif

        <h1 class="title">Certificado de Conclusão</h1>

        {{-- Subtítulo com o título do evento --}}
        <p class="subtitle">{{ $event_title ?? $course ?? '—' }}</p>

        <p class="intro">Certificamos que</p>
        <div class="name">{{ $name ?? 'Nome do participante' }}</div>

        @php
          $etype  = $event_type_name ?? 'evento';
          $inst   = $institution_name ?? config('app.name');
          $dur    = $duration_phrase ?? null;
          $when   = $date_phrase ?? null;

          // Checa se o título do evento já contém o tipo de evento para evitar repetição de palavras como Curso de Curso Python
          // Use str_contains to check if the event title includes the event type name

          if (str_contains(Str::lower($event_title), Str::lower($etype))) {
            /*If it contains it, just use the event title*/
            $mainPhrase = $event_title;
            } else {
            /*Otherwise, combine the event type and event title*/
            $mainPhrase = "{$etype} " . $event_title;
            }

          /*Texto que pode ser personalisado pelo usuário*/
          $prefix = $course_line_prefix ?? 'Concluiu com êxito o/a ';

          /*monta frase final com pontuação certa
          -> array_filter(): This function removes all empty or null values from an array.
          -> $parts: temporary array that holds all the individual phrases to combine.
          -> implode() takes all the elements from an array ($parts) and joins them into a single string.
          -> The first argument, ', ', specifies the separator to use between each element.
          -> The final . is added to the end to complete the sentence.
          -> The complete $sentence variable is then passed to the html.*/

          $parts = array_filter([
            "{$prefix} {$mainPhrase}",
            $inst ? "promovido por {$inst}" : null,
            $dur,
            $when ? $when : null,
          ]);
          $sentence = implode(', ', $parts) . '.';
        @endphp

        <p class="course-line">{{ $sentence }}</p>

        <hr class="rule" />

        <table class="footer-table">
          <tr>
            <td class="sig-block">
              @if(!empty($signatureBase64))
                <img class="sig-img" src="{{ $signatureBase64 }}" alt="Assinatura">
              @endif
              <div class="sig-line"></div>
              <div>
                @if(!empty($signer_name)) <strong>{{ $signer_name }}</strong><br>@endif
                @if(!empty($signer_role)) <span class="muted">{{ $signer_role }}</span>@endif
              </div>
            </td>
            <td class="info-block">
              @if(!empty($institution_name))
                <div><span class="muted">Entidade:</span> {{ $institution_name }}</div>
              @endif
              <div><span class="muted">Código de validação:</span> <span class="code">{{ $ref ?? '—' }}</span></div>
            </td>
          </tr>
        </table>

      </div>
    </div>
  </div>

</body>
</html>
