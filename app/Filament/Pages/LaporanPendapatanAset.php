<?php

namespace App\Filament\Pages;

use App\Models\data_r2r4;
use App\Models\PenjualanR2r4;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

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

    public function mount(): void
    {
        $year = (int) now()->format('Y');

        $this->form->fill([
            'year' => (string) $year,
        ]);

        $this->loadReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
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

        $this->selectedYear = $year;
        $this->monthLabels = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => Carbon::createFromDate($year, $month, 1)->translatedFormat('F')])
            ->all();

        $r2Income = [];
        $r4Income = [];
        $r2Units = [];
        $r4Units = [];
        $disposalIncome = [];
        $vehicleSalesIncome = [];
        $monthlyVehicleDetails = [];

        foreach (range(1, 12) as $month) {
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

    protected function getMonthlyVehicleRentalData(int $year, int $month): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $vehicles = data_r2r4::query()
            ->whereIn('stat', ['Sewa - Kontrak Berjalan', 'Sewa dihentikan'])
            ->with(['kontrak_detail.kontrak'])
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

        foreach (range(1, 12) as $month) {
            $currentVehicles = collect($monthlyVehicleDetails[$month][$type] ?? [])->keyBy('id');

            if ($month > 1) {
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

    protected function getYearOptions(): array
    {
        $years = range((int) date('Y') + 1, 2020);

        return array_combine(array_map('strval', $years), array_map('strval', $years));
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