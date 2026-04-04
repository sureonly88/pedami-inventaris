<?php

namespace App\Exports;

use App\Models\PermohonanDisposal;
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

class PermohonanDisposalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
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
        return PermohonanDisposal::with(['asset', 'dibuatOleh'])
            ->when($this->filters['from'] ?? null, fn ($q, $from) => $q->whereDate('tgl_pengajuan', '>=', $from))
            ->when($this->filters['until'] ?? null, fn ($q, $until) => $q->whereDate('tgl_pengajuan', '<=', $until))
            ->when($this->filters['month'] ?? null, fn ($q, $month) => $q->whereMonth('tgl_pengajuan', $month))
            ->when($this->filters['year'] ?? null, fn ($q, $year) => $q->whereYear('tgl_pengajuan', $year))
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Surat',
            'Tgl Pengajuan',
            'Kode Aset',
            'Nama Aset',
            'Kondisi',
            'Keterangan',
            'Status Manager',
            'Status Ketua',
            'Diajukan Oleh',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nomor,
            $row->tgl_pengajuan ? date('d/m/Y', strtotime($row->tgl_pengajuan)) : '',
            $row->asset?->kode_asset,
            $row->asset?->nama_asset,
            $row->kondisi,
            $row->keterangan,
            $row->verif_manager ? 'Sudah Verifikasi' : 'Belum',
            $row->verif_ketua ? 'Sudah Verifikasi' : 'Belum',
            $row->dibuatOleh?->nama_karyawan,
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

                $sheet->setCellValue('A1', 'LAPORAN DISPOSAL ASET');
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
