<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Purchase;
use App\Models\Setting;
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

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;
    protected $startDate;
    protected $endDate;

    public function __construct($search = null, $startDate = null, $endDate = null)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Purchase::with(['supplier', 'user'])
            ->where('branch_id', Auth::user()->branch_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('purchase_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($subQuery) {
                            $subQuery->where('company_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ]);
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° Factura',
            'Proveedor',
            'Cajero',
            'Fecha y Hora',
            'Método de Pago',
            'Total'
        ];
    }

    public function map($purchase): array
    {
        return [
            (string) $purchase->purchase_number,
            $purchase->supplier->company_name ?? 'Proveedor General',
            $purchase->user->name ?? 'Sistema',
            $purchase->created_at->format('d/m/Y H:i'),
            $purchase->credit > 0 ? 'CRÉDITO' : 'CONTADO',
            (float) $purchase->total,
        ];
    }

    public function columnFormats(): array
    {
        $setting = Company::first();
        $simbol = $setting->currency_code ?? '$';

        return [
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
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
                $sheet->setCellValue('A3', $taxId . ' | Reporte Ejecutivo de Compras');
                $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748b');

                $sheet->mergeCells('A4:F4');
                $sheet->setCellValue('A4', 'Emitido el: ' . date('d/m/Y H:i'));
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
                $sheet->getStyle('D8:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F8:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('E' . $totalRow, 'TOTAL GENERAL:');
                $sheet->setCellValue('F' . $totalRow, '=SUM(F8:F' . $highestRow . ')');

                $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow . ':F' . $totalRow)->applyFromArray([
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
