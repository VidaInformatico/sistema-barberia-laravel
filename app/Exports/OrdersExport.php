<?php

namespace App\Exports;

use App\Models\Order;
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

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
{
    protected $search;
    protected $selectedStatus;
    protected $selectedDate;

    public function __construct($search = null, $selectedStatus = null, $selectedDate = null)
    {
        $this->search = $search;
        $this->selectedStatus = $selectedStatus;
        $this->selectedDate = $selectedDate;
    }

    public function collection()
    {
        $branchId = Auth::user()->branch_id;
        $search = $this->search;
        $selectedStatus = $this->selectedStatus;
        $selectedDate = $this->selectedDate;

        return Order::query()
            ->with(['state', 'client'])
            ->where('branch_id', $branchId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', '%' . $search . '%')
                        ->orWhere('payment_id', 'like', '%' . $search . '%');
                });
            })
            ->when($selectedStatus, fn($q) => $q->where('order_state_id', $selectedStatus))
            ->when($selectedDate, fn($q) => $q->whereDate('created_at', $selectedDate))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° Orden',
            'Fecha y Hora',
            'Cliente',
            'Estado',
            'Método Pago',
            'Total'
        ];
    }

    public function map($order): array
    {
        $clientName = trim(($order->client->name ?? '') . ' ' . ($order->client->last_name ?? '')) ?: 'Cliente General';

        return [
            (string) $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $clientName,
            strtoupper($order->state->name ?? 'N/D'),
            ucfirst($order->payment_gateway ?? 'Efectivo / Manual'),
            (float) $order->total,
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
                $sheet->setCellValue('A3', $taxId . ' | Reporte de Órdenes y Ventas');
                $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('64748b');

                $sheet->mergeCells('A4:F4');
                $sheet->setCellValue('A4', 'Emitido el: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('94a3b8');

                $sheet->getStyle('A7:F7')->applyFromArray([
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

                $sheet->getStyle('A7:F' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A8:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D8:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('E' . $totalRow, 'MONTO TOTAL:');
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