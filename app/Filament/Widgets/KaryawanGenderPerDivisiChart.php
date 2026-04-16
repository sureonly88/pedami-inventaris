<?php

namespace App\Filament\Widgets;

use App\Models\Karyawan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KaryawanGenderPerDivisiChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Jenis Kelamin per Divisi';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $excludedDivisions = array_map('strtolower', Karyawan::EXCLUDED_REKAP_ACTIVE_DIVISIONS);
        $activeDivisionFilterSql = "karyawans.status_karyawan = 'Aktif' AND LOWER(COALESCE(divisis.nama_divisi, '')) NOT IN (?, ?, ?, ?)";

        $rows = DB::table('karyawans')
            ->leftJoin('subdivisis', 'karyawans.subdivisi_id', '=', 'subdivisis.id')
            ->leftJoin('divisis', 'subdivisis.divisi_id', '=', 'divisis.id')
            ->selectRaw("COALESCE(divisis.nama_divisi, 'Tanpa Divisi') as divisi")
            ->selectRaw(
                "SUM(CASE WHEN karyawans.jkel = 'Laki-Laki' AND {$activeDivisionFilterSql} THEN 1 ELSE 0 END) as laki_laki",
                $excludedDivisions,
            )
            ->selectRaw(
                "SUM(CASE WHEN karyawans.jkel = 'Perempuan' AND {$activeDivisionFilterSql} THEN 1 ELSE 0 END) as perempuan",
                $excludedDivisions,
            )
            ->selectRaw(
                "SUM(CASE WHEN karyawans.jkel = 'L/P' AND {$activeDivisionFilterSql} THEN 1 ELSE 0 END) as campuran",
                $excludedDivisions,
            )
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