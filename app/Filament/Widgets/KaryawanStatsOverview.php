<?php

namespace App\Filament\Widgets;

use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KaryawanStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $total = Karyawan::count();
        $aktif = Karyawan::aktifUntukRekap()->count();
        $pensiun = Karyawan::where('status_karyawan', 'Pensiun')->count();
        $lakiLaki = Karyawan::aktifUntukRekap()
            ->where('jkel', 'Laki-Laki')
            ->count();
        $perempuan = Karyawan::aktifUntukRekap()
            ->where('jkel', 'Perempuan')
            ->count();

        return [
            Stat::make('Total Karyawan', $total)
                ->description('Seluruh data karyawan')
                ->color('primary'),
            Stat::make('Karyawan Aktif', $aktif)
                ->description('Status aktif saat ini')
                ->color('success'),
            Stat::make('Karyawan Pensiun', $pensiun)
                ->description('Status pensiun')
                ->color('danger'),
            Stat::make('Laki-Laki', $lakiLaki)
                ->description('Jumlah karyawan laki-laki aktif')
                ->color('info'),
            Stat::make('Perempuan', $perempuan)
                ->description('Jumlah karyawan perempuan aktif')
                ->color('warning'),
        ];
    }
}