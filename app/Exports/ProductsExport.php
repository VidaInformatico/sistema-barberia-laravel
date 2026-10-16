<?php

namespace App\Exports;

use App\Models\Product;
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

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $branchId = Auth::user()->branch_id;

        return Product::whereIn('type', ['product', 'combo'])
            ->with('category')
            ->where('branch_id', $branchId)
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nombre / Producto',
            'Categoría',
            'Tipo',
            'Stock',
            'Precio Venta',
            'Costo'
        ];
    }

    public function map($product): array
    {
        return [
            (string) ($product->sku ?? 'S/N'),
            $product->name,
            $product->category->name ?? 'Sin Categoría',
            ucfirst($product->type),
            (int) $product->stock,
            (float) $product->price,
            (float) $product->cost_price,
        ];
    }

    public function columnFormats(): array
    {
        $setting = Company::first();
        $simbol = $setting->currency_code ?? '$';

        return [
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'E' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
            'F' => '"' . $simbol . '"#,##0.00',
            'G' => '"' . $simbol . '"#,##0.00',
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

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', strtoupper($companyName));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1e293b');

                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', $taxId . ' | Inventario de Productos y Combos');
                $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748b');

                $sheet->mergeCells('A4:G4');
                $sheet->setCellValue('A4', 'Emitido el: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');

                $sheet->getStyle('A7:G7')->applyFromArray([
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

                $sheet->getStyle('A7:G' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D8:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F8:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('D' . $totalRow, 'TOTAL ITEMS:');
                $sheet->setCellValue('E' . $totalRow, '=COUNTA(A8:A' . $highestRow . ')');

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

                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}