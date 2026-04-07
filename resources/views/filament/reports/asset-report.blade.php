<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventaris Aset</title>
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
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 7pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN INVENTARIS ASET</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama Aset</th>
                <th>Kelompok</th>
                <th>Tgl Beli</th>
                <th>Harga Beli</th>
                <th>Lokasi/Ruangan</th>
                <th>Penanggung Jawab</th>
                <th>Pemakai</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->kode_asset }}</td>
                <td>{{ $row->nama_asset }}</td>
                <td class="text-center">{{ $row->kelompok_asset }}</td>
                <td class="text-center">{{ $row->tgl_beli ? date('d/m/Y', strtotime($row->tgl_beli)) : '-' }}</td>
                <td class="text-right">Rp. {{ number_format($row->hrg_beli, 0, ',', '.') }}</td>
                <td>{{ $row->ruangan?->ruangan }} - {{ $row->ruangan?->lokasi }}</td>
                <td>{{ $row->penanggung_jawab?->nama_karyawan }}</td>
                <td>{{ $row->karyawan?->nama_karyawan }}</td>
                <td class="text-center">{{ $row->status_barang }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right; background-color: #f9fafb;">Total Nilai Aset:</th>
                <th colspan="5" style="background-color: #f9fafb;">Rp. {{ number_format($records->sum('hrg_beli'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}<br>
        Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>
