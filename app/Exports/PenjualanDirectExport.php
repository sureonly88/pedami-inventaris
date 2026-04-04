<?php

namespace App\Exports;

use App\Models\PenjualanR2r4;
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

class PenjualanDirectExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $ids;
    protected $filters;
    protected $period;

    public function __construct($ids = null, $filters = [], $period = 'Semua Periode')
    {
        $this->ids = $ids;
        $this->filters = $filters;
        $this->period = $period;
    }

    public function collection()
    {
        return PenjualanR2r4::with('data_r2r4')
            ->when($this->ids, fn ($q) => $q->whereIn('id', $this->ids))
            ->when($this->filters['from'] ?? null, fn ($q, $from) => $q->whereDate('tgl_jual', '>=', $from))
            ->when($this->filters['until'] ?? null, fn ($q, $until) => $q->whereDate('tgl_jual', '<=', $until))
            ->when($this->filters['month'] ?? null, fn ($q, $month) => $q->whereMonth('tgl_jual', $month))
            ->when($this->filters['year'] ?? null, fn ($q, $year) => $q->whereYear('tgl_jual', $year))
            ->get();
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Header Judul
            3 => ['font' => ['bold' => true, 'size' => 12]], // Header Nama Institusi
            5 => ['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']]], // Header Tabel (Black text)
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // 1. Tambahkan 4 Baris di awal untuk Judul
                $sheet->insertNewRowBefore(1, 4);

                // 2. Isi Tulisan Judul
                $sheet->setCellValue('A1', 'LAPORAN PENJUALAN RODA 2 & RODA 4');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', 'Periode: ' . $this->period);

                // 3. Merge Cell Judul
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                // 4. Perataan Tengah Judul
                $styleCenter = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ];
                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray($styleCenter);

                // 5. Styling Header Tabel (sekarang berada di baris 5 karena ada 4 baris judul)
                $sheet->getStyle('A5:' . $highestColumn . '5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE5E7EB'], // Gray 200 (Warna Terang)
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // 6. Terapkan Border ke seluruh tabel (Baris 5 ke bawah)
                $sheet->getStyle('A5:' . $highestColumn . $highestRow + 4)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // 7. Format Mata Uang untuk Kolom Harga (Kolom C)
                $sheet->getStyle('C6:C' . ($highestRow + 4))->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0_-');
            },
        ];
    }

    public function headings(): array
    {
        return [
            'Nopol',
            'Tanggal Penjualan',
            'Harga Penjualan',
            'Nama Pembeli',
            'Nama Barang',
            'Tahun',
            'No Rangka',
            'No Mesin',
            'Warna',
        ];
    }

    public function map($row): array
    {
        return [
            $row->data_r2r4?->plat,
            $row->tgl_jual ? date('d/m/Y', strtotime($row->tgl_jual)) : '',
            $row->hrg_jual,
            $row->nm_pembeli,
            $row->data_r2r4?->nm_brg,
            $row->data_r2r4?->thn,
            $row->data_r2r4?->no_rangka,
            $row->data_r2r4?->no_mesin,
            $row->data_r2r4?->warna,
        ];
    }
}
