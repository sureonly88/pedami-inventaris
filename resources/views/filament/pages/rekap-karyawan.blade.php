<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-900 dark:bg-amber-950/40">
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-amber-900 dark:text-amber-100">Karyawan Mendekati Pensiun</h3>
                    <p class="text-sm text-amber-700 dark:text-amber-200">
                        Menampilkan karyawan aktif/pengurus yang akan mencapai umur pensiun 56 tahun dalam 1 tahun ke depan.
                    </p>
                </div>

                <div class="rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800 dark:bg-amber-900 dark:text-amber-100">
                    {{ count($this->karyawanMendekatiPensiun) }} karyawan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-amber-200 bg-amber-100 dark:border-amber-900 dark:bg-amber-900/60">
                            <th class="px-3 py-2 text-left">NIK</th>
                            <th class="px-3 py-2 text-left">Nama Karyawan</th>
                            <th class="px-3 py-2 text-left">Jabatan</th>
                            <th class="px-3 py-2 text-left">Divisi</th>
                            <th class="px-3 py-2 text-center">Umur Saat Ini</th>
                            <th class="px-3 py-2 text-center">Tanggal Pensiun</th>
                            <th class="px-3 py-2 text-center">Sisa Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->karyawanMendekatiPensiun as $row)
                            <tr class="border-b border-amber-100 dark:border-amber-900/70">
                                <td class="px-3 py-2">{{ $row['nik'] }}</td>
                                <td class="px-3 py-2 font-semibold">{{ $row['nama_karyawan'] }}</td>
                                <td class="px-3 py-2">{{ $row['jabatan'] }}</td>
                                <td class="px-3 py-2">{{ $row['divisi'] }}</td>
                                <td class="px-3 py-2 text-center">{{ $row['umur'] }}</td>
                                <td class="px-3 py-2 text-center font-semibold text-amber-800 dark:text-amber-100">{{ $row['tanggal_pensiun'] }}</td>
                                <td class="px-3 py-2 text-center font-bold text-rose-600 dark:text-rose-300">
                                    {{ $row['sisa_waktu'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-amber-700 dark:text-amber-200">
                                    Tidak ada karyawan yang mendekati umur pensiun 56 tahun dalam 1 tahun ke depan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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