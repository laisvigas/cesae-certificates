<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <title>Certificado</title>
  <style>
    /* Página A4 deitada */
    @page {
      size: 297mm 210mm;  
      margin: 0;      
    }

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
      top: 25mm; 
      left: 12mm;
      right: 12mm;
      bottom: 13mm; 
      box-sizing: border-box;
    }

    .vcenter {
      display: table;
      width: 100%;
      height: 100%;
      table-layout: fixed;
    }

    .vcenter-cell {
      display: table-cell;
      vertical-align: middle;
      text-align: center; 
      padding: 0 10mm;
    }

    /* Watermark  */
    .watermark {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      font-size: 72pt;
      color: var(--primary);
      opacity: .06;
      white-space: nowrap;
      pointer-events: none;
      
    }

    /* Cabeçalho / Logo */
    .logo { margin-bottom: 6mm; }
    .logo img { max-height: 14mm; }

    /* Títulos e texto */
    .title {
      font-size: 20pt;
      letter-spacing: .5px;
      margin: 0 0 6mm 0;
      text-transform: uppercase;
      color: #111;
    }

    .subtitle {
      font-size: 11pt;
      color: #666;
      margin: 0 0 6mm 0;
    }

    .intro {
      font-size: 11pt;
      margin: 0 0 4mm 0;
    }

    .name {
      font-size: 28pt;
      font-weight: 700;
      margin: 2mm 0 6mm 0;
      line-height: 1.1;
    }

    .course-line {
      font-size: 12pt;
      margin: 0 0 2mm 0;
    }

    .course { font-weight: 700; }

    .date-line {
      font-size: 11pt;
      color: #444;
      margin-top: 2mm;
    }

    /* Divisor */
    .rule {
      margin: 8mm auto;
      border: 0;
      border-top: .4mm solid var(--primary-light);
      width: 70%;
    }

    /* Rodapé: assinatura + código/entidade */
    .footer-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6mm;
      font-size: 10pt;
      color: #444;
    }
    .footer-table td {
      vertical-align: top;
      padding-top: 6mm;
    }

    .sig-block {
      text-align: center;
      width: 55%;
    }

    .sig-img { max-height: 16mm; display: block; margin: 0 auto 2mm auto; }

    .sig-line {
      border-top: .3mm solid #999;
      margin: 0 auto 2mm auto;
      width: 70%;
      height: 0;
    }

    .info-block {
      text-align: right;
      width: 45%;
      font-size: 9.5pt;
      line-height: 1.4;
    }

    .muted { color: #666; }
    .code  { font-family: "DejaVu Sans Mono", monospace; letter-spacing: .3px; }

    @if(request()->query('preview'))
    /* --- Estilos para o preview (web) --- */
    .frame {
        top: 2vw;
        left: 2vw;
        right: 2vw;
        bottom: 2vw;
        border-width: 0.25vw;
    }
    .content-area {
        top: 2.2vw;
        left: 2.2vw;
        right: 2.2vw;
        bottom: 2.2vw;
    }
    .vcenter-cell {
        padding: 0 4vw;
    }
    .logo img {
        max-height: 5vw;
    }
    .title {
        font-size: 2.2vw;
        margin-bottom: 0.8vw;
    }
    .subtitle {
        font-size: 1vw;
        margin-bottom: 0.8vw;
    }
    .intro {
        font-size: 1vw;
        margin-bottom: 0.6vw;
    }
    .name {
        font-size: 3.2vw;
        margin: 0.3vw 0 1vw 0;
    }
    .course-line {
        font-size: 1.2vw;
    }
    .date-line {
        font-size: 1vw;
        margin-top: 0.4vw;
    }
    .rule {
        margin: 2vw auto;
        border-top-width: 0.08vw;
        width: 70%;
    }
    .footer-table {
        margin-top: 1.5vw;
        font-size: 0.8vw;
    }
    .footer-table td {
        padding-top: 1.5vw;
    }
    .sig-img {
        max-height: 4vw;
        margin: 0 auto 0.5vw auto;
    }
    .sig-line {
        border-top-width: 0.05vw;
        margin-bottom: 0.5vw;
    }
    .info-block {
        font-size: 0.8vw;
    }
@endif
</style>

</head>
  <body>

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
  $dur    = $duration_phrase ?? null;   // ex.: "com uma carga horária total de 12 horas"
  $when   = $date_phrase ?? null;       // ex.: "em 04/09/2025" ou "iniciado em 01/09/2025 até 05/09/2025"

  // monta frase final com pontuação certa
  $parts = array_filter([
    "Concluiu com êxito o {$etype} " . ($event_title ?? $course ?? ''),
    $inst ? "da {$inst}" : null,
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
