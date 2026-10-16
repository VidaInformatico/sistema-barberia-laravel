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
        <div class="brand-name">{{ $company->name }}</div>
        <div>{{ $company->address }}</div>
        <div>Email: {{ $company->email }} | TEL: {{ $company->phone }}</div>
    </div>

    <div class="divider"></div>
    <div style="font-size: 7.5pt;">
        <strong>FECHA:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}<br>
        <strong>CLIENTE:</strong> {{ strtoupper($sale->client->name ?? 'Cliente General') }}<br>
        <strong>TEL:</strong> {{ strtoupper($sale->client->phone ?? '-') }}<br>
        <strong>VENDEDOR:</strong> {{ $sale->creator->name ?? 'Sistema' }}<br>
        <strong>Factura N°:</strong> {{ $sale->sale_number ?? '' }}
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>DESC</th>
                <th>CANT</th>
                <th>P.U.</th>
                <th>TOT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr class="product-row">
                    <td colspan="4"><strong>{{ $item->product_name }}</strong></td>
                </tr>

                @if ($item->comboItems && $item->comboItems->count() > 0)
                    @foreach ($item->comboItems as $comboItem)
                        <tr class="details-row">
                            <td colspan="4" style="padding-left: 10px; font-size: 9px; color: #555;">
                                - {{ $comboItem->product_name ?? '' }} ({{ (int) $comboItem->quantity }})
                            </td>
                        </tr>
                    @endforeach
                @endif

                <tr class="details-row">
                    <td></td>
                    <td style="text-align: right;">{{ (int) $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td>SUBTOTAL:</td>
            <td style="text-align: right;">{{ number_format($sale->net_total, 2) }}</td>
        </tr>
        <tr>
            <td>IMPUESTO:</td>
            <td style="text-align: right;">{{ number_format($sale->tax_total, 2) }}</td>
        </tr>
        <tr style="font-size: 10pt; font-weight: bold;">
            <td>TOTAL:</td>
            <td style="text-align: right;">{{ $company->currency_code }}{{ number_format($sale->total, 2) }}</td>
        </tr>
    </table>

    <p>{{ $totalTexto }}</p>

    <div class="payment-info">
        @foreach ($sale->payments as $payment)
            <div style="display: flex; justify-content: space-between;">
                <span>{{ $payment->method->name ?? 'Efectivo' }}:</span>
                <span>{{ $company->currency_code }}{{ number_format($payment->amount, 2) }}</span>
            </div>
        @endforeach
        <div class="divider"></div>
        <div>CAMBIO: {{ $company->currency_code }}{{ number_format($sale->change, 2) }}</div>
    </div>

    <div style="text-align: center; margin-top: 10px; font-size: 6.5pt;">
        ¡GRACIAS POR SU PREFERENCIA!
    </div>

</body>

</html>
