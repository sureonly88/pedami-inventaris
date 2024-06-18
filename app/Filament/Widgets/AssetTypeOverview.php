<?php

namespace App\Filament\Widgets;

use App\Models\data_r2r4;
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
            Stat::make('Kendaraan R2 Operasional', data_r2r4::query()->where('jns_brg', 'R2 Operasional')->count()),
            Stat::make('Kendaraan R2 Dinas', data_r2r4::query()->where('jns_brg', 'R2 Dinas')->count()),
            Stat::make('Kendaraan R4 Operasional', data_r2r4::query()->where('jns_brg', 'R4 Operasional')->count()),
            Stat::make('Kendaraan R4 Dinas', data_r2r4::query()->where('jns_brg', 'R4 Dinas')->count()),
        ];
    }
}
