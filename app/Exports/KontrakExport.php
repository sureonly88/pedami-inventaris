<?php

namespace App\Exports;

use App\Models\Kontrak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KontrakExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $filters;
    protected $period;

    public function __construct($filters = [], $period = 'Semua Periode')
    {
        $this->filters = $filters;
        $this->period = $period;
    }

    public function collection()
    {
        return Kontrak::with('kontrakDetails.dataR2r4')
            ->when($this->filters['from'] ?? null, fn ($q, $from) => $q->whereDate('tgl_awal', '>=', $from))
            ->when($this->filters['until'] ?? null, fn ($q, $until) => $q->whereDate('tgl_awal', '<=', $until))
            ->when($this->filters['month'] ?? null, fn ($q, $month) => $q->whereMonth('tgl_awal', $month))
            ->when($this->filters['year'] ?? null, fn ($q, $year) => $q->whereYear('tgl_awal', $year))
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Kontrak',
            'Judul Kontrak',
            'Tgl Awal',
            'Tgl Akhir',
            'Masa Sewa',
            'Unit Kendaraan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        
        $units = $row->kontrakDetails->map(function($detail) {
            return $detail->dataR2r4?->plat;
        })->filter()->implode(', ');

        return [
            $no,
            $row->no_kontrak,
            $row->judul,
            $row->tgl_awal ? date('d/m/Y', strtotime($row->tgl_awal)) : '',
            $row->tgl_akhir ? date('d/m/Y', strtotime($row->tgl_akhir)) : '',
            $row->tgl_awal && $row->tgl_akhir ? (int) \Carbon\Carbon::parse($row->tgl_awal)->diffInMonths(\Carbon\Carbon::parse($row->tgl_akhir)) . ' Bulan' : '-',
            $units,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'LAPORAN DAFTAR KONTRAK');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', 'Periode: ' . $this->period);

                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                $styleCenter = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ];
                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray($styleCenter);
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);

                $sheet->getStyle('A5:' . $highestColumn . '5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE5E7EB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A5:' . $highestColumn . $highestRow + 4)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}
