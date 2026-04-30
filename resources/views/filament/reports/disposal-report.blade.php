<!DOCTYPE html>
<html>
<head>
    <title>Laporan Disposal Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 8pt; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 14pt; color: #000; }
        .header h2 { margin: 5px 0; font-size: 12pt; color: #000; }
        .header p { margin: 0; font-size: 10pt; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px 3px; text-align: left; }
        th { background-color: #E5E7EB; color: #000; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 7pt; color: #777; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 7pt; color: white; }
        .asset-image { width: 58px; height: 58px; object-fit: cover; display: block; margin: 0 auto; }
        .asset-image-placeholder { font-size: 7pt; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENGAJUAN DISPOSAL ASET</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Surat</th>
                <th>Tgl</th>
                <th>Kode</th>
                <th>Nama Aset</th>
                <th>Harga Beli</th>
                <th>Lokasi Aset</th>
                <th>Gambar Aset</th>
                <th>Kondisi</th>
                <th>Manager</th>
                <th>Ketua</th>
                <th>Pemohon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            @php
                $gambarAset = null;

                if (!empty($row->asset?->gambar)) {
                    try {
                        $disk = \Illuminate\Support\Facades\Storage::disk('minio');

                        if ($disk->exists($row->asset->gambar)) {
                            $mimeType = $disk->mimeType($row->asset->gambar) ?: 'image/jpeg';
                            $gambarAset = 'data:' . $mimeType . ';base64,' . base64_encode($disk->get($row->asset->gambar));
                        }
                    } catch (\Throwable $e) {
                        $gambarAset = null;
                    }
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row->nomor }}</td>
                <td class="text-center">{{ $row->tgl_pengajuan ? date('d/m/Y', strtotime($row->tgl_pengajuan)) : '-' }}</td>
                <td class="text-center">{{ $row->asset?->kode_asset }}</td>
                <td>{{ $row->asset?->nama_asset }}</td>
                <td>Rp {{ number_format((float) ($row->asset?->hrg_beli ?? 0), 0, ',', '.') }}</td>
                <td>{{ $row->asset?->ruangan?->ruangan ? $row->asset->ruangan->ruangan . ' - ' . ($row->asset->ruangan->lokasi ?? '-') : '-' }}</td>
                <td class="text-center">
                    @if($gambarAset)
                        <img src="{{ $gambarAset }}" alt="Gambar Aset" class="asset-image">
                    @else
                        <div class="asset-image-placeholder">Tidak ada gambar</div>
                    @endif
                </td>
                <td>{{ $row->kondisi }}</td>
                <td class="text-center">{{ $row->verif_manager ? 'V' : '-' }}</td>
                <td class="text-center">{{ $row->verif_ketua ? 'V' : '-' }}</td>
                <td>{{ $row->dibuatOleh?->nama_karyawan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
