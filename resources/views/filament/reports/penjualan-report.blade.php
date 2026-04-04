<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan R2 & R4</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 14pt; color: #000; }
        .header h2 { margin: 5px 0; font-size: 12pt; color: #000; }
        .header p { margin: 0; font-size: 10pt; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px 4px; text-align: left; }
        th { background-color: #E5E7EB; color: #000; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 8pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN RODA 2 & RODA 4</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Jual</th>
                <th>Kode</th>
                <th>Nopol</th>
                <th>Nama Barang</th>
                <th>Pembeli</th>
                <th>Harga Jual</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->tgl_jual ? date('d/m/Y', strtotime($row->tgl_jual)) : '-' }}</td>
                <td class="text-center">{{ $row->data_r2r4?->kode_brg }}</td>
                <td class="text-center"><b>{{ $row->data_r2r4?->plat }}</b></td>
                <td>{{ $row->data_r2r4?->nm_brg }}</td>
                <td>{{ $row->nm_pembeli }}</td>
                <td class="text-right">Rp. {{ number_format($row->hrg_jual, 0, ',', '.') }}</td>
                <td class="text-center">{{ $row->data_r2r4?->thn }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align: right; background-color: #f9fafb;">Total Penjualan:</th>
                <th colspan="2" style="background-color: #f9fafb;">Rp. {{ number_format($records->sum('hrg_jual'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
