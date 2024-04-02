<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Asset;

class AssetTypeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Peralatan Komputer', Asset::query()->where('kelompok_asset', 'komputer')->count()),
            Stat::make('Perabotan Kantor', Asset::query()->where('kelompok_asset', 'kantor')->count()),
            Stat::make('Kendaraan', Asset::query()->where('kelompok_asset', 'kendaraan')->count()),
        ];
    }
}
