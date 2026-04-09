<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\KaryawanGenderPerDivisiChart;
use App\Filament\Widgets\KaryawanStatsOverview;
use App\Filament\Widgets\KaryawanStatusChart;
use App\Models\Karyawan;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class RekapKaryawan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string $view = 'filament.pages.rekap-karyawan';

    protected static ?string $title = 'Rekap Karyawan';

    protected static ?string $navigationLabel = 'Rekap Karyawan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    public array $rekapPerDivisi = [];

    public function mount(): void
    {
        $this->rekapPerDivisi = $this->getRekapPerDivisi();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KaryawanStatsOverview::class,
            KaryawanGenderPerDivisiChart::class,
            KaryawanStatusChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Karyawan::count();
    }

    protected function getRekapPerDivisi(): array
    {
        return DB::table('karyawans')
            ->leftJoin('subdivisis', 'karyawans.subdivisi_id', '=', 'subdivisis.id')
            ->leftJoin('divisis', 'subdivisis.divisi_id', '=', 'divisis.id')
            ->selectRaw("COALESCE(divisis.nama_divisi, 'Tanpa Divisi') as divisi")
            ->selectRaw("SUM(CASE WHEN karyawans.jkel = 'Laki-Laki' THEN 1 ELSE 0 END) as laki_laki")
            ->selectRaw("SUM(CASE WHEN karyawans.jkel = 'Perempuan' THEN 1 ELSE 0 END) as perempuan")
            ->selectRaw("SUM(CASE WHEN karyawans.jkel = 'L/P' THEN 1 ELSE 0 END) as campuran")
            ->selectRaw("SUM(CASE WHEN karyawans.status_karyawan = 'Aktif' THEN 1 ELSE 0 END) as aktif")
            ->selectRaw("SUM(CASE WHEN karyawans.status_karyawan = 'Pensiun' THEN 1 ELSE 0 END) as pensiun")
            ->selectRaw("SUM(CASE WHEN karyawans.status_karyawan = 'Nonaktif' THEN 1 ELSE 0 END) as nonaktif")
            ->selectRaw('COUNT(karyawans.id) as total')
            ->groupBy('divisi')
            ->orderBy('divisi')
            ->get()
            ->map(fn ($row) => [
                'divisi' => $row->divisi,
                'laki_laki' => (int) $row->laki_laki,
                'perempuan' => (int) $row->perempuan,
                'campuran' => (int) $row->campuran,
                'aktif' => (int) $row->aktif,
                'pensiun' => (int) $row->pensiun,
                'nonaktif' => (int) $row->nonaktif,
                'total' => (int) $row->total,
            ])
            ->all();
    }
}