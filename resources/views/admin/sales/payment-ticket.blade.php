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
            line-height: 1.2;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .logo {
            width: 70px;
            margin-bottom: 5px;
        }

        .brand-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .divider {
            border-top: 0.5pt dashed #000;
            margin: 6px 0;
        }

        .receipt-title {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-top: 5px;
        }

        .info-table td {
            padding: 2px 0;
        }

        .payment-box {
            background: #f9f9f9;
            border: 0.5pt solid #ccc;
            padding: 8px;
            margin: 10px 0;
        }

        .amount-highlight {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 7pt;
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
    <div class="receipt-title">Comprobante de Pago</div>
    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td><strong>RECIBO #:</strong></td>
            <td style="text-align: right;">{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td><strong>FECHA:</strong></td>
            <td style="text-align: right;">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>CLIENTE:</strong></td>
            <td style="text-align: right;">
                {{ $payment->paymentable?->client
                    ? $payment->paymentable->client->name .
                        ($payment->paymentable->client->last_name ? ' ' . $payment->paymentable->client->last_name : '')
                    : 'N/A' }}
            </td>
        </tr>
        <tr>
            <td><strong>MÉTODO PAGO:</strong></td>
            <td style="text-align: right;">{{ $payment->method->name }}</td>
        </tr>
    </table>

    <div class="payment-box">
        <div style="font-size: 8pt; text-align: center;">MONTO RECIBIDO</div>
        <div class="amount-highlight">{{ $company->currency_code }}{{ number_format($payment->amount, 2) }}</div>

        <div class="divider"></div>

        <table style="width: 100%; font-size: 8.5pt;">
            <tr>
                <td>TOTAL VENTA:</td>
                <td style="text-align: right;">{{ $company->currency_code }}
                    {{ number_format($payment->paymentable->total, 2) }}</td>
            </tr>
            @php
                $totalPagado = $payment->paymentable->payments->where('id', '<=', $payment->id)->sum('amount');
                $saldoRestante = $payment->paymentable->total - $totalPagado;
            @endphp
            <tr>
                <td>PAGADO ACUM:</td>
                <td style="text-align: right;">{{ $company->currency_code }} {{ number_format($totalPagado, 2) }}</td>
            </tr>
            <tr style="font-weight: bold; font-size: 9pt;">
                <td>SALDO PENDIENTE:</td>
                <td style="text-align: right;">{{ $company->currency_code }} {{ number_format($saldoRestante, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Gracias por su pago.</p>
        <p>Cualquier reclamo presentar este comprobante.</p>
    </div>

</body>

</html>
