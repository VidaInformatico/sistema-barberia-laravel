<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Purchase;
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

class CreditPurchasesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
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
        return Purchase::with(['user', 'supplier', 'payments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('credit', '>', 0)
            ->when($this->fromDate, fn($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->whereHas('supplier', function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° Factura',
            'Proveedor',
            'Fecha y Hora',
            'Total',
            'Abonado',
            'Restante'
        ];
    }

    public function map($purchase): array
    {
        $paid = $purchase->payments->sum('amount');
        $remaining = max(0, $purchase->total - $paid);

        return [
            (string) $purchase->purchase_number,
            $purchase->supplier->company_name ?? 'Proveedor General',
            $purchase->created_at->format('d/m/Y F:i'),
            (float) $purchase->total,
            (float) $paid,
            (float) $remaining
        ];
    }

    public function columnFormats(): array
    {
        $setting = Company::first();
        $simbol = $setting->currency_code ?? '$';

        return [
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'D' => '"' . $simbol . '"#,##0.00',
            'E' => '"' . $simbol . '"#,##0.00',
            'F' => '"' . $simbol . '"#,##0.00',
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
                $companyName = $setting->name ?? 'Mi Empresa';
                $taxId = $setting->tax_id ? 'RUC/NIT: ' . $setting->tax_id : '';

                $sheet->insertNewRowBefore(1, 6);

                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', strtoupper($companyName));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1e293b');

                $sheet->mergeCells('A3:F3');
                $sheet->setCellValue('A3', $taxId . ' | Reporte de Créditos y Abonados');
                $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748b');

                $sheet->mergeCells('A4:F4');
                $sheet->setCellValue('A4', 'Emitido el: ' . date('d/m/Y F:i'));
                $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');

                $sheet->getStyle('A7:F7')->applyFromArray([
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

                $sheet->getStyle('A7:F' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C8:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G8:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('D8:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('C' . $totalRow, 'TOTALES:');
                $sheet->setCellValue('D' . $totalRow, '=SUM(D8:D' . $highestRow . ')');
                $sheet->setCellValue('E' . $totalRow, '=SUM(E8:E' . $highestRow . ')');
                $sheet->setCellValue('F' . $totalRow, '=SUM(F8:F' . $highestRow . ')');

                $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . $totalRow . ':F' . $totalRow)->applyFromArray([
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

                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
