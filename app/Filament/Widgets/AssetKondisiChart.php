<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetKondisiChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kondisi Aset';
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = Asset::select('status_barang', DB::raw('count(*) as total'))
            ->groupBy('status_barang')
            ->pluck('total', 'status_barang')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Aset',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#10b981', // Baik (Green)
                        '#f59e0b', // Rusak Ringan (Amber)
                        '#ef4444', // Disposal/Rusak (Red)
                        '#6366f1', // Fallback
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
