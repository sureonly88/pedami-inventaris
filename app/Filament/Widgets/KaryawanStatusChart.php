<?php

namespace App\Filament\Widgets;

use App\Models\Karyawan;
use Filament\Widgets\ChartWidget;

class KaryawanStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Karyawan';

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $aktif = Karyawan::where('status_karyawan', 'Aktif')->count();
        $pensiun = Karyawan::where('status_karyawan', 'Pensiun')->count();
        $nonaktif = Karyawan::where('status_karyawan', 'Nonaktif')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status Karyawan',
                    'data' => [$aktif, $pensiun, $nonaktif],
                    'backgroundColor' => ['#10b981', '#ef4444', '#94a3b8'],
                ],
            ],
            'labels' => ['Aktif', 'Pensiun', 'Nonaktif'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}