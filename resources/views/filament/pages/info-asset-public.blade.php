<x-filament-panels::page>

    <!-- <h1 class="text-2xl font-bold">Halaman Publik</h1>
    <p>Ini adalah halaman yang dapat diakses tanpa login.</p> -->

    <h1 class="text-2xl font-bold">INFORMASI INVENTARIS ASSET</h1>

    <p><strong>ID:</strong> {{ $record->id }}</p>
    <p><strong>Kode Asset:</strong> {{ $record->kode_asset }}</p>
    <p><strong>Nama:</strong> {{ $record->nama_asset }}</p>
    <p><strong>Tanggal Pembelian:</strong> {{ $record->tgl_beli }}</p>
    <p><strong>Harga: Rp.</strong> {{ number_format($record->hrg_beli, 0, ',', '.') }}</p>
    <p><strong>Ruang/Lokasi:</strong> {{ $record->ruangan->lokasi }}</p>
    <p><strong>Divisi:</strong> {{ $record->ruangan->ruangan }}</p>
    <p><strong>Status Barang:</strong> {{ $record->status_barang }}</p>
    <p><strong>Gambar Barang:</strong> {{ $urlGambar }}


    <img src="{{ url("storage/".$record->gambar) }}" alt="" title="" width="300" height="300"/>
</p>
   
    <x-filament::input.wrapper>
    <x-filament::input
        type="text"
        value="{{ $record->nama_asset }}"
    />

    
</x-filament::input.wrapper>
</x-filament-panels::page>
