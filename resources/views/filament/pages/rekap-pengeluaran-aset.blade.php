<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
            <div class="mb-4">
                <h3 class="text-lg font-bold">Rekap Pengeluaran Biaya Aset per Divisi</h3>
                <p class="text-sm text-gray-500">
                    Menampilkan total aset dan akumulasi harga pembelian inventaris aset pada setiap divisi.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-2 text-left">Divisi</th>
                            <th class="px-3 py-2 text-center">Total Aset</th>
                            <th class="px-3 py-2 text-right">Total Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->rekapPengeluaranAsetPerDivisi as $row)
                            <tr class="border-b dark:border-gray-800">
                                <td class="px-3 py-2 font-semibold">{{ $row['divisi'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['total_aset'] }}</td>
                                <td class="px-3 py-2 text-right font-bold text-sky-700">
                                    Rp {{ number_format($row['total_pengeluaran'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-6 text-center text-gray-500">
                                    Belum ada data inventaris aset.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>