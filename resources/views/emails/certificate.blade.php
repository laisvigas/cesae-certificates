<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>O seu certificado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="x-apple-disable-message-reformatting">

  <style>
    /*  Utilitários seguros para e-mail, não está separado em um arquivo .css por risco de não funcionar */
    .container { width:600px; max-width:100%; }
    .card { border:1px solid #e5e7eb; border-radius:12px; }
    .px-24 { padding-left:24px; padding-right:24px; }
    .py-24 { padding-top:24px; padding-bottom:24px; }
    .muted { color:#6b7280; }
    .btn-td { border-radius:8px; }
    .btn-a  { display:inline-block; padding:12px 20px; font-size:14px; line-height:1;
              color:#ffffff; text-decoration:none; font-weight:600; }
    h1 { margin:0 0 8px 0; font-size:22px; line-height:1.3; color:#111827; }
    p  { margin:0 0 12px 0; font-size:14px; line-height:1.6; color:#374151; }
    .spacer-16 { line-height:16px; height:16px; }
    .spacer-24 { line-height:24px; height:24px; }

    @media (max-width: 600px) {
      .container { width:100% !important; }
      .px-24 { padding-left:16px !important; padding-right:16px !important; }
      .py-24 { padding-top:16px !important; padding-bottom:16px !important; }
      .btn-a  { display:block !important; text-align:center !important; }
    }
  </style>

</head>
<body style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif;">

  <!-- Preheader (pré-visualização nas caixas de entrada) -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; visibility:hidden;">
    O seu certificado já está disponível para consulta e partilha.
  </div>

  <!-- Wrapper -->
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td align="center" style="padding:24px;">
        <table role="presentation" class="container" cellspacing="0" cellpadding="0" border="0">

          <!-- Cabeçalho -->
          <tr>
            <td class="px-24" style="padding-top:8px; padding-bottom:16px;">
              <div style="font-weight:700; font-size:18px; color:#111827;">
                Certificado disponível
              </div>
            </td>
          </tr>

          <!-- Cartão principal -->
          <tr>
            <td class="px-24" style="padding-bottom:24px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" class="card">
                <tr>
                  <td class="px-24 py-24">
                    <h1>Olá, {{ $name }}!</h1>

                    <p>Agradecemos a sua participação. Segue o seu certificado em anexo.</p>
                    <p>Se preferir, pode consultá-lo no navegador e partilhá-lo nas redes sociais.</p>

                    <!-- Botão bulletproof -->
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:16px 0 8px 0;">
                      <tr>
                        <td class="btn-td" align="center" bgcolor="#0f172a">
                          <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml"
                              href="{{ $publicUrl }}"
                              style="height:44px;v-text-anchor:middle;width:240px;"
                              arcsize="12%" stroke="f" fillcolor="#0f172a">
                              <w:anchorlock/>
                              <center>
                          <![endif]-->
                            <a class="btn-a" href="{{ $publicUrl }}">Ver certificado</a>
                          <!--[if mso]>
                              </center>
                            </v:roundrect>
                          <![endif]-->
                        </td>
                      </tr>
                    </table>

                    <!-- Fallback de link -->
                    <p class="muted" style="font-size:12px;">
                      Se o botão não funcionar, copie e cole este link no seu navegador:<br>
                      <a href="{{ $publicUrl }}" style="color:#2563eb; text-decoration:underline; word-break:break-all;">
                        {{ $publicUrl }}
                      </a>
                    </p>

                    <!-- Separador com espaçador (sem margin tricky) -->
                    <div class="spacer-24">&nbsp;</div>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top:1px solid #e5e7eb;">
                      <tr><td class="spacer-16">&nbsp;</td></tr>
                    </table>

                    <p>Caso precise de ajuda, basta entrar em contacto.</p>
                    <p style="margin-top:16px;">
                      Com os nossos melhores cumprimentos,<br>
                      <strong>Equipa Cesae Digital</strong>
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Rodapé -->
          <tr>
            <td class="px-24" style="padding-bottom:24px;">
              <p class="muted" style="font-size:12px;">
                Este é um e-mail transaccional. Se não estava à espera dele, ignore a mensagem.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
