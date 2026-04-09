<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KaryawanGenderPerDivisiChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Jenis Kelamin per Divisi';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $rows = DB::table('karyawans')
            ->leftJoin('subdivisis', 'karyawans.subdivisi_id', '=', 'subdivisis.id')
            ->leftJoin('divisis', 'subdivisis.divisi_id', '=', 'divisis.id')
            ->selectRaw("COALESCE(divisis.nama_divisi, 'Tanpa Divisi') as divisi")
            ->selectRaw("SUM(CASE WHEN karyawans.jkel = 'Laki-Laki' THEN 1 ELSE 0 END) as laki_laki")
            ->selectRaw("SUM(CASE WHEN karyawans.jkel = 'Perempuan' THEN 1 ELSE 0 END) as perempuan")
            ->selectRaw("SUM(CASE WHEN karyawans.jkel = 'L/P' THEN 1 ELSE 0 END) as campuran")
            ->groupBy('divisi')
            ->orderBy('divisi')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Laki-Laki',
                    'data' => $rows->pluck('laki_laki')->map(fn ($value) => (int) $value)->all(),
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Perempuan',
                    'data' => $rows->pluck('perempuan')->map(fn ($value) => (int) $value)->all(),
                    'backgroundColor' => '#ec4899',
                ],
                [
                    'label' => 'L/P',
                    'data' => $rows->pluck('campuran')->map(fn ($value) => (int) $value)->all(),
                    'backgroundColor' => '#8b5cf6',
                ],
            ],
            'labels' => $rows->pluck('divisi')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}