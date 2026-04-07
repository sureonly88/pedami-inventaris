<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanTagihanSewaSheetExport implements FromArray, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    public function __construct(
        protected string $sheetTitle,
        protected array $rows,
        protected array $summary,
        protected string $periodLabel,
    ) {
    }

    public function array(): array
    {
        $data = [
            ['DAFTAR SEWA KENDARAAN KOPERASI KONSUMEN PEDAMI DENGAN PT. AIR MINUM BANDARMASIH KOTA BANJARMASIN'],
            [strtoupper('Kendaraan ' . $this->sheetTitle . ' Bulan ' . $this->periodLabel)],
            [],
            ['NO', 'NO. KONTRAK', 'NO. PLAT', 'JENIS/TYPE', 'TAHUN', 'NOMOR MESIN', 'NOMOR RANGKA', 'AWAL', 'AKHIR', 'URAIAN', 'JUMLAH UNIT', 'HARGA KONTRAK', 'TOTAL HARGA KONTRAK', 'PENANGGUNG JAWAB', 'STOP TAGIHAN'],
        ];

        foreach ($this->rows as $row) {
            $data[] = [
                $row['no'],
                $row['no_kontrak'],
                $row['plat'],
                $row['jenis_type'],
                $row['tahun'],
                $row['nomor_mesin'],
                $row['nomor_rangka'],
                $row['awal'] ? date('d/m/Y', strtotime($row['awal'])) : '-',
                $row['akhir'] ? date('d/m/Y', strtotime($row['akhir'])) : '-',
                $row['uraian'],
                $row['jumlah_unit'],
                $row['harga_kontrak'],
                $row['total_harga_kontrak'],
                $row['penanggung_jawab'],
                $row['tgl_stop_tagihan']
                    ? date('d/m/Y', strtotime($row['tgl_stop_tagihan'])) . ' - ' . ($row['alasan_stop_tagihan'] ?: '-')
                    : '-',
            ];
        }

        $data[] = [];
        $data[] = ['TOTAL', '', '', '', '', '', '', '', '', 'Sewa Kendaraan ' . $this->sheetTitle, $this->summary['unit'] ?? 0, 'Unit', $this->summary['nominal'] ?? 0, '', ''];

        return $data;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
            2 => ['font' => ['bold' => true, 'size' => 11]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:O2');

                $sheet->getStyle('A1:O2')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A4:O4')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE5E7EB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('A4:O' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('L5:M' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
            },
        ];
    }
}