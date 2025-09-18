<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Certificado – {{ $cert->participant->name ?? 'Participante' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- (Opcional) evitar indexação por buscadores --}}
  <meta name="robots" content="noindex, nofollow">
</head>
{{-- VIEW PARA TESTE --}}
<body style="max-width: 800px; margin: 2rem auto; font-family: system-ui, sans-serif;">
  <h1>Certificado</h1>

  <p><strong>Participante:</strong> {{ $cert->participant->name ?? '—' }}</p>
  <p><strong>Evento:</strong> {{ $cert->event->title ?? '—' }}</p>
  <p><strong>Referência:</strong> {{ $cert->ref }}</p>
  <p><strong>Emitido em:</strong> {{ optional($cert->issued_at)->format('d/m/Y') }}</p>

  {{-- Se você já gera o PDF e armazena num caminho, dá para mostrar um link de download aqui.
       Se ainda não armazena, ignore essa parte por enquanto. --}}
  @if(!empty($cert->pdf_path))
      <p><a href="{{ Storage::url($cert->pdf_path) }}" target="_blank" rel="noopener">Baixar PDF</a></p>
  @endif
</body>
</html>
