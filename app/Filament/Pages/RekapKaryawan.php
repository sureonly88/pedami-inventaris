<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\KaryawanGenderPerDivisiChart;
use App\Filament\Widgets\KaryawanStatsOverview;
use App\Filament\Widgets\KaryawanStatusChart;
use App\Models\Karyawan;
use Carbon\Carbon;
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

    public array $karyawanMendekatiPensiun = [];

    public function mount(): void
    {
        $this->rekapPerDivisi = $this->getRekapPerDivisi();
        $this->karyawanMendekatiPensiun = $this->getKaryawanMendekatiPensiun();
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

    protected function getRekapPerDivisi(): array
    {
        $excludedDivisions = array_map('strtolower', Karyawan::EXCLUDED_REKAP_ACTIVE_DIVISIONS);
        $activeMaleFilterSql = "karyawans.jkel = 'Laki-Laki' AND karyawans.status_karyawan = 'Aktif'";
        $activeDivisionFilterSql = "karyawans.status_karyawan = 'Aktif' AND LOWER(COALESCE(divisis.nama_divisi, '')) NOT IN (?, ?, ?, ?)";

        return DB::table('karyawans')
            ->leftJoin('subdivisis', 'karyawans.subdivisi_id', '=', 'subdivisis.id')
            ->leftJoin('divisis', 'subdivisis.divisi_id', '=', 'divisis.id')
            ->selectRaw("COALESCE(divisis.nama_divisi, 'Tanpa Divisi') as divisi")
            ->selectRaw("SUM(CASE WHEN {$activeMaleFilterSql} THEN 1 ELSE 0 END) as laki_laki")
            ->selectRaw(
                "SUM(CASE WHEN karyawans.jkel = 'Perempuan' AND {$activeDivisionFilterSql} THEN 1 ELSE 0 END) as perempuan",
                $excludedDivisions,
            )
            ->selectRaw(
                "SUM(CASE WHEN karyawans.jkel = 'L/P' AND {$activeDivisionFilterSql} THEN 1 ELSE 0 END) as campuran",
                $excludedDivisions,
            )
            ->selectRaw(
                "SUM(CASE WHEN {$activeDivisionFilterSql} THEN 1 ELSE 0 END) as aktif",
                $excludedDivisions,
            )
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

    protected function getKaryawanMendekatiPensiun(): array
    {
        $today = now()->timezone(config('app.timezone'))->startOfDay();
        $warningUntil = $today->copy()->addYear();

        return Karyawan::query()
            ->with('subdivisi.divisi')
            ->whereIn('status_karyawan', ['Aktif', 'Pengurus'])
            ->whereNotNull('tanggal_lahir')
            ->get()
            ->map(function (Karyawan $karyawan) use ($today) {
                $tanggalPensiun = Carbon::parse($karyawan->tanggal_lahir)
                    ->timezone(config('app.timezone'))
                    ->addYears(56)
                    ->startOfDay();
                $sisaWaktu = $today->diff($tanggalPensiun);
                $sisaBulan = ($sisaWaktu->y * 12) + $sisaWaktu->m;

                return [
                    'nik' => $karyawan->nik ?? '-',
                    'nama_karyawan' => $karyawan->nama_karyawan ?? '-',
                    'jabatan' => $karyawan->jabatan ?? '-',
                    'divisi' => $karyawan->subdivisi?->divisi?->nama_divisi ?? 'Tanpa Divisi',
                    'umur' => $karyawan->umur ?? '-',
                    'tanggal_pensiun' => $tanggalPensiun->locale('id')->translatedFormat('d F Y'),
                    'sisa_hari' => $today->diffInDays($tanggalPensiun, false),
                    'sisa_waktu' => sprintf('%d bulan %d hari', $sisaBulan, $sisaWaktu->d),
                    'tanggal_pensiun_sort' => $tanggalPensiun->timestamp,
                ];
            })
            ->filter(fn (array $row) => $row['sisa_hari'] >= 0 && $row['sisa_hari'] <= $today->diffInDays($warningUntil))
            ->sortBy('tanggal_pensiun_sort')
            ->values()
            ->map(function (array $row) {
                unset($row['tanggal_pensiun_sort']);

                return $row;
            })
            ->all();
    }
}