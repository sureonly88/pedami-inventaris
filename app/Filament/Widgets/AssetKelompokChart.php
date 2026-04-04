<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetKelompokChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Berdasarkan Kelompok';
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Grouping by kelompok_asset handling possible nulls
        $data = Asset::select('kelompok_asset', DB::raw('count(*) as total'))
            ->whereNotNull('kelompok_asset')
            ->groupBy('kelompok_asset')
            ->pluck('total', 'kelompok_asset')->toArray();

        // Standardize labels to uppercase first letter
        $labels = array_map('ucfirst', array_keys($data));

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Aset',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#8b5cf6', // violet
                        '#ec4899', // pink
                        '#14b8a6', // teal
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
