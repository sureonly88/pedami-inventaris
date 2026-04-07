<?php

namespace App\Exports;

use App\Models\Asset;
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

class AssetReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Asset::with(['ruangan', 'penanggung_jawab', 'karyawan'])
            ->when($this->filters['kelompok_asset'] ?? null, fn ($q, $kelompok) => $q->where('kelompok_asset', $kelompok))
            ->when($this->filters['ruangan_id'] ?? null, fn ($q, $ruangan) => $q->where('ruangan_id', $ruangan))
            ->when($this->filters['status_barang'] ?? null, fn ($q, $status) => $q->where('status_barang', $status))
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Aset',
            'Nama Aset',
            'Kelompok',
            'Tgl Beli',
            'Harga Beli',
            'Lokasi/Ruangan',
            'Penanggung Jawab',
            'Pemakai',
            'Kondisi',
            'Deskripsi',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->kode_asset,
            $row->nama_asset,
            $row->kelompok_asset,
            $row->tgl_beli ? date('d/m/Y', strtotime($row->tgl_beli)) : '-',
            $row->hrg_beli,
            $row->ruangan?->ruangan . ' - ' . $row->ruangan?->lokasi,
            $row->penanggung_jawab?->nama_karyawan,
            $row->karyawan?->nama_karyawan,
            $row->status_barang,
            $row->deskripsi,
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

                $sheet->setCellValue('A1', 'LAPORAN INVENTARIS ASET');
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

                $sheet->getStyle('F8:F' . ($highestRow + 6))->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0_-');
            },
        ];
    }
}
