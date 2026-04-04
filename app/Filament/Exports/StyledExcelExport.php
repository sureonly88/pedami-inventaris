<?php

namespace App\Filament\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StyledExcelExport extends ExcelExport
{
    public function registerEvents(): array
    {
        // Ambil event parent (seperti RTL jika dinyalakan)
        $events = parent::registerEvents();

        // Hook ke event AfterSheet untuk mengubah style spreadsheet
        $events[AfterSheet::class] = function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            
            $highestColumn = $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();
            $cellRange = 'A1:' . $highestColumn . $highestRow;

            // 1. Terapkan Border (Garis Grid) ke semua cell yang ada isinya
            $sheet->getStyle($cellRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ]);

            // 2. Styling khusus untuk Header (Baris 1)
            $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'], // Teks Putih
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E293B'], // Slate 800 (Biru Kusam Gelap)
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // 3. Tambahkan tinggi baris Header agar lebih lega
            $sheet->getRowDimension(1)->setRowHeight(25);
            
            // 4. Wrap text di dalam header jika terlalu panjang
            $sheet->getStyle('A1:' . $highestColumn . '1')
                  ->getAlignment()->setWrapText(true);
        };

        return $events;
    }
}
