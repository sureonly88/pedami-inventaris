<?php

namespace App\Exports;

use App\Models\RiwayatServisR2r4;
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

class RiwayatServisR2r4Export implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
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
        return RiwayatServisR2r4::with('dataR2r4')
            ->when($this->filters['from'] ?? null, fn ($q, $from) => $q->whereDate('tanggal_servis', '>=', $from))
            ->when($this->filters['until'] ?? null, fn ($q, $until) => $q->whereDate('tanggal_servis', '<=', $until))
            ->when($this->filters['month'] ?? null, fn ($q, $month) => $q->whereMonth('tanggal_servis', $month))
            ->when($this->filters['year'] ?? null, fn ($q, $year) => $q->whereYear('tanggal_servis', $year))
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tgl Servis',
            'Kode',
            'Nopol',
            'Nama Kendaraan',
            'Jenis Servis',
            'Biaya',
            'Bengkel',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tanggal_servis ? date('d/m/Y', strtotime($row->tanggal_servis)) : '',
            $row->dataR2r4?->kode_brg,
            $row->dataR2r4?->plat,
            $row->dataR2r4?->nm_brg,
            $row->jenis_servis,
            $row->biaya,
            $row->bengkel,
            $row->keterangan,
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

                $sheet->insertNewRowBefore(1, 6);

                $sheet->setCellValue('A1', 'LAPORAN RIWAYAT SERVICE KENDARAAN (R2 & R4)');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', 'Periode: ' . $this->period);
                $sheet->setCellValue('A4', 'Dicetak pada: ' . date('d/m/Y H:i:s'));
                $sheet->setCellValue('A5', 'Oleh: ' . auth()->user()->name);

                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');
                $sheet->mergeCells('A4:' . $highestColumn . '4');
                $sheet->mergeCells('A5:' . $highestColumn . '5');

                $styleCenter = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ];
                $sheet->getStyle('A1:' . $highestColumn . '5')->applyFromArray($styleCenter);
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);

                $sheet->getStyle('A7:' . $highestColumn . '7')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE5E7EB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A7:' . $highestColumn . $highestRow + 6)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('G8:G' . ($highestRow + 6))->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0_-');
            },
        ];
    }
}
