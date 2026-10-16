<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja - {{ $caja->name }}</title>
    <style>
        @page {
            margin: 1.5cm 1.2cm;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Encabezado Ultra Pro */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 11px;
            color: #d97706; /* Amber Primario */
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* Sección de metadatos estructural */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #f8fafc;
            border-radius: 6px;
        }

        .info-table td {
            padding: 12px 16px;
            vertical-align: top;
            width: 50%;
        }

        .label {
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .value {
            font-size: 12px;
            color: #1e293b;
            font-weight: 700;
        }

        .status-badge {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        /* Títulos de Secciones */
        .section-heading {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            border-left: 3px solid #d97706; /* Detalle Amber */
            padding-left: 8px;
        }

        /* Tabla de Resumen por Métodos de Pago */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .summary-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: 700;
            padding: 9px 12px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
            text-transform: uppercase;
        }

        .summary-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
        }

        .summary-table th.total-cell,
        .summary-table td.total-cell {
            background: #fffbeb; /* Fondo sutil Amber */
            color: #b45309; /* Texto Amber Oscuro */
            border-bottom: 2px solid #f59e0b;
        }

        /* Listado de Transacciones */
        .date-group-header {
            background-color: #fafafa;
            color: #475569;
            padding: 6px 10px;
            margin: 20px 0 8px 0;
            font-size: 11px;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-table th {
            background: #1e293b; /* Slate Oscuro Elegante */
            color: #ffffff;
            padding: 9px 12px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.main-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
        }

        /* Utilidades y Estados */
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }

        .ingreso {
            color: #16a34a;
            font-weight: 700;
        }

        .egreso {
            color: #dc2626;
            font-weight: 700;
        }

        .badge {
            font-size: 9px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-gasto {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-venta {
            background-color: #dcfce7;
            color: #166534;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }
    </style>
</head>

<body>

    <!-- Encabezado Estructural Pro -->
    <table class="header-table">
        <tr>
            <td class="text-left">
                <div class="company-name">{{ $company->name ?? 'MI EMPRESA' }}</div>
                <div class="report-title">Arqueo Detallado de Caja</div>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <div style="height: 4px; width: 60px; background-color: #d97706; float: right; border-radius: 2px;"></div>
            </td>
        </tr>
    </table>
    
    <table class="info-table">
        <tr>
            <td>
                <div class="label">Caja Identificador</div>
                <div class="value" style="color: #d97706;">{{ $caja->name }}</div>
                
                <div class="label" style="margin-top: 12px;">Responsable de Turno</div>
                <div class="value">{{ $caja->opener->name }}</div>
            </td>
            <td class="text-right">
                <div class="label">Fecha de Apertura</div>
                <div class="value">{{ $caja->created_at->format('d/m/Y H:i') }}</div>
                
                <div class="label" style="margin-top: 12px;">Estado Actual</div>
                <div class="value status-badge" style="color: {{ $caja->status == 'open' ? '#16a34a' : '#64748b' }}">
                    {{ $caja->status == 'open' ? '● SESIÓN ABIERTA' : '○ SESIÓN CERRADA' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Métodos de Pago -->
    <div class="section-heading">Resumen por Métodos de Pago</div>
    <table class="summary-table">
        <thead>
            <tr>
                @foreach ($pagosPorMetodo as $metodo => $total)
                    <th>{{ $metodo }}</th>
                @endforeach
                <th class="text-right total-cell">SALDO TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($pagosPorMetodo as $metodo => $total)
                    <td>{{ $company->currency_code ?? 'S/' }}{{ number_format($total, 2) }}</td>
                @endforeach
                <td class="text-right total-cell">
                    {{ $company->currency_code ?? 'S/' }}{{ number_format($caja->current_amount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Flujo de Movimientos -->
    <div class="section-heading">Detalle de Movimientos</div>

    @foreach ($movimientos as $fecha => $grupo)
        <div class="date-group-header">
            {{ \Carbon\Carbon::parse($fecha)->format('d \d\e F, Y') }}
        </div>

        <table class="main-table" style="margin-bottom: 15px;">
            <thead>
                <tr>
                    <th class="text-left" width="12%">Tipo</th>
                    <th class="text-left">Detalle / Concepto</th>
                    <th class="text-left" width="20%">Método</th>
                    <th class="text-right" width="18%">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupo as $m)
                    <tr>
                        <td class="text-left">
                            @if ($m['type'] == 'egreso')
                                <span class="badge badge-gasto">Gasto</span>
                            @else
                                <span class="badge badge-venta">Venta</span>
                            @endif
                        </td>
                        <td>{{ $m['concept'] }}</td>
                        <td>{{ $m['methods'] }}</td>
                        <td class="text-right {{ $m['type'] == 'egreso' ? 'egreso' : 'ingreso' }}">
                            {{ $m['type'] == 'egreso' ? '-' : '+' }}{{ $company->currency_code ?? 'S/' }}{{ number_format($m['amount'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <!-- Pie de página estático -->
    <div class="footer">
        Este documento es un comprobante interno de movimientos de tesorería.<br>
        <strong>Generado el:</strong> {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>

</html>
