<x-filament-panels::page>

    <h1 class="text-2xl font-bold">Halaman Publik</h1>
    <p>Ini adalah halaman yang dapat diakses tanpa login.</p>

    <h1 class="text-2xl font-bold">Detail Record</h1>

    <p><strong>ID:</strong> {{ $record->id }}</p>
    <p><strong>Nama:</strong> {{ $record->kode_asset }}</p>
    <p><strong>Deskripsi:</strong> {{ $record->nama_asset }}</p>


   
    <x-filament::input.wrapper>
    <x-filament::input
        type="text"
        value="{{ $record->nama_asset }}"
    />

    
</x-filament::input.wrapper>
</x-filament-panels::page>
