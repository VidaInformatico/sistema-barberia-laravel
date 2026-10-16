<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PaymentsPurchasesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;
    protected $fromDate;
    protected $toDate;

    public function __construct($search = null, $fromDate = null, $toDate = null)
    {
        $this->search = $search;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
{
    $branchId = Auth::user()->branch_id;

    $query = Payment::with(['method', 'paymentable.supplier'])
        ->where('paymentable_type', 'App\Models\Purchase')
        ->whereHasMorph('paymentable', ['App\Models\Purchase'], function ($q) use ($branchId) {

            $q->where('branch_id', $branchId);

            if ($this->search) {
                $search = $this->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('purchase_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn($c) => $c->where('company_name', 'like', "%{$search}%"));
                });
            }
        });

    // Filtro por fechas (se mantiene igual a nivel de la tabla payments)
    if ($this->fromDate && $this->toDate) {
        $query->whereBetween('created_at', [
            $this->fromDate . ' 00:00:00',
            $this->toDate . ' 23:59:59'
        ]);
    }

    return $query->latest()->get();
}

    public function headings(): array
    {
        return [
            'N° Factura',
            'Proveedor',
            'Método de Pago',
            'Fecha y Hora',
            'Monto'
        ];
    }

    public function map($payment): array
    {
        return [
            (string) ($payment->paymentable->purchase_number ?? 'N/A'),
            $payment->paymentable->supplier->company_name ?? 'Proveedor General',
            $payment->method->name ?? 'Efectivo',
            $payment->created_at->format('d/m/Y H:i'),
            (float) $payment->amount,
        ];
    }

    public function columnFormats(): array
    {
        $setting = Company::first();
        $simbol = $setting->currency_code ?? '$';

        return [
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'E' => '"' . $simbol . '"#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $setting = Company::first();
                $companyName = $setting->company_name ?? 'Mi Empresa';
                $taxId = $setting->tax_id ? 'RUC/NIT: ' . $setting->tax_id : '';

                $sheet->insertNewRowBefore(1, 6);

                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', strtoupper($companyName));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1e293b');

                $sheet->mergeCells('A3:E3');
                $sheet->setCellValue('A3', $taxId . ' | Reporte de Pagos / Ingresos');
                $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748b');

                $sheet->mergeCells('A4:E4');
                $sheet->setCellValue('A4', 'Emitido el: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');

                $sheet->getStyle('A7:E7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A7:E' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C8:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E8:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('D' . $totalRow, 'TOTAL GENERAL:');
                $sheet->setCellValue('E' . $totalRow, '=SUM(E8:E' . $highestRow . ')');

                $sheet->getStyle('D' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . $totalRow . ':E' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0F172A']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F1F5F9']
                    ],
                    'borders' => [
                        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => 'CBD5E1']],
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE, 'color' => ['rgb' => '64748b']],
                    ]
                ]);

                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
