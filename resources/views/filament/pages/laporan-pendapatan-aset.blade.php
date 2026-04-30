<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border bg-white p-6 shadow-sm dark:bg-gray-900">
            {{ $this->form }}

            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button wire:click="loadReport" icon="heroicon-m-funnel">
                    Tampilkan Laporan
                </x-filament::button>
            </div>
        </div>

        @php
            $buildSparklinePoints = function (array $values, int $width = 140, int $height = 36) {
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
            };

            $buildTrendNotes = function (string $label, string $typeKey, array $months, array $monthLabels) {
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
                            strtoupper($monthLabels[$previousMonth] ?? ''),
                            strtoupper($monthLabels[$monthNumber] ?? ''),
                            ! empty($identitas) ? ' dengan ' . implode(' | ', $identitas) : ''
                        );
                    }

                    $previousValue = $value;
                    $previousMonth = $monthNumber;
                }

                if (empty($notes)) {
                    $notes[] = $label . ' cenderung stabil sepanjang tahun ' . $this->selectedYear . '.';
                }

                return $notes;
            };

            $roda2IncomeRow = collect($this->incomeRows)->firstWhere('label', 'Tagihan sewa kendaraan Roda Dua (R2)');
            $roda4IncomeRow = collect($this->incomeRows)->firstWhere('label', 'Tagihan sewa kendaraan Roda Empat (R4)');
            $roda2Notes = $roda2IncomeRow ? $buildTrendNotes('Pendapatan kendaraan roda dua', 'r2', $roda2IncomeRow['months'], $this->monthLabels) : [];
            $roda4Notes = $roda4IncomeRow ? $buildTrendNotes('Pendapatan kendaraan roda empat', 'r4', $roda4IncomeRow['months'], $this->monthLabels) : [];
        @endphp

        <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
            <div class="mb-4">
                <h3 class="text-lg font-bold">Total Pendapatan Aset Koperasi Konsumen Pedami</h3>
                <p class="text-sm text-gray-500">Tahun {{ $this->selectedYear }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-2 text-left">No</th>
                            <th class="px-3 py-2 text-left">Jenis Pendapatan</th>
                            @foreach ($this->monthLabels as $monthLabel)
                                <th class="px-3 py-2 text-right">{{ strtoupper($monthLabel) }}</th>
                            @endforeach
                            <th class="px-3 py-2 text-right">TOTAL</th>
                            <th class="px-3 py-2 text-center">GRAFIK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->incomeRows as $index => $row)
                            <tr class="border-b dark:border-gray-800">
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 font-medium">{{ $row['label'] }}</td>
                                @foreach ($row['months'] as $value)
                                    <td class="px-3 py-2 text-right">{{ $value ? number_format($value, 0, ',', '.') : '-' }}</td>
                                @endforeach
                                <td class="px-3 py-2 text-right font-bold text-primary-600">{{ number_format($row['total'], 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-center">
                                    <svg width="140" height="36" viewBox="0 0 140 36" class="mx-auto overflow-visible">
                                        <polyline
                                            fill="none"
                                            stroke="#0ea5e9"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            points="{{ $buildSparklinePoints($row['months']) }}"
                                        />
                                    </svg>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-primary-50 font-bold dark:bg-gray-800">
                            <td colspan="2" class="px-3 py-2">TOTAL PENDAPATAN</td>
                            @foreach (range(1, 12) as $month)
                                <td class="px-3 py-2 text-right">
                                    {{ number_format(collect($this->incomeRows)->sum(fn ($row) => $row['months'][$month] ?? 0), 0, ',', '.') }}
                                </td>
                            @endforeach
                            <td class="px-3 py-2 text-right">{{ number_format($this->incomeGrandTotal, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $incomeTotalsByMonth = collect(range(1, 12))
                                        ->mapWithKeys(fn ($month) => [$month => collect($this->incomeRows)->sum(fn ($row) => $row['months'][$month] ?? 0)])
                                        ->all();
                                @endphp
                                <svg width="140" height="36" viewBox="0 0 140 36" class="mx-auto overflow-visible">
                                    <polyline
                                        fill="none"
                                        stroke="#16a34a"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        points="{{ $buildSparklinePoints($incomeTotalsByMonth) }}"
                                    />
                                </svg>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
            <div class="mb-4">
                <h3 class="text-lg font-bold">Jumlah Unit Aktif Tagihan</h3>
                <p class="text-sm text-gray-500">Tahun {{ $this->selectedYear }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-2 text-left">No</th>
                            <th class="px-3 py-2 text-left">Jenis Pendapatan</th>
                            @foreach ($this->monthLabels as $monthLabel)
                                <th class="px-3 py-2 text-center">{{ strtoupper($monthLabel) }}</th>
                            @endforeach
                            <th class="px-3 py-2 text-center">TOTAL</th>
                            <th class="px-3 py-2 text-center">GRAFIK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->unitRows as $index => $row)
                            <tr class="border-b dark:border-gray-800">
                                <td class="px-3 py-2">{{ $index + 1 }}</td>
                                <td class="px-3 py-2 font-medium">{{ $row['label'] }}</td>
                                @foreach ($row['months'] as $value)
                                    <td class="px-3 py-2 text-center">{{ $value ?: '-' }}</td>
                                @endforeach
                                <td class="px-3 py-2 text-center font-bold text-primary-600">{{ $row['total'] }}</td>
                                <td class="px-3 py-2 text-center">
                                    <svg width="140" height="36" viewBox="0 0 140 36" class="mx-auto overflow-visible">
                                        <polyline
                                            fill="none"
                                            stroke="#f59e0b"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            points="{{ $buildSparklinePoints($row['months']) }}"
                                        />
                                    </svg>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-primary-50 font-bold dark:bg-gray-800">
                            <td colspan="2" class="px-3 py-2">TOTAL JUMLAH UNIT</td>
                            @foreach (range(1, 12) as $month)
                                <td class="px-3 py-2 text-center">
                                    {{ collect($this->unitRows)->sum(fn ($row) => $row['months'][$month] ?? 0) }}
                                </td>
                            @endforeach
                            <td class="px-3 py-2 text-center">{{ $this->unitGrandTotal }}</td>
                            <td class="px-3 py-2 text-center">
                                @php
                                    $unitTotalsByMonth = collect(range(1, 12))
                                        ->mapWithKeys(fn ($month) => [$month => collect($this->unitRows)->sum(fn ($row) => $row['months'][$month] ?? 0)])
                                        ->all();
                                @endphp
                                <svg width="140" height="36" viewBox="0 0 140 36" class="mx-auto overflow-visible">
                                    <polyline
                                        fill="none"
                                        stroke="#7c3aed"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        points="{{ $buildSparklinePoints($unitTotalsByMonth) }}"
                                    />
                                </svg>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 shadow-sm dark:border-amber-900 dark:bg-gray-900 dark:text-amber-300">
            <p class="font-semibold">Catatan:</p>
            <div class="mt-2 space-y-3">
                <div>
                    <p class="font-medium">Roda Dua (R2)</p>
                    <ul class="mt-1 list-disc space-y-1 pl-5">
                        @foreach ($roda2Notes as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="font-medium">Roda Empat (R4)</p>
                    <ul class="mt-1 list-disc space-y-1 pl-5">
                        @foreach ($roda4Notes as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>