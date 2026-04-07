<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendataan Kendaraan</title>
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
        <h1>LAPORAN PENDATAAN KENDARAAN (R2 & R4)</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Jenis</th>
                <th>Plat</th>
                <th>Nama Barang</th>
                <th>Tahun</th>
                <th>Pajak</th>
                <th>STNK</th>
                <th>Pemegang</th>
                <th>Departemen</th>
                <th>Status</th>
                <th>Harga Sewa</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($records as $index => $row)
            @php $total += $row->hrg_sewa; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->kode_brg }}</td>
                <td class="text-center">{{ $row->jns_brg }}</td>
                <td class="text-center"><b>{{ $row->plat }}</b></td>
                <td>{{ $row->nm_brg }}</td>
                <td class="text-center">{{ $row->thn }}</td>
                <td class="text-center">{{ $row->pajak ? date('d/m/Y', strtotime($row->pajak)) : '-' }}</td>
                <td class="text-center">{{ $row->stnk ? date('d/m/Y', strtotime($row->stnk)) : '-' }}</td>
                <td>{{ $row->pemegang }}</td>
                <td>{{ $row->departemen }}</td>
                <td class="text-center">{{ $row->stat }}</td>
                <td class="text-right">Rp. {{ number_format($row->hrg_sewa, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="11" style="text-align: right; background-color: #f9fafb;">Total Nilai Kendaraan:</th>
                <th style="background-color: #f9fafb;">Rp. {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}<br>
        Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>
