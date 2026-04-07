<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanTagihanSewaKendaraanExport implements WithMultipleSheets
{
    public function __construct(
        protected array $roda2Rows,
        protected array $roda4Rows,
        protected array $summary,
        protected string $periodLabel,
    ) {
    }

    public function sheets(): array
    {
        return [
            new LaporanTagihanSewaSheetExport('Roda 2', $this->roda2Rows, $this->summary['roda2'] ?? ['unit' => 0, 'nominal' => 0], $this->periodLabel),
            new LaporanTagihanSewaSheetExport('Roda 4', $this->roda4Rows, $this->summary['roda4'] ?? ['unit' => 0, 'nominal' => 0], $this->periodLabel),
        ];
    }
}