<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Seu Certificado</title>
</head>
<body>
    <p>Olá {{ $name }},</p>
    <p>Segue anexo o seu certificado.</p>

    <p>Veja seu certificado no navegador:</p>
    <p><a href="{{ $publicUrl }}">Abrir certificado</a></p>

    <p>Obrigado!</p>
</body>
</html>
