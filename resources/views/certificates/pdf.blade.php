<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Certificado</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 48px; }
    .wrap { border: 2px solid #333; padding: 40px; }
    h1 { text-align: center; margin-bottom: 24px; }
    .name { font-size: 26px; font-weight: bold; text-align: center; margin: 20px 0; }
    .course { font-weight: 600; }
    .footer { margin-top: 40px; display:flex; justify-content: space-between; font-size: 12px; color:#666; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Certificado de Conclusão</h1>
    <p>Este certificado é atribuído a:</p>
    <div class="name">{{ $name ?? 'João Silva' }}</div>
    <p>em reconhecimento da sua participação e aproveitamento no curso
      <span class="course">{{ $course ?? 'Laravel Básico' }}</span>
      na data <strong>{{ $date ?? '04/09/2025' }}</strong>.
    </p>
    <div class="footer">
      <div>Entidade formadora: {{ config('app.name') }}</div>
      <div>Código: {{ $ref ?? '0000000001' }}</div>
    </div>
  </div>
</body>
</html>
