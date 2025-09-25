<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <title>Certificado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @php
    // A barra só deve aparecer quando for preview web E houver public_id (página pública).
    $resolvedPublicId = $public_id ?? request()->route('publicId');
    $showActionBar   = ($preview_mode ?? false) && !empty($resolvedPublicId);
  @endphp

  {{-- Assets e metas só no modo web (não entram no PDF) --}}
  @if($preview_mode ?? false)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta property="og:type" content="article">
    <meta property="og:title" content="Certificado – {{ $name ?? 'Participante' }}">
    <meta property="og:description" content="{{ $event_title ?? 'Evento' }} • Código: {{ $ref ?? '—' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="robots" content="noindex, nofollow">
    {{-- OG image meta properties for social media sharing (prototype) --}}
    <meta property="og:image" content="https://i.postimg.cc/4yQM0wRS/79.png" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="900" />
  @endif

  <style>
    /* ===== Página A4 deitada ===== */
    @page { size: 297mm 210mm; margin: 0; }

    html, body { margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }

    /* ===== Tokens de design ===== */
    :root {
      --primary: {{ $primary_color ?? '#6d28d9' }};
      --accent:  {{ $accent_color ?? '#b79200' }};
      --frame-thick: 1.6mm;    /* espessura da borda externa */
      --frame-radius: 4mm;     /* raio da borda externa */
      --inner-gap: 6mm;        /* recuo entre borda 1 e borda 2 */
      --inner-thick: 0.6mm;    /* espessura da borda interna */
      --title-letterspace: 1.5px;
    }

    /* ===== NOVO: Estilo base do certificado ===== */
    .certificate-container {
    width: 1123px;         /* A4 horizontal */
    aspect-ratio: 297 / 210;
    margin: 0 auto;
    background: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border-radius: .6vw;
    overflow: hidden;
    position: relative;
    }

    /* ===== Moldura dupla ===== */
    .frame {
      position: absolute;  /* Mudei de fixed pra absolute (pq agora está dentro do container) */
      top: 10mm; left: 10mm; right: 10mm; bottom: 10mm;
      border: var(--frame-thick) solid var(--primary);
      border-radius: var(--frame-radius);
      box-sizing: border-box;
      overflow: hidden;
    }
    .frame::after {
      content: "";
      position: absolute;
      top: var(--inner-gap);
      left: var(--inner-gap);
      right: var(--inner-gap);
      bottom: var(--inner-gap);
      border: var(--inner-thick) solid var(--primary);
      border-radius: calc(var(--frame-radius) - 1mm);
      box-sizing: border-box;
    }

    /* ===== Área útil ===== */
    .content-area {
      position: absolute;  /* Mudei de fixed -> absolute (pq agora está dentro do container) */
      top: calc(25mm + 2mm);
      left: 14mm; right: 14mm; bottom: 15mm;
      box-sizing: border-box;
    }

    /* ===== Centralização ===== */
    .vcenter { display: table; width: 100%; height: 100%; table-layout: fixed; }
    .vcenter-cell { display: table-cell; vertical-align: middle; text-align: center; padding: 0 10mm; }

    /* ===== Watermark ===== */
    .watermark {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      font-size: 72pt; color: var(--primary); opacity: .06;
      white-space: nowrap; pointer-events: none;
    }

    /* ====== LOGO ====== */
    .logo {
      margin-bottom: 7mm;
      text-align: center;
    }
    .logo img{
      max-height: 20mm;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    /* ===== Tipografia ===== */
    .title {
      font-family: "DejaVu Serif", Georgia, "Times New Roman", serif;
      font-size: 28pt;
      letter-spacing: var(--title-letterspace);
      margin: 0 0 4mm 0;
      text-transform: uppercase;
      color: #111;
    }
    .subtitle {
      font-size: 10.5pt;
      color: #666;
      margin: 0 0 8mm 0;
      text-transform: uppercase;
      letter-spacing: .8px;
    }
    .intro { font-size: 11pt; margin: 0 0 3mm 0; color: #444; }
    .name {
      font-family: "DejaVu Serif", Georgia, "Times New Roman", serif;
      font-size: 32pt; font-weight: 700;
      margin: 1mm 0 6mm 0; line-height: 1.05; letter-spacing: .2px;
    }

    /* ===== Linhas do texto do curso ===== */
    .course-line { font-size: 12pt; margin: 0 0 2.5mm 0; }
    .main-line  { font-weight: 700; }
    .extra-line { font-size: 11pt; color: #444; margin-top: 1.5mm; }
    .date-line  { font-size: 11pt; color: #444; margin-top: 2mm; }

    /* ===== Divisória ===== */
    .rule {
      margin: 9mm auto;
      border: 0; border-top: .4mm solid var(--primary);
      width: 68%;
    }

    /* ===== Rodapé (assinatura central) ===== */
    .footer-table { width: 100%; border-collapse: collapse; margin-top: 6mm; font-size: 10pt; color: #444; }
    .footer-table td { vertical-align: top; padding-top: 7mm; }

    .sig-block { text-align: center; width: 100%; }
    .sig-img   { max-height: 16mm; display: block; margin: 0 auto 2mm auto; }
    .sig-line  { border-top: .3mm solid #999; margin: 0 auto 2mm auto; width: 70%; height: 0; }

    .muted { color: #666; }
    .code  { font-family: "DejaVu Sans Mono", monospace; letter-spacing: .3px; }

    /* ===== Código de validação fixo no rodapé ===== */
    .validation-code {
      position: fixed;
      left: 14mm; right: 14mm;
      bottom: 12mm;
      text-align: center;
      font-size: 5pt;
      color: #444;
    }

    /* ===== Preview (web) ===== */
    @if(($preview_mode ?? false) || request()->query('preview'))
       .certificate-preview {
    width: 92vw;                /* ocupa quase toda a largura */
    max-width: 1200px;          /* limite de tamanho */
    aspect-ratio: 297 / 210;    /* formato A4 paisagem */
    margin: 2vw auto;
    position: relative;
    background: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border-radius: .6vw;
    overflow: hidden;
  }

  .frame,
  .content-area {
    position: absolute;
    inset: 0;
  }

  .frame {
    border-width: .25vw;
    border-radius: .6vw;
  }
  .frame::after {
    top: 1.2vw; left: 1.2vw; right: 1.2vw; bottom: 1.2vw;
    border-width: .1vw;
    border-radius: .45vw;
  }
      .content-area { top: calc(2.2vw + .8vw); left: 3vw; right: 3vw; bottom: 3vw; }
      .vcenter-cell { padding: 0 4vw; }

      .logo { margin-bottom: 1vw; margin-top: 1vw; text-align:center; } /* Mudei margin-bottom para 1vw e acrescentei margin-top */
      .logo img{
        max-height: 5vw; /* mudei de 6 para 5 */
        display: block;
        margin-left: auto;
        margin-right: auto;
      }

      .watermark { font-size: 10vw; }

      .title { font-size: 2.5vw; margin-bottom: 1vw; } /* mudei de 3 pra 2.5 */
      .subtitle { font-size: 0.8vw; margin-bottom: 1.2vw; letter-spacing: .09vw; } /* mudei de 1 pra 0.8 */
      .intro { font-size: 1.05vw; margin-bottom: .8vw; }
      .name  { font-size: 3vw; margin-bottom: 1.2vw; } /* Mudei de 3,6 para 3 */

      .course-line { font-size: 1.25vw; }
      .main-line   { font-weight: 600; } /* mudei de 700 pra 600 */
      .extra-line  { font-size: 1.05vw; color: #444; }

      .date-line { font-size: 1vw; margin-top: .5vw; }
      .rule      { width: 70%; margin: 2.2vw auto; border-top-width: .08vw; }

      .footer-table   { margin-top: 1.6vw; font-size: .9vw; }
      .footer-table td{ padding-top: 1.6vw; }
      .sig-img        { max-height: 4vw; margin-bottom: .6vw; } /* Mudei de 4.2 para 4 */
      .sig-line       { border-top-width: .06vw; margin-bottom: .6vw; }

      /* Código de validação no preview */
      .validation-code {
        left: 3vw; right: 3vw;
        bottom: 3vw;
        text-align: center;
        font-size: .9vw;
      }

      /* Se existir barra de ações, empurra o certificado para baixo */
      @if($showActionBar)
        .frame       { top: calc(2vw + 72px); }
        .content-area{ top: calc(2.2vw + 72px + .8vw); }
      @endif
    @endif
  </style>
</head>

<body>
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

  {{-- NOVO: Container principal do certificado --}}
  <div class="certificate-container">
  <!-- Moldura -->
  <div class="frame"></div>

  <!-- Área útil -->
  <div class="content-area">
    {{-- Watermark opcional --}}
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
          $etype = $event_type_name ?? 'evento';
          $inst  = $institution_name ?? config('app.name');
          $dur   = $duration_phrase ?? null;
          $when  = $date_phrase ?? null;

          // Checa se o título já contém o tipo para evitar repetição
          if (!empty($event_title) && str_contains(Str::lower($event_title), Str::lower($etype))) {
            $mainPhrase = $event_title;
          } else {
            $mainPhrase = trim(($etype ? "{$etype} " : '') . ($event_title ?? ''));
          }

          // Texto personalizável
          $prefix = $course_line_prefix ?? 'Concluiu com êxito o/a ';

          // Separação em duas linhas
          $mainLine = "{$prefix} {$mainPhrase}";
          $extraParts = array_filter([
            $inst ? "promovido por {$inst}" : null,
            $dur,
            $when ?: null,
          ]);
          $extraLine = implode(', ', $extraParts) . (count($extraParts) ? '.' : '');
        @endphp

        {{-- Linha principal e linhas secundárias --}}
        <p class="course-line main-line">{{ $mainLine }}</p>
        @if($extraLine)
          <p class="course-line extra-line">{{ $extraLine }}</p>
        @endif

        <hr class="rule" />

        {{-- Rodapé: assinatura centralizada --}}
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
          </tr>
        </table>

      </div>
    </div>
  </div>
</div>

  {{-- Código de validação fixo no rodapé (dentro do body) --}}
  @if(!empty($ref))
    <div class="validation-code">
      <span class="muted">Código:</span>
      <span class="code">{{ $ref }}</span>
    </div>
  @endif
</body>
</html>

