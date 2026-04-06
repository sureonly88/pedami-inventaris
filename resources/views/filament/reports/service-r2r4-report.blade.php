<!DOCTYPE html>
<html>
<head>
    <title>Laporan Riwayat Service Kendaraan</title>
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
        <h1>LAPORAN RIWAYAT SERVICE KENDARAAN (R2 & R4)</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Servis</th>
                <th>Kode</th>
                <th>Nopol</th>
                <th>Jenis Pekerjaan</th>
                <th>Biaya</th>
                <th>Bengkel</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->tanggal_servis ? date('d/m/Y', strtotime($row->tanggal_servis)) : '-' }}</td>
                <td class="text-center">{{ $row->dataR2r4?->kode_brg }}</td>
                <td class="text-center"><b>{{ $row->dataR2r4?->plat }}</b></td>
                <td>{{ $row->jenis_servis }}</td>
                <td class="text-right">Rp. {{ number_format($row->biaya, 0, ',', '.') }}</td>
                <td>{{ $row->bengkel }}</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right; background-color: #f9fafb;">Total Biaya:</th>
                <th colspan="3" style="background-color: #f9fafb;">Rp. {{ number_format($records->sum('biaya'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}<br>
        Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>
