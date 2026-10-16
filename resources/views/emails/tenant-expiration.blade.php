<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Aviso de Suscripción</title>
</head>

<body
    style="background-color: #0f172a; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px 0; margin: 0; color: #cbd5e1;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 600px; background-color: #1e293b; border-radius: 16px; border: 1px solid #334155; overflow: hidden;">
        <tr>
            <td style="padding: 40px 40px 20px 40px; text-align: center;">
                <h2 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">
                    @if ($daysLeft > 5)
                        Aviso de Renovación
                    @elseif($daysLeft > 0)
                        ¡Atención! Tu plan está por vencer
                    @else
                        Suscripción Vencida
                    @endif
                </h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 40px 30px 40px; text-align: center;">
                <div
                    style="background-color: {{ $daysLeft > 0 ? 'rgba(245, 158, 11, 0.1)' : 'rgba(244, 63, 94, 0.1)' }}; border: 1px solid {{ $daysLeft > 0 ? 'rgba(245, 158, 11, 0.2)' : 'rgba(244, 63, 94, 0.2)' }}; border-radius: 12px; padding: 20px; color: {{ $daysLeft > 0 ? '#fbbf24' : '#fb7185' }}; font-size: 14px; font-weight: 600;">
                    @if ($daysLeft > 0)
                        Te quedan <span style="font-size: 18px; font-weight: 900;">{{ $daysLeft }} días</span> para
                        renovar tu membresía y evitar interrupciones en el sistema.
                    @else
                        El periodo de acceso de tu establecimiento ha concluido por falta de renovación.
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 40px 30px 40px; color: #94a3b8; font-size: 14px; line-height: 1.6; text-align: left;">
                <p style="margin: 0 0 15px 0;">Hola, <strong>{{ $company?->name ?? $tenant->id }}</strong>:</p>
                @if ($daysLeft > 0)
                    <p style="margin: 0 0 15px 0;">Queremos recordarte que tu suscripción actual está próxima a
                        finalizar. Para seguir disfrutando de todas las herramientas de gestión sin contratiempos, por
                        favor realiza tu pago a tiempo.</p>
                @else
                    <p style="margin: 0 0 15px 0;">Tu cuenta se encuentra temporalmente suspendida. Para restablecer el
                        acceso inmediato a tu panel de control y bases de datos, ponte en contacto con nuestro soporte
                        técnico.</p>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 0 40px 40px 40px; text-align: center;">
                <a href="mailto:{{ config('mail.support_address') }}"
                    style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: inline-block; box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.3);">
                    Contactar con Soporte / Renovar
                </a>
            </td>
        </tr>
        <tr>
            <td
                style="background-color: #0f172a; padding: 20px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #334155;">
                &copy; {{ date('Y') }} {{ $company?->name ?? env('APP_NAME') }}. Todos los derechos reservados.
            </td>
        </tr>
    </table>
</body>

</html>