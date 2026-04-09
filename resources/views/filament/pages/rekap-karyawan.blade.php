<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900">
            <div class="mb-4">
                <h3 class="text-lg font-bold">Rekap Karyawan per Divisi</h3>
                <p class="text-sm text-gray-500">
                    Menampilkan total karyawan per divisi berdasarkan jenis kelamin dan status karyawan.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-2 text-left">Divisi</th>
                            <th class="px-3 py-2 text-center">Laki-Laki</th>
                            <th class="px-3 py-2 text-center">Perempuan</th>
                            <th class="px-3 py-2 text-center">L/P</th>
                            <th class="px-3 py-2 text-center">Aktif</th>
                            <th class="px-3 py-2 text-center">Pensiun</th>
                            <th class="px-3 py-2 text-center">Nonaktif</th>
                            <th class="px-3 py-2 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->rekapPerDivisi as $row)
                            <tr class="border-b dark:border-gray-800">
                                <td class="px-3 py-2 font-semibold">{{ $row['divisi'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['laki_laki'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['perempuan'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['campuran'] }}</td>
                                <td class="px-3 py-2 text-center text-emerald-600 font-semibold">{{ $row['aktif'] }}</td>
                                <td class="px-3 py-2 text-center text-rose-600 font-semibold">{{ $row['pensiun'] }}</td>
                                <td class="px-3 py-2 text-center text-slate-600 font-semibold">{{ $row['nonaktif'] }}</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $row['total'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-gray-500">
                                    Belum ada data karyawan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>