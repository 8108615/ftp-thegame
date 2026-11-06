<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acceso habilitado - Sistema FTP - THE GAME</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Contenedor principal con esquinas redondeadas o bordes limpios -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    <!-- FRANJA AZUL SUPERIOR -->
                    <tr>
                        <td style="background-color: #0066cc; padding: 20px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="width: 70px; vertical-align: middle; text-align: center; padding-right: 15px;">
                                        <!-- Logo superior (Asegúrate de que la ruta sea correcta o usa Base64 si prefieres máxima compatibilidad) -->
                                        <img src="{{ $message->embed(public_path('assets/images/logoTG.png')) }}" alt="THE GAME" style="width: 60px; height: auto; display: block; background: #ffffff; border-radius: 6px; padding: 4px;">
                                    </td>
                                    <td style="vertical-align: middle; border-left: 2px solid rgba(255,255,255,0.4); padding-left: 15px; color: #ffffff;">
                                        <div style="font-size: 20px; font-weight: bold; letter-spacing: 0.5px;">THE GAME</div>
                                        <div style="font-size: 13px; letter-spacing: 1px; opacity: 0.9;">SISTEMA FTP</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CONTENIDO CENTRAL -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #111827; margin-top: 0; font-size: 20px;">ACCESO AL FTP - THE GAME</h2>

                            <p style="color: #1f2937; font-size: 14px; line-height: 1.5;">Hola, <strong>{{ $user->name }}</strong>:</p>

                            <p style="color: #1f2937; font-size: 14px; line-height: 1.5;">Bienvenido al <strong>Sistema FTP – THE GAME</strong>. Se ha habilitado su cuenta de acceso al sistema. A continuación, encontrará sus credenciales:</p>

                            <!-- Panel de credenciales -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0f7ff; border: 1px solid #bde0fe; border-radius: 6px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 15px; font-size: 14px; color: #1f2937;">
                                        <div style="margin-bottom: 8px;">
                                            <span style="font-size: 14px;">👤</span> <strong>Usuario / Correo:</strong><br>
                                            <span style="color: #0066cc; font-size: 15px; font-weight: bold; display: inline-block; margin-top: 2px;">{{ $user->email }}</span>
                                        </div>
                                        <div style="margin-top: 12px;">
                                            <span style="font-size: 14px;">🔒</span> <strong>Contraseña:</strong><br>
                                            <span style="font-family: monospace; font-size: 15px; background: #ffffff; border: 1px solid #d1d5db; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; color: #111827;">{{ $password }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botón de acceso -->
                            <div style="text-align: center; margin: 25px 0;">
                                <a href="http://200.87.199.10/ftp-thegame/public/login" target="_blank" style="background-color: #0066cc; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 14px;">↗ ACCEDER AL SISTEMA</a>
                            </div>

                            <p style="font-size: 12px; color: #4b5563; line-height: 1.5;">
                                <strong>Por motivos de seguridad, recomendamos no compartir sus credenciales de acceso.</strong> Su cuenta permite mantener una sola sesión activa, por lo que no podrá acceder al sistema desde dos dispositivos al mismo tiempo mientras la sesión anterior permanezca abierta.
                            </p>

                            <p style="font-size: 12px; color: #4b5563; line-height: 1.5;">
                                Si tiene algún inconveniente para acceder al sistema, no dude en comunicarse con nosotros.
                            </p>

                            <p style="color: #1f2937; font-size: 14px; margin-top: 20px;">Saludos cordiales,</p>

                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                            <!-- Firma corporativa -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="width: 130px; vertical-align: middle; padding-right: 15px;">
                                        <img src="{{ $message->embed(public_path('assets/images/logoTG.png')) }}" alt="THE GAME" style="max-width: 120px; height: auto; display: block;">
                                    </td>
                                    <td style="vertical-align: middle; border-left: 2px solid #0066cc; padding-left: 15px; line-height: 1.5; font-size: 12px; color: #111827;">
                                        <strong>ING. DANIEL URÍA VILLAMOR</strong><br>
                                        <strong>THE GAME S.R.L.</strong><br>
                                        <a href="mailto:duria@thegamesrl.com" style="color: #0066cc; text-decoration: none;">duria@thegamesrl.com</a><br>
                                        Tel.: +(591) 70663668 &nbsp;+(591) 69201601
                                    </td>
                                </tr>
                            </table>

                            <!-- Franja negra inferior de dirección -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 25px;">
                                <tr>
                                    <td align="center" style="background-color: #000000; color: #ffffff; padding: 10px; font-size: 11px; font-weight: bold; border-radius: 4px; letter-spacing: 0.5px;">
                                        4to Anillo Doble Vía La Guardia B/ Jenecherú C/ Eucalipto № 60 &nbsp;&nbsp;|&nbsp;&nbsp; Tlf: +59133556565
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                <!-- Pie de página externo institucional -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="margin-top: 15px;">
                    <tr>
                        <td align="center" style="font-size: 11px; color: #6b7280; line-height: 1.4;">
                            Este correo fue generado automáticamente por el Sistema FTP – THE GAME.<br>
                            Si usted no reconoce esta solicitud de acceso, comuníquese con el administrador del sistema.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
