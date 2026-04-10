<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    public function collection()
    {
        return Karyawan::with(['subdivisi.divisi'])
            ->orderBy('nama_karyawan')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Karyawan',
            'No KTP',
            'No HP',
            'No Rekening',
            'No BPJS Ketenagakerjaan',
            'No BPJS Kesehatan',
            'Pendidikan Terakhir',
            'Alamat',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Tanggal Masuk Kerja',
            'Nama Bank',
            'Kontak Darurat',
            'Status Karyawan',
            'Jabatan',
            'Subdivisi ID',
            'Subdivisi',
            'Divisi',
            'Jenis Kelamin',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nik,
            $row->nama_karyawan,
            $row->no_ktp,
            $row->no_hp,
            $row->no_rekening,
            $row->no_bpjs_ketenagakerjaan,
            $row->no_bpjs_kesehatan,
            $row->pendidikan_terakhir,
            $row->alamat,
            $row->tempat_lahir,
            optional($row->tanggal_lahir)->format('Y-m-d'),
            optional($row->tanggal_masuk_kerja)->format('Y-m-d'),
            $row->nama_bank,
            $row->kontak_darurat,
            $row->status_karyawan,
            $row->jabatan,
            $row->subdivisi_id,
            $row->subdivisi?->nama_sub,
            $row->subdivisi?->divisi?->nama_divisi,
            $row->jkel,
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
                $sheet->setCellValue('A1', 'DATA KARYAWAN');
                $sheet->setCellValue('A2', 'KOPERASI KONSUMEN PEDAMI');
                $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d/m/Y H:i'));

                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

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

                $sheet->getStyle('A5:' . $highestColumn . ($highestRow + 4))->applyFromArray([
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