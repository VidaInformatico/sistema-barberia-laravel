<?php

namespace App\Exports;

use App\Models\Booking;
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

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents
{
    protected $selectedBarber;
    protected $selectedDate;
    protected $selectedStatus;
    protected $search;

    public function __construct($selectedBarber = null, $selectedDate = null, $selectedStatus = null, $search = null)
    {
        $this->selectedBarber = $selectedBarber;
        $this->selectedDate = $selectedDate;
        $this->selectedStatus = $selectedStatus;
        $this->search = $search;
    }

    public function collection()
    {
        $branchId = Auth::user()->branch_id;

        return Booking::query()
            ->with(['client', 'barber.user', 'services.service'])
            ->where('branch_id', $branchId)
            ->when($this->selectedBarber, fn($q) => $q->forBarber($this->selectedBarber))
            ->when($this->selectedDate, fn($q) => $q->forDate($this->selectedDate))
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('booking_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('client', fn($c) => $c->where('phone', 'like', "%{$search}%"));
                });
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° Reserva',
            'Cliente',
            'Teléfono',
            'Barbero',
            'Fecha y Hora',
            'Estado'
        ];
    }

    public function map($booking): array
    {
        $clientName = $booking->client
            ? trim($booking->client->name . ' ' . ($booking->client->last_name ?? ''))
            : 'Sin nombre';

        $clientPhone = $booking->client->phone ?? 'Sin teléfono';
        $barberName = $booking->barber->user->name ?? 'No asignado';
        $dateTime = \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') . ' ' . $booking->start_time;

        return [
            (string) $booking->booking_number,
            $clientName,
            (string) $clientPhone,
            $barberName,
            $dateTime,
            strtoupper($booking->status),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
            'C' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
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
                $sheet->setCellValue('A3', $taxId . ' | Reporte Ejecutivo de Reservas');
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

                $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C8:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $totalRow = $highestRow + 1;
                $sheet->setCellValue('E' . $totalRow, 'TOTAL RESERVAS:');
                $sheet->setCellValue('F' . $totalRow, '=COUNTA(A8:A' . $highestRow . ')');

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
