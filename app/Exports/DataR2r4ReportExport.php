<?php

namespace App\Exports;

use App\Models\data_r2r4;
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

class DataR2r4ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return data_r2r4::query()
            ->when($this->filters['jns_brg'] ?? null, fn ($q, $jenis) => $q->where('jns_brg', $jenis))
            ->when($this->filters['stat'] ?? null, fn ($q, $status) => $q->where('stat', $status))
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Jenis Barang',
            'Plat/Nopol',
            'Nama Barang',
            'Tahun',
            'No Rangka',
            'No Mesin',
            'Pajak',
            'STNK',
            'Pemegang',
            'Departemen',
            'Status',
            'Harga Sewa',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->kode_brg,
            $row->jns_brg,
            $row->plat,
            $row->nm_brg,
            $row->thn,
            $row->no_rangka,
            $row->no_mesin,
            $row->pajak ? date('d/m/Y', strtotime($row->pajak)) : '-',
            $row->stnk ? date('d/m/Y', strtotime($row->stnk)) : '-',
            $row->pemegang,
            $row->departemen,
            $row->stat,
            $row->hrg_sewa,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => ['font' => ['bold' => true]],
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

                $sheet->setCellValue('A1', 'LAPORAN PENDATAAN KENDARAAN (R2 & R4)');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', 'Dicetak pada: ' . date('d/m/Y H:i:s'));
                $sheet->setCellValue('A4', 'Oleh: ' . auth()->user()->name);

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

                $sheet->getStyle('N8:N' . ($highestRow + 6))->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0_-');
            },
        ];
    }
}
