<?php

namespace App\Exports;

use App\Models\MutasiKaryawan;
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

class MutasiKaryawanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
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
        return MutasiKaryawan::with(['karyawan', 'divisiAsal', 'subdivisiAsal', 'divisiTujuan', 'subdivisiTujuan'])
            ->when($this->filters['from'] ?? null, fn ($q, $from) => $q->whereDate('tgl_mutasi', '>=', $from))
            ->when($this->filters['until'] ?? null, fn ($q, $until) => $q->whereDate('tgl_mutasi', '<=', $until))
            ->when($this->filters['month'] ?? null, fn ($q, $month) => $q->whereMonth('tgl_mutasi', $month))
            ->when($this->filters['year'] ?? null, fn ($q, $year) => $q->whereYear('tgl_mutasi', $year))
            ->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Tgl Mutasi', 'No SK', 'NIK', 'Nama Karyawan',
            'Jabatan Asal', 'Divisi Asal', 'Sub Divisi Asal',
            'Jabatan Baru', 'Divisi Baru', 'Sub Divisi Baru', 'Alasan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tgl_mutasi ? date('d/m/Y', strtotime($row->tgl_mutasi)) : '',
            $row->no_sk,
            $row->karyawan?->nik,
            $row->karyawan?->nama_karyawan,
            $row->jabatan_asal,
            $row->divisiAsal?->nama_divisi,
            $row->subdivisiAsal?->nama_sub,
            $row->jabatan_tujuan,
            $row->divisiTujuan?->nama_divisi,
            $row->subdivisiTujuan?->nama_sub,
            $row->alasan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [5 => ['font' => ['bold' => true]]];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->insertNewRowBefore(1, 4);
                $sheet->setCellValue('A1', 'LAPORAN MUTASI KARYAWAN');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', 'Periode: ' . $this->period);

                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);

                $sheet->getStyle('A5:' . $highestColumn . '5')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E7EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A5:' . $highestColumn . $highestRow + 4)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}
