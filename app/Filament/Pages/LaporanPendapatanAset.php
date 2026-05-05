<?php

namespace App\Filament\Pages;

use App\Models\data_r2r4;
use App\Models\PenjualanR2r4;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;

class LaporanPendapatanAset extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $title = 'Laporan Pendapatan Aset';

    protected static ?string $navigationLabel = 'Pendapatan Aset';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.laporan-pendapatan-aset';

    public ?array $data = [];

    public array $incomeRows = [];

    public array $unitRows = [];

    public array $monthLabels = [];

    public array $vehicleTrendDetails = [
        'r2' => [],
        'r4' => [],
    ];

    public int $selectedYear;

    public int $selectedStartMonth;

    public int $selectedEndMonth;

    public function mount(): void
    {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('n');

        $this->form->fill([
            'year' => (string) $year,
            'start_month' => (string) $month,
            'end_month' => (string) $month,
        ]);

        $this->loadReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Select::make('start_month')
                        ->label('Dari Bulan')
                        ->options($this->getMonthOptions())
                        ->required(),
                    Select::make('end_month')
                        ->label('Sampai Bulan')
                        ->options($this->getMonthOptions())
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
        $year = (int) ($state['year'] ?? now()->year);
        $startMonth = (int) ($state['start_month'] ?? now()->month);
        $endMonth = (int) ($state['end_month'] ?? $startMonth);

        if ($endMonth < $startMonth) {
            $endMonth = $startMonth;

            $this->form->fill([
                ...$state,
                'end_month' => (string) $endMonth,
            ]);
        }

        $this->selectedYear = $year;
        $this->selectedStartMonth = $startMonth;
        $this->selectedEndMonth = $endMonth;
        $selectedMonths = range($startMonth, $endMonth);

        $this->monthLabels = collect($selectedMonths)
            ->mapWithKeys(fn (int $month) => [$month => Carbon::createFromDate($year, $month, 1)->translatedFormat('F')])
            ->all();

        $r2Income = [];
        $r4Income = [];
        $r2Units = [];
        $r4Units = [];
        $disposalIncome = [];
        $vehicleSalesIncome = [];
        $monthlyVehicleDetails = [];

        foreach ($selectedMonths as $month) {
            $monthlyData = $this->getMonthlyVehicleRentalData($year, $month);

            $r2Income[$month] = $monthlyData['r2_nominal'];
            $r4Income[$month] = $monthlyData['r4_nominal'];
            $r2Units[$month] = $monthlyData['r2_unit'];
            $r4Units[$month] = $monthlyData['r4_unit'];
            $disposalIncome[$month] = 0;
            $vehicleSalesIncome[$month] = $this->getMonthlyVehicleSales($year, $month);
            $monthlyVehicleDetails[$month] = $monthlyData['vehicles'];
        }

        $this->incomeRows = [
            $this->makeRow('Tagihan sewa kendaraan Roda Dua (R2)', $r2Income),
            $this->makeRow('Tagihan sewa kendaraan Roda Empat (R4)', $r4Income),
            $this->makeRow('Penjualan Barang Disposal', $disposalIncome),
            $this->makeRow('Penjualan Kendaraan', $vehicleSalesIncome),
        ];

        $this->unitRows = [
            $this->makeRow('Tagihan sewa kendaraan Roda Dua (R2)', $r2Units),
            $this->makeRow('Tagihan sewa kendaraan Roda Empat (R4)', $r4Units),
        ];

        $this->vehicleTrendDetails = [
            'r2' => $this->buildVehicleTrendDetails($monthlyVehicleDetails, 'r2'),
            'r4' => $this->buildVehicleTrendDetails($monthlyVehicleDetails, 'r4'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Laporan Pendapatan Aset')
                ->modalSubmitActionLabel('Unduh PDF')
                ->form([
                    Grid::make(3)->schema([
                        Select::make('start_month')
                            ->label('Dari Bulan')
                            ->options($this->getMonthOptions())
                            ->default(fn () => (string) $this->selectedStartMonth)
                            ->required(),
                        Select::make('end_month')
                            ->label('Sampai Bulan')
                            ->options($this->getMonthOptions())
                            ->default(fn () => (string) $this->selectedEndMonth)
                            ->required(),
                        Select::make('year')
                            ->label('Tahun')
                            ->options($this->getYearOptions())
                            ->default(fn () => (string) $this->selectedYear)
                            ->required(),
                    ]),
                ])
                ->action(function (array $data) {
                    $year = (int) ($data['year'] ?? now()->year);
                    $startMonth = (int) ($data['start_month'] ?? now()->month);
                    $endMonth = (int) ($data['end_month'] ?? $startMonth);

                    if ($endMonth < $startMonth) {
                        Notification::make()
                            ->warning()
                            ->title('Periode tidak valid')
                            ->body('Bulan sampai harus sama atau lebih besar dari bulan mulai.')
                            ->send();

                        return null;
                    }

                    $report = $this->buildPrintableReport($year, $startMonth, $endMonth);

                    if (empty($report['incomeRows']) && empty($report['unitRows'])) {
                        Notification::make()
                            ->warning()
                            ->title('Data tidak ditemukan')
                            ->body('Tidak ada data laporan pendapatan aset untuk periode yang dipilih.')
                            ->send();

                        return null;
                    }

                    $paper = $this->resolvePdfPaper(count($report['monthLabels']));

                    $pdf = Pdf::setPaper($paper['size'], $paper['orientation'])->loadHtml(
                        Blade::render('filament.reports.laporan-pendapatan-aset-report', $report)
                    );

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'Laporan_Pendapatan_Aset_' . str_replace([' ', '/'], ['_', '-'], $report['period']) . '.pdf');
                }),
        ];
    }

    protected function getMonthlyVehicleRentalData(int $year, int $month): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $vehicles = data_r2r4::query()
            ->where(function ($query) {
                $query
                    ->whereIn('stat', ['Sewa - Kontrak Berjalan', 'Sewa dihentikan'])
                    ->orWhereHas('penjualanR2r4');
            })
            ->with(['kontrak_detail.kontrak', 'penjualanR2r4'])
            ->get();

        $result = [
            'r2_nominal' => 0,
            'r4_nominal' => 0,
            'r2_unit' => 0,
            'r4_unit' => 0,
            'vehicles' => [
                'r2' => [],
                'r4' => [],
            ],
        ];

        foreach ($vehicles as $vehicle) {
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

            $saleDate = $vehicle->penjualanR2r4?->tgl_jual
                ? Carbon::parse($vehicle->penjualanR2r4->tgl_jual)->startOfDay()
                : null;

            if ($saleDate && $saleDate->lte($startDate)) {
                continue;
            }

            if ($vehicle->tgl_stop_tagihan && Carbon::parse($vehicle->tgl_stop_tagihan)->startOfDay()->lte($startDate)) {
                continue;
            }

            $type = str_contains(strtoupper((string) $vehicle->jns_brg), 'R4') ? 'r4' : 'r2';

            $result[$type . '_unit']++;
            $result[$type . '_nominal'] += (float) ($vehicle->hrg_sewa ?? 0);
            $result['vehicles'][$type][] = [
                'id' => $vehicle->id,
                'kode' => $vehicle->kode_brg ?? '-',
                'plat' => $vehicle->plat ?? '-',
                'nama' => $vehicle->nm_brg ?? '-',
                'pemegang' => $vehicle->pemegang ?? '-',
                'departemen' => $vehicle->departemen ?? '-',
                'harga' => (float) ($vehicle->hrg_sewa ?? 0),
            ];
        }

        return $result;
    }

    protected function buildVehicleTrendDetails(array $monthlyVehicleDetails, string $type): array
    {
        $trendDetails = [];
        $previousVehicles = [];

        foreach (array_keys($this->monthLabels) as $month) {
            $currentVehicles = collect($monthlyVehicleDetails[$month][$type] ?? [])->keyBy('id');

            if (! empty($previousVehicles)) {
                $added = $currentVehicles->keys()->diff(array_keys($previousVehicles))->values();
                $removed = collect(array_keys($previousVehicles))->diff($currentVehicles->keys())->values();

                $trendDetails[$month] = [
                    'added' => $added->map(fn ($id) => $currentVehicles[$id])->values()->all(),
                    'removed' => $removed->map(fn ($id) => $previousVehicles[$id])->values()->all(),
                ];
            } else {
                $trendDetails[$month] = [
                    'added' => [],
                    'removed' => [],
                ];
            }

            $previousVehicles = $currentVehicles->all();
        }

        return $trendDetails;
    }

    protected function getMonthlyVehicleSales(int $year, int $month): float
    {
        return (float) PenjualanR2r4::query()
            ->whereYear('tgl_jual', $year)
            ->whereMonth('tgl_jual', $month)
            ->sum('hrg_jual');
    }

    protected function makeRow(string $label, array $months): array
    {
        return [
            'label' => $label,
            'months' => $months,
            'total' => array_sum($months),
        ];
    }

    protected function buildPrintableReport(int $year, int $startMonth, int $endMonth): array
    {
        $originalState = $this->form->getState();

        $this->form->fill([
            'year' => (string) $year,
            'start_month' => (string) $startMonth,
            'end_month' => (string) $endMonth,
        ]);

        $this->loadReport();

        $period = $this->getPeriodLabel($year, $startMonth, $endMonth);
        $incomeTotalsByMonth = collect(array_keys($this->monthLabels))
            ->mapWithKeys(fn ($month) => [$month => collect($this->incomeRows)->sum(fn ($row) => $row['months'][$month] ?? 0)])
            ->all();
        $unitTotalsByMonth = collect(array_keys($this->monthLabels))
            ->mapWithKeys(fn ($month) => [$month => collect($this->unitRows)->sum(fn ($row) => $row['months'][$month] ?? 0)])
            ->all();

        $roda2IncomeRow = collect($this->incomeRows)->firstWhere('label', 'Tagihan sewa kendaraan Roda Dua (R2)');
        $roda4IncomeRow = collect($this->incomeRows)->firstWhere('label', 'Tagihan sewa kendaraan Roda Empat (R4)');

        $report = [
            'period' => $period,
            'selectedYear' => $this->selectedYear,
            'monthLabels' => $this->monthLabels,
            'incomeRows' => $this->incomeRows,
            'unitRows' => $this->unitRows,
            'incomeTotalsByMonth' => $incomeTotalsByMonth,
            'unitTotalsByMonth' => $unitTotalsByMonth,
            'incomeGrandTotal' => $this->incomeGrandTotal,
            'unitGrandTotal' => $this->unitGrandTotal,
            'incomeSparklineBuilder' => fn (array $values, string $color = '#0ea5e9') => $this->buildSparklineImage($values, $color),
            'unitSparklineBuilder' => fn (array $values, string $color = '#f59e0b') => $this->buildSparklineImage($values, $color),
            'incomeTotalSparkline' => $this->buildSparklineImage($incomeTotalsByMonth, '#16a34a'),
            'unitTotalSparkline' => $this->buildSparklineImage($unitTotalsByMonth, '#7c3aed'),
            'roda2Notes' => $roda2IncomeRow ? $this->buildTrendNotes('Pendapatan kendaraan roda dua', 'r2', $roda2IncomeRow['months']) : [],
            'roda4Notes' => $roda4IncomeRow ? $this->buildTrendNotes('Pendapatan kendaraan roda empat', 'r4', $roda4IncomeRow['months']) : [],
        ];

        $this->form->fill($originalState);
        $this->loadReport();

        return $report;
    }

    protected function getPeriodLabel(int $year, int $startMonth, int $endMonth): string
    {
        $startMonthLabel = strtoupper(Carbon::createFromDate($year, $startMonth, 1)->translatedFormat('F'));
        $endMonthLabel = strtoupper(Carbon::createFromDate($year, $endMonth, 1)->translatedFormat('F'));

        return $startMonth === $endMonth
            ? $startMonthLabel . ' ' . $year
            : $startMonthLabel . ' - ' . $endMonthLabel . ' ' . $year;
    }

    protected function buildTrendNotes(string $label, string $typeKey, array $months): array
    {
        $notes = [];
        $previousValue = null;
        $previousMonth = null;

        foreach ($months as $monthNumber => $value) {
            if ($previousValue !== null && $value !== $previousValue) {
                $selisih = $value - $previousValue;
                $status = $selisih > 0 ? 'kenaikan' : 'penurunan';
                $trendDetail = $this->vehicleTrendDetails[$typeKey][$monthNumber] ?? ['added' => [], 'removed' => []];

                $identitas = [];

                if ($status === 'kenaikan' && ! empty($trendDetail['added'])) {
                    $identitas[] = 'kendaraan bertambah: ' . collect($trendDetail['added'])
                        ->map(fn ($vehicle) => trim(
                            ($vehicle['kode'] ?? '-') . ' / ' .
                            ($vehicle['plat'] ?? '-') . ' / ' .
                            ($vehicle['nama'] ?? '-') . ' / ' .
                            ($vehicle['pemegang'] ?? '-') . ' / ' .
                            ($vehicle['departemen'] ?? '-')
                        ))
                        ->implode('; ');
                }

                if ($status === 'penurunan' && ! empty($trendDetail['removed'])) {
                    $identitas[] = 'kendaraan berkurang: ' . collect($trendDetail['removed'])
                        ->map(fn ($vehicle) => trim(
                            ($vehicle['kode'] ?? '-') . ' / ' .
                            ($vehicle['plat'] ?? '-') . ' / ' .
                            ($vehicle['nama'] ?? '-') . ' / ' .
                            ($vehicle['pemegang'] ?? '-') . ' / ' .
                            ($vehicle['departemen'] ?? '-')
                        ))
                        ->implode('; ');
                }

                $notes[] = sprintf(
                    '%s mengalami %s sebesar Rp %s dari %s ke %s%s.',
                    $label,
                    $status,
                    number_format(abs($selisih), 0, ',', '.'),
                    strtoupper($this->monthLabels[$previousMonth] ?? ''),
                    strtoupper($this->monthLabels[$monthNumber] ?? ''),
                    ! empty($identitas) ? ' dengan ' . implode(' | ', $identitas) : ''
                );
            }

            $previousValue = $value;
            $previousMonth = $monthNumber;
        }

        if (empty($notes)) {
            $notes[] = $label . ' cenderung stabil pada periode ' . $this->getPeriodLabel($this->selectedYear, $this->selectedStartMonth, $this->selectedEndMonth) . '.';
        }

        return $notes;
    }

    protected function buildSparklinePoints(array $values, int $width = 140, int $height = 36): string
    {
        $numbers = array_values($values);
        $count = count($numbers);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return '0,' . ($height / 2);
        }

        $min = min($numbers);
        $max = max($numbers);
        $range = $max - $min;

        return collect($numbers)->map(function ($value, $index) use ($count, $width, $height, $min, $range) {
            $x = ($index / max($count - 1, 1)) * $width;
            $y = $range == 0
                ? $height / 2
                : $height - ((($value - $min) / $range) * ($height - 6)) - 3;

            return round($x, 2) . ',' . round($y, 2);
        })->implode(' ');
    }

    protected function buildSparklineImage(array $values, string $strokeColor, int $width = 140, int $height = 36): string
    {
        $points = $this->buildSparklinePoints($values, $width, $height);

        if ($points === '') {
            return '';
        }

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%2$d" viewBox="0 0 %1$d %2$d"><polyline fill="none" stroke="%3$s" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="%4$s"/></svg>',
            $width,
            $height,
            $strokeColor,
            $points,
        );

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    protected function resolvePdfPaper(int $monthCount): array
    {
        if ($monthCount >= 10) {
            return [
                'size' => 'a3',
                'orientation' => 'landscape',
            ];
        }

        return [
            'size' => 'a4',
            'orientation' => 'landscape',
        ];
    }

    protected function getYearOptions(): array
    {
        $years = range((int) date('Y') + 1, 2020);

        return array_combine(array_map('strval', $years), array_map('strval', $years));
    }

    protected function getMonthOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                (string) $month => Carbon::create()->month($month)->translatedFormat('F'),
            ])
            ->all();
    }

    public function getIncomeGrandTotalProperty(): float
    {
        return (float) collect($this->incomeRows)->sum('total');
    }

    public function getUnitGrandTotalProperty(): int
    {
        return (int) collect($this->unitRows)->sum('total');
    }
}