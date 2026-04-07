<?php

namespace App\Filament\Pages;

use App\Exports\LaporanTagihanSewaKendaraanExport;
use App\Models\data_r2r4;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class LaporanTagihanSewaKendaraan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $title = 'Laporan Tagihan Sewa Kendaraan';

    protected static ?string $navigationLabel = 'Tagihan Sewa Kendaraan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.laporan-tagihan-sewa-kendaraan';

    public ?array $data = [];

    public array $roda2Rows = [];

    public array $roda4Rows = [];

    public array $summary = [
        'roda2' => ['unit' => 0, 'nominal' => 0],
        'roda4' => ['unit' => 0, 'nominal' => 0],
    ];

    public string $periodLabel = '';

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->format('m'),
            'year' => now()->format('Y'),
        ]);

        $this->loadReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ])
                        ->required(),
                    Select::make('year')
                        ->label('Tahun')
                        ->options($this->getYearOptions())
                        ->required(),
                ]),
            ])
            ->statePath('data');
    }

    public function loadReport(): void
    {
        $state = $this->form->getState();
        $report = $this->buildReportData($state['month'], $state['year']);

        $this->roda2Rows = $report['roda2'];
        $this->roda4Rows = $report['roda4'];
        $this->summary = $report['summary'];
        $this->periodLabel = $report['periodLabel'];
    }

    public function exportExcel()
    {
        $state = $this->form->getState();
        $report = $this->buildReportData($state['month'], $state['year']);

        if (empty($report['roda2']) && empty($report['roda4'])) {
            Notification::make()
                ->warning()
                ->title('Data tidak ditemukan')
                ->body('Tidak ada data tagihan sewa kendaraan untuk periode yang dipilih.')
                ->send();

            return null;
        }

        return Excel::download(
            new LaporanTagihanSewaKendaraanExport(
                $report['roda2'],
                $report['roda4'],
                $report['summary'],
                $report['periodLabel']
            ),
            'Laporan_Tagihan_Sewa_Kendaraan_' . str_replace(' ', '_', $report['periodLabel']) . '.xlsx'
        );
    }

    protected function buildReportData(string $month, string $year): array
    {
        $startDate = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $periodLabel = $startDate->translatedFormat('F Y');

        $vehicles = data_r2r4::query()
            ->where('stat', 'Sewa - Kontrak Berjalan')
            ->with(['kontrak_detail.kontrak'])
            ->get();

        $rows = collect();

        foreach ($vehicles as $vehicle) {
            if ($vehicle->tgl_stop_tagihan && Carbon::parse($vehicle->tgl_stop_tagihan)->startOfDay()->lte($startDate)) {
                continue;
            }

            $activeDetail = $vehicle->kontrak_detail
                ->filter(fn ($detail) => $detail->kontrak)
                ->first(function ($detail) use ($startDate, $endDate) {
                    $kontrak = $detail->kontrak;

                    return Carbon::parse($kontrak->tgl_awal)->startOfDay()->lte($endDate)
                        && Carbon::parse($kontrak->tgl_akhir)->startOfDay()->gte($startDate);
                });

            if (! $activeDetail || ! $activeDetail->kontrak) {
                continue;
            }

            $kontrak = $activeDetail->kontrak;
            $type = str_contains(strtoupper((string) $vehicle->jns_brg), 'R4') ? 'R4' : 'R2';

            $rows->push([
                'type' => $type,
                'no_kontrak' => $kontrak->no_kontrak,
                'plat' => $vehicle->plat,
                'jenis_type' => $vehicle->nm_brg,
                'tahun' => $vehicle->thn,
                'nomor_mesin' => $vehicle->no_mesin,
                'nomor_rangka' => $vehicle->no_rangka,
                'awal' => $kontrak->tgl_awal,
                'akhir' => $kontrak->tgl_akhir,
                'uraian' => $kontrak->judul ?: ('Sewa kendaraan ' . strtolower($vehicle->jns_brg ?? '')),
                'harga_kontrak' => (int) ($vehicle->hrg_sewa ?? 0),
                'penanggung_jawab' => $vehicle->pemegang,
                'departemen' => $vehicle->departemen,
                'tgl_stop_tagihan' => $vehicle->tgl_stop_tagihan,
                'alasan_stop_tagihan' => $vehicle->alasan_stop_tagihan,
            ]);
        }

        $groupedCounts = $rows
            ->groupBy(fn (array $row) => $row['type'] . '|' . $row['no_kontrak'])
            ->map(fn (Collection $group) => $group->count());

        $normalized = $rows->map(function (array $row, int $index) use ($groupedCounts) {
            $jumlahUnit = $groupedCounts[$row['type'] . '|' . $row['no_kontrak']] ?? 1;

            return [
                'type' => $row['type'],
                'no' => $index + 1,
                'no_kontrak' => $row['no_kontrak'],
                'plat' => $row['plat'],
                'jenis_type' => $row['jenis_type'],
                'tahun' => $row['tahun'],
                'nomor_mesin' => $row['nomor_mesin'],
                'nomor_rangka' => $row['nomor_rangka'],
                'awal' => $row['awal'],
                'akhir' => $row['akhir'],
                'uraian' => $row['uraian'],
                'jumlah_unit' => $jumlahUnit,
                'harga_kontrak' => $row['harga_kontrak'],
                'total_harga_kontrak' => $row['harga_kontrak'] * $jumlahUnit,
                'penanggung_jawab' => $row['penanggung_jawab'],
                'departemen' => $row['departemen'],
                'tgl_stop_tagihan' => $row['tgl_stop_tagihan'],
                'alasan_stop_tagihan' => $row['alasan_stop_tagihan'],
            ];
        });

        $roda2 = $normalized
            ->filter(fn (array $row) => $row['type'] === 'R2')
            ->values()
            ->all();

        $roda4 = $normalized
            ->filter(fn (array $row) => $row['type'] === 'R4')
            ->values()
            ->all();

        return [
            'roda2' => $this->reindexRows($roda2),
            'roda4' => $this->reindexRows($roda4),
            'summary' => [
                'roda2' => [
                    'unit' => count($roda2),
                    'nominal' => array_sum(array_column($roda2, 'harga_kontrak')),
                ],
                'roda4' => [
                    'unit' => count($roda4),
                    'nominal' => array_sum(array_column($roda4, 'harga_kontrak')),
                ],
            ],
            'periodLabel' => $periodLabel,
        ];
    }

    protected function reindexRows(array $rows): array
    {
        return collect($rows)
            ->values()
            ->map(function (array $row, int $index) {
                $row['no'] = $index + 1;
                return $row;
            })
            ->all();
    }

    protected function getYearOptions(): array
    {
        $years = range((int) date('Y') + 1, 2020);
        return array_combine(array_map('strval', $years), array_map('strval', $years));
    }
}