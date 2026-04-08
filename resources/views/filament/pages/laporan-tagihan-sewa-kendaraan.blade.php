<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border bg-white p-6 shadow-sm dark:bg-gray-900">
            {{ $this->form }}

            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button wire:click="loadReport" icon="heroicon-m-funnel">
                    Tampilkan Laporan
                </x-filament::button>

                <x-filament::button wire:click="exportExcel" color="success" icon="heroicon-m-arrow-down-tray">
                    Export Excel
                </x-filament::button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Total Roda 2 - {{ $this->periodLabel }}</div>
                <div class="mt-1 text-lg font-bold">{{ $this->summary['roda2']['unit'] ?? 0 }} Unit</div>
                <div class="text-primary-600 font-semibold">Rp {{ number_format($this->summary['roda2']['nominal'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Total Roda 4 - {{ $this->periodLabel }}</div>
                <div class="mt-1 text-lg font-bold">{{ $this->summary['roda4']['unit'] ?? 0 }} Unit</div>
                <div class="text-primary-600 font-semibold">Rp {{ number_format($this->summary['roda4']['nominal'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        @php
            $sections = [
                'Roda 2' => $this->roda2Rows,
                'Roda 4' => $this->roda4Rows,
            ];
        @endphp

        @foreach ($sections as $title => $rows)
            <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
                <div class="mb-4">
                    <h3 class="text-lg font-bold">{{ $title }}</h3>
                    <p class="text-sm text-gray-500">Periode {{ $this->periodLabel }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50 dark:bg-gray-800">
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">No. Kontrak</th>
                                <th class="px-3 py-2 text-left">Plat</th>
                                <th class="px-3 py-2 text-left">Jenis/Type</th>
                                <th class="px-3 py-2 text-left">Awal</th>
                                <th class="px-3 py-2 text-left">Akhir</th>
                                <th class="px-3 py-2 text-left">Harga</th>
                                <th class="px-3 py-2 text-left">Penanggung Jawab</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="border-b dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $row['no'] }}</td>
                                    <td class="px-3 py-2">{{ $row['no_kontrak'] }}</td>
                                    <td class="px-3 py-2 font-semibold">{{ $row['plat'] }}</td>
                                    <td class="px-3 py-2">{{ $row['jenis_type'] }}</td>
                                    <td class="px-3 py-2">{{ $row['awal'] ? \Carbon\Carbon::parse($row['awal'])->format('d/m/Y') : '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['akhir'] ? \Carbon\Carbon::parse($row['akhir'])->format('d/m/Y') : '-' }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($row['harga_kontrak'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">{{ $row['penanggung_jawab'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-6 text-center text-gray-500">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @php
            $historySections = [
                'History Tidak Ditagihkan - Roda 2' => $this->historyRoda2Rows,
                'History Tidak Ditagihkan - Roda 4' => $this->historyRoda4Rows,
            ];
        @endphp

        @foreach ($historySections as $title => $rows)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-900 dark:bg-gray-900">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-amber-700 dark:text-amber-400">{{ $title }}</h3>
                    <p class="text-sm text-gray-500">Unit yang tidak lagi ditagihkan pada periode {{ $this->periodLabel }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-amber-100 dark:bg-gray-800">
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">No. Kontrak</th>
                                <th class="px-3 py-2 text-left">Plat</th>
                                <th class="px-3 py-2 text-left">Jenis/Type</th>
                                <th class="px-3 py-2 text-left">Tanggal Stop</th>
                                <th class="px-3 py-2 text-left">Alasan</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="border-b dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $row['no'] }}</td>
                                    <td class="px-3 py-2">{{ $row['no_kontrak'] }}</td>
                                    <td class="px-3 py-2 font-semibold">{{ $row['plat'] }}</td>
                                    <td class="px-3 py-2">{{ $row['jenis_type'] }}</td>
                                    <td class="px-3 py-2">{{ $row['tgl_stop_tagihan'] ? \Carbon\Carbon::parse($row['tgl_stop_tagihan'])->translatedFormat('d F Y') : '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['alasan_stop_tagihan'] ?: '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['status'] }}</td>
                                    <td class="px-3 py-2">{{ $row['keterangan'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-6 text-center text-gray-500">Tidak ada data history.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>