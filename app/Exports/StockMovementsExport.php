<?php

namespace App\Exports;

use App\Models\StockMovement;
use App\Models\Company;
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

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;
    protected $filterType;

    public function __construct($search = null, $filterType = null)
    {
        $this->search = $search;
        $this->filterType = $filterType;
    }

    public function collection()
    {
        $branchId = Auth::user()->branch_id;
        $search = $this->search;
        $filterType = $this->filterType;

        return StockMovement::with(['product', 'user', 'reference'])
            ->where('branch_id', $branchId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhereHas('product', function ($sub) use ($search) {
                            $sub->where('name', 'like', '%' . $search . '%')
                                ->orWhere('sku', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($sub) use ($search) {
                            $sub->where('name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($filterType, function ($query) use ($filterType) {
                $query->where('type', $filterType);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Fecha y Hora',
            'Producto / SKU',
            'Tipo',
            'Cantidad',
            'Stock Ant.',
            'Stock Act.',
            'Motivo',
            'Responsable'
        ];
    }

    public function map($movement): array
    {
        $productName = ($movement->product->sku ? '[' . $movement->product->sku . '] ' : '') . ($movement->product->name ?? 'Producto eliminado');
        $userName = trim(($movement->user->name ?? '') . ' ' . ($movement->user->last_name ?? '')) ?: 'Sistema';

        return [
            $movement->created_at->format('d/m/Y H:i'),
            $productName,
            strtoupper($movement->type),
            (int) $movement->quantity,
            (int) $movement->stock_before,
            (int) $movement->stock_after,
            $movement->reason ?? 'Sin motivo',
            $userName,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'D' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
            'E' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
            'F' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
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

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', strtoupper($companyName));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1e293b');

                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', $taxId . ' | Historial de Movimientos de Stock');
                $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748b');

                $sheet->mergeCells('A4:H4');
                $sheet->setCellValue('A4', 'Emitido el: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');

                $sheet->getStyle('A7:H7')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'd97706']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A7:H' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C8:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('G' . $totalRow, 'TOTAL MOVIMIENTOS:');
                $sheet->setCellValue('H' . $totalRow, '=COUNTA(A8:A' . $highestRow . ')');

                $sheet->getStyle('G' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G' . $totalRow . ':H' . $totalRow)->applyFromArray([
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

                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}