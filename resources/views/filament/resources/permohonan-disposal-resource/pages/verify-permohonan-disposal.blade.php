<x-filament::page>
    <x-filament::grid :columns="2" class="gap-6">

        {{-- INFORMASI PERMOHONAN --}}
        <x-filament::section>
            <x-slot name="heading">
                Informasi Permohonan Disposal
            </x-slot>

            <x-filament::grid :columns="2" class="gap-4">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Kode Asset
                    </div>
                    <div class="font-semibold">
                        {{ $record->asset->kode_asset }}
                    </div>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Asset
                    </div>
                    <div class="font-semibold">
                        {{ $record->asset->nama_asset }}
                    </div>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Kode Asset
                    </div>
                    <div class="font-semibold">
                        {{ $record->asset->kelompok_asset }}
                    </div>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Tanggal Pengajuan
                    </div>
                    <div class="font-semibold">
                        {{ \Carbon\Carbon::parse($record->tgl_pengajuan)->translatedFormat('d F Y') }}
                    </div>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Pemohon
                    </div>
                    <div class="font-semibold">
                        {{ $record->dibuatOleh->nama_karyawan }}
                    </div>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Keterangan
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">
                        {{ $record->keterangan ?? '-' }}
                    </div>
                </div>


                {{-- MODAL GAMBAR ASSET --}}
                @if ($record->gambar)
                    <div x-data="{ open: false }">

                        {{-- Trigger (boleh diganti tombol lain / icon) --}}
                        <x-filament::button
                            size="sm"
                            color="primary"
                            @click="open = true"
                        >
                            Lihat Gambar Disposal
                        </x-filament::button>

                        {{-- Modal --}}
                        <div
                            x-show="open"
                            x-transition
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
                            @click.self="open = false"
                        >
                            <div class="relative bg-white dark:bg-gray-900 rounded-xl p-4 shadow-xl max-w-4xl w-full mx-4">


                                {{-- Image --}}
                                <div class="flex justify-center">
                                    <img
                                        src="{{ asset('storage/' . $record->gambar) }}"
                                        alt="Gambar Asset"
                                        class="max-h-[75vh] object-contain rounded-lg"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <span class="text-sm text-gray-400 italic">
                        Tidak ada gambar
                    </span>
                @endif

                
            </x-filament::grid>
        </x-filament::section>


        {{-- STATUS VERIFIKASI --}}
<x-filament::section>
    <x-slot name="heading">
        Status Verifikasi
    </x-slot>

    <x-filament::grid :columns="2" class="gap-4">

        {{-- Manager --}}
        <div class="space-y-2">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Verifikasi Manager
            </div>

            @if ($record->verif_manager)
                <x-filament::badge
                    color="success"
                    icon="heroicon-o-check-circle"
                    class="px-4 py-2 text-sm min-h-[3rem]"
                >
                    Sudah Diverifikasi
                </x-filament::badge>

                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                    <x-filament::icon name="heroicon-o-clock" class="w-4 h-4" />
                    {{ \Carbon\Carbon::parse($record->tgl_verif_manager)->translatedFormat('d F Y H:i') }}
                </div>
            @else
                <x-filament::badge
                    color="danger"
                    icon="heroicon-o-x-circle"
                    class="px-4 py-2 text-sm min-h-[3rem]"
                >
                    Belum Diverifikasi
                </x-filament::badge>

                <div class="text-xs text-gray-400 italic">
                    -
                </div>
            @endif
        </div>

        {{-- Ketua --}}
        <div class="space-y-2">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Verifikasi Ketua
            </div>

            @if ($record->verif_ketua)
                <x-filament::badge
                    color="success"
                    icon="heroicon-o-check-circle"
                    class="px-4 py-2 text-sm min-h-[3rem]"
                >
                    Sudah Diverifikasi
                </x-filament::badge>

                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                    <x-filament::icon name="heroicon-o-clock" class="w-4 h-4" />
                    {{ \Carbon\Carbon::parse($record->tgl_verif_ketua)->translatedFormat('d F Y H:i') }}
                </div>
            @else
                <x-filament::badge
                    color="warning"
                    icon="heroicon-o-clock"
                    class="px-4 py-2 text-sm min-h-[3rem]"
                >
                    Menunggu Verifikasi
                </x-filament::badge>

                <div class="text-xs text-gray-400 italic">
                    -
                </div>
            @endif
        </div>

    </x-filament::grid>
</x-filament::section>


    </x-filament::grid>
</x-filament::page>
