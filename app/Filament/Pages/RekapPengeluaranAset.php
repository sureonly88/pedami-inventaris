<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class RekapPengeluaranAset extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.pages.rekap-pengeluaran-aset';

    protected static ?string $title = 'Rekap Pengeluaran Biaya Aset';

    protected static ?string $navigationLabel = 'Rekap Pengeluaran Aset';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    public array $rekapPengeluaranAsetPerDivisi = [];

    public function mount(): void
    {
        $this->rekapPengeluaranAsetPerDivisi = $this->getRekapPengeluaranAsetPerDivisi();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Rekap Pengeluaran Biaya Aset')
                ->modalSubmitActionLabel('Unduh Laporan')
                ->form([
                    Grid::make(3)->schema([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                        Select::make('divisi_id')
                            ->label('Divisi')
                            ->options(fn () => DB::table('divisis')->orderBy('nama_divisi')->pluck('nama_divisi', 'id')->all())
                            ->searchable()
                            ->placeholder('Semua Divisi'),
                    ]),
                ])
                ->action(function (array $data) {
                    $divisiId = $data['divisi_id'] ?? null;

                    $summary = $this->getRekapPengeluaranAsetPerDivisi($data['from'] ?? null, $data['until'] ?? null, $divisiId);
                    $details = $this->getRincianPengeluaranAset($data['from'] ?? null, $data['until'] ?? null, $divisiId);

                    $period = 'Semua Tanggal';
                    $divisi = 'Semua Divisi';

                    if (! empty($data['from']) && ! empty($data['until'])) {
                        $period = date('d/m/Y', strtotime($data['from'])) . ' - ' . date('d/m/Y', strtotime($data['until']));
                    } elseif (! empty($data['from'])) {
                        $period = 'Dari ' . date('d/m/Y', strtotime($data['from']));
                    } elseif (! empty($data['until'])) {
                        $period = 'Sampai ' . date('d/m/Y', strtotime($data['until']));
                    }

                    if (! empty($divisiId)) {
                        $divisi = DB::table('divisis')->where('id', $divisiId)->value('nama_divisi') ?? 'Semua Divisi';
                    }

                    $pdf = Pdf::setPaper('a4', 'landscape')->loadHtml(
                        Blade::render('filament.reports.rekap-pengeluaran-aset-report', [
                            'summary' => $summary,
                            'details' => $details,
                            'period' => $period,
                            'selectedDivisi' => $divisi,
                        ])
                    );

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'Rekap_Pengeluaran_Aset_' . str_replace([' ', '/'], ['_', '-'], $period) . '.pdf');
                }),
        ];
    }

    protected function getRekapPengeluaranAsetPerDivisi(?string $from = null, ?string $until = null, ?string $divisiId = null): array
    {
        return DB::table('assets')
            ->leftJoin('karyawans', 'assets.penanggung_jawab_id', '=', 'karyawans.id')
            ->leftJoin('subdivisis', 'karyawans.subdivisi_id', '=', 'subdivisis.id')
            ->leftJoin('divisis', 'subdivisis.divisi_id', '=', 'divisis.id')
            ->when($from, fn ($query) => $query->whereDate('assets.tgl_beli', '>=', $from))
            ->when($until, fn ($query) => $query->whereDate('assets.tgl_beli', '<=', $until))
            ->when($divisiId, fn ($query) => $query->where('divisis.id', $divisiId))
            ->selectRaw("COALESCE(divisis.nama_divisi, 'Tanpa Divisi') as divisi")
            ->selectRaw('COUNT(assets.id) as total_aset')
            ->selectRaw('COALESCE(SUM(assets.hrg_beli), 0) as total_pengeluaran')
            ->groupByRaw("COALESCE(divisis.nama_divisi, 'Tanpa Divisi')")
            ->orderBy('divisi')
            ->get()
            ->map(fn ($row) => [
                'divisi' => $row->divisi,
                'total_aset' => (int) $row->total_aset,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
            ])
            ->all();
    }

    protected function getRincianPengeluaranAset(?string $from = null, ?string $until = null, ?string $divisiId = null)
    {
        return Asset::query()
            ->with(['penanggung_jawab.subdivisi.divisi', 'karyawan'])
            ->when($from, fn ($query) => $query->whereDate('tgl_beli', '>=', $from))
            ->when($until, fn ($query) => $query->whereDate('tgl_beli', '<=', $until))
            ->when($divisiId, function ($query, $divisiId) {
                $query->whereHas('penanggung_jawab.subdivisi.divisi', fn ($divisiQuery) => $divisiQuery->where('divisis.id', $divisiId));
            })
            ->orderBy('tgl_beli')
            ->orderBy('nama_asset')
            ->get();
    }
}