<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Órdenes</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; font-size: 10px; margin: 0; padding: 0; }
        .header-container { width: 100%; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-img { max-height: 45px; }
        .company-name { font-size: 14px; font-weight: bold; color: #0f172a; margin: 0; }
        .company-info { font-size: 8.5px; color: #64748b; margin-top: 2px; }
        .report-badge { text-align: right; }
        .report-title { font-size: 13px; font-weight: bold; color: #d97706; margin: 0; }
        .report-date { font-size: 8.5px; color: #64748b; margin-top: 2px; }
        
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; margin-bottom: 15px; border-radius: 4px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-item { text-align: center; }
        .summary-label { font-size: 8px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .summary-value { font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 2px; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th { background: #d97706; color: #ffffff; text-align: left; padding: 6px 8px; font-size: 9px; text-transform: uppercase; }
        .data-table td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; color: #1e293b; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td>
                    @if ($company && $company->logo_path)
                        <img src="{{ public_path('storage/' . $company->logo_path) }}" class="logo-img" alt="Logo">
                    @else
                        <h2 style="color: #d97706; margin: 0;">LOGO</h2>
                    @endif
                </td>
                <td>
                    <h1 class="company-name">{{ $company->name ?? 'Mi Empresa' }}</h1>
                    <div class="company-info">
                        @if ($company && $company->tax_id) <strong>RUC:</strong> {{ $company->tax_id }}<br> @endif
                        @if ($company && $company->address) {{ $company->address }} @endif
                    </div>
                </td>
                <td class="report-badge">
                    <div class="report-title">Reporte de Órdenes</div>
                    <div class="report-date">Emitido: {{ date('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="summary-item">
                    <div class="summary-label">Total Órdenes</div>
                    <div class="summary-value">{{ $orders->count() }}</div>
                </td>
                <td class="summary-item">
                    <div class="summary-label">Monto Recaudado</div>
                    <div class="summary-value">{{ $company->currency_code ?? '$' }}{{ number_format($orders->sum('total'), 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>N° Orden</th>
                <th>Fecha / Hora</th>
                <th>Cliente</th>
                <th class="text-center">Estado</th>
                <th>Pago</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $item)
                <tr>
                    <td><strong>{{ $item->order_number }}</strong></td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ trim(($item->client->name ?? '') . ' ' . ($item->client->last_name ?? '')) ?: 'Cliente General' }}</td>
                    <td class="text-center">{{ $item->state->name ?? 'N/D' }}</td>
                    <td>{{ ucfirst($item->payment_gateway ?? 'Efectivo') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ $company->currency_code ?? '$' }}{{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #94a3b8; font-style: italic;">
                        No se encontraron órdenes con los filtros seleccionados.
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