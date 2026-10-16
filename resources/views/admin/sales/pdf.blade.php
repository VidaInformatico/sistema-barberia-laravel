<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Ejecutivo de Ventas</title>
    <style>
        @page {
            margin: 1cm 1cm 1.5cm 1cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .header-container {
            width: 100%;
            border-bottom: 3px solid #d97706;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 25%;
            vertical-align: middle;
        }

        .logo-img {
            max-height: 55px;
            max-width: 150px;
            object-fit: contain;
        }

        .company-cell {
            width: 45%;
            vertical-align: middle;
        }

        .company-name {
            font-size: 18px;
            font-weight: 800;
            color: #d97706;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-info {
            font-size: 9.5px;
            color: #64748b;
            line-height: 1.3;
        }

        .meta-cell {
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }

        .report-badge {
            background-color: #f1f5f9;
            border-left: 4px solid #d97706;
            padding: 8px 12px;
            display: inline-block;
            text-align: left;
            border-radius: 0 6px 6px 0;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .report-date {
            font-size: 9px;
            color: #64748b;
            margin: 0;
        }

        .summary-box {
            width: 100%;
            margin-bottom: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-item {
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background-color: #d97706;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 10px 12px;
            text-align: left;
            letter-spacing: 0.5px;
        }

        .data-table th:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .data-table th:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .data-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-contado {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-credito {
            background-color: #fef3c7;
            color: #92400e;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    @php
        $totalVentas = $sales->sum('total');
        $cantidadVentas = $sales->count();
    @endphp

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if ($company && $company->logo_path)
                        <img src="{{ public_path('storage/' . $company->logo_path) }}" class="logo-img" alt="Logo">
                    @else
                        <h2 style="color: #d97706; margin: 0;">LOGO</h2>
                    @endif
                </td>
                <td class="company-cell">
                    <h1 class="company-name">{{ $company->name ?? 'Mi Empresa' }}</h1>
                    <div class="company-info">
                        @if ($company && $company->tax_id)
                            <strong>RUC / NIT:</strong> {{ $company->tax_id }}<br>
                        @endif
                        @if ($company && $company->address)
                            <strong>Dir:</strong> {{ $company->address }}<br>
                        @endif
                        @if ($company && $company->phone)
                            <strong>Tel:</strong> {{ $company->phone }} |
                        @endif
                        @if ($company && $company->email)
                            {{ $company->email }}
                        @endif
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="report-badge">
                        <div class="report-title">Reporte de Ventas</div>
                        <div class="report-date">Emitido: {{ date('d/m/Y H:i') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="summary-item">
                    <div class="summary-label">Total Registros</div>
                    <div class="summary-value">{{ $cantidadVentas }}</div>
                </td>
                <td class="summary-item">
                    <div class="summary-label">Monto Global Recaudado</div>
                    <div class="summary-value" style="color: #d97706;">
                        {{ $company->currency_code ?? '$' }}{{ number_format($totalVentas, 2) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>N° Factura</th>
                <th>Cliente / Cajero</th>
                <th>Fecha y Hora</th>
                <th>Método de Pago</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $item)
                <tr>
                    <td><strong>{{ $item->sale_number }}</strong></td>
                    <td>
                        @php
                            $clientName = $item->client
                                ? trim($item->client->name . ' ' . $item->client->last_name)
                                : 'Cliente General';
                        @endphp
                        <div style="font-weight: bold; color: #0f172a;">{{ $clientName }}</div>
                        <div style="font-size: 8.5px; color: #64748b; margin-top: 1px;">Cajero:
                            {{ $item->creator->name ?? 'Sistema' }}</div>
                    </td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $item->credit > 0 ? 'badge-credito' : 'badge-contado' }}">
                            {{ $item->credit > 0 ? 'CRÉDITO' : 'CONTADO' }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">
                        {{ $company->currency_code ?? '$' }}{{ number_format($item->total, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">
                        No se encontraron registros de ventas para los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ $company->name ?? 'Sistema' }} — Reporte generado de forma automatizada
    </div>

</body>

</html>
