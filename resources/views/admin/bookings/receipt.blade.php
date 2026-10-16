<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 3mm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8pt;
            color: #000;
            line-height: 1.1;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .logo {
            width: 80px;
            margin-bottom: 5px;
        }

        .brand-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .divider {
            border-top: 0.5pt dashed #000;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        th {
            font-size: 7.5pt;
            border-bottom: 0.5pt solid #000;
            padding: 2px 0;
            text-align: right;
        }

        th:first-child {
            text-align: left;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .product-row {
            font-weight: bold;
        }

        .details-row {
            font-size: 7.5pt;
            color: #333;
        }

        .totals-table {
            margin-top: 5px;
            font-size: 8.5pt;
        }

        .payment-info {
            margin-top: 5px;
            font-size: 7.5pt;
            border: 0.5pt solid #ccc;
            padding: 3px;
        }

        .qr-section {
            text-align: center;
            margin-top: 8px;
        }

        .qr-section img {
            width: 90px;
        }
    </style>
</head>

<body>

    <div class="header">
        @if (isset($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
        @endif
        <div class="brand-name">{{ $company->name ?? 'Barbería' }}</div>
        <div>{{ $company->address ?? '' }}</div>
        @if (isset($booking->branch))
            <div><strong>Sucursal:</strong> {{ $booking->branch->name }}</div>
        @endif
        <div>Email: {{ $company->email ?? '' }} | TEL: {{ $company->phone ?? '' }}</div>
    </div>

    <div class="divider"></div>
    <div style="font-size: 7.5pt;">
        <strong>EMISIÓN:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>FECHA CITA:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
        ({{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }})<br>
        <strong>CLIENTE:</strong> {{ strtoupper($booking->client->name ?? 'Cliente General') }}<br>
        <strong>TEL CLIENTE:</strong> {{ strtoupper($booking->client->phone ?? '-') }}<br>
        <strong>BARBERO:</strong> {{ $booking->barber->user->name ?? 'Por asignar' }}<br>
        <strong>N° RESERVA:</strong> {{ $booking->booking_number ?? '' }}
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>SERVICIO / DESC</th>
                <th>TIEMPO</th>
                <th>P.U.</th>
                <th>TOT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booking->services as $item)
                <tr class="product-row">
                    <td colspan="4"><strong>{{ $item->service_name ?? ($item->service->name ?? 'Servicio') }}</strong>
                    </td>
                </tr>

                <tr class="details-row">
                    <td></td>
                    <td style="text-align: right;">{{ $item->duration_minutes }} min</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr style="font-size: 10pt; font-weight: bold;">
            <td>TOTAL A PAGAR:</td>
            <td style="text-align: right;">
                {{ $company->currency_code ?? 'S/' }}{{ number_format($booking->total_price, 2) }}</td>
        </tr>
    </table>

    @if (isset($totalTexto))
        <p style="font-size: 7.5pt; text-transform: uppercase;"><strong>Son:</strong> {{ $totalTexto }}</p>
    @endif

    @if ($booking->notes)
        <div style="margin-top: 5px; font-size: 7.5pt;">
            <strong>Notas:</strong> {{ $booking->notes }}
        </div>
    @endif

    <div style="text-align: center; margin-top: 10px; font-size: 6.5pt;">
        ¡TE ESPERAMOS! LLEGA 5 MINUTOS ANTES.
    </div>

</body>

</html>
