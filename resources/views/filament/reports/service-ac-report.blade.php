<!DOCTYPE html>
<html>
<head>
    <title>Laporan Riwayat Service Aset</title>
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
        <h1>LAPORAN RIWAYAT SERVICE ASET</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Service</th>
                <th>Kode</th>
                <th>Nama Aset</th>
                <th>Pekerjaan</th>
                <th>Biaya</th>
                <th>Teknisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->tanggal_service ? date('d/m/Y', strtotime($row->tanggal_service)) : '-' }}</td>
                <td class="text-center">{{ $row->asset?->kode_asset }}</td>
                <td>{{ $row->asset?->nama_asset }}</td>
                <td>{{ $row->jenis_pekerjaan }}</td>
                <td class="text-right">Rp. {{ number_format($row->biaya, 0, ',', '.') }}</td>
                <td>{{ $row->teknisi }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right; background-color: #f9fafb;">Total Biaya:</th>
                <th colspan="2" style="background-color: #f9fafb;">Rp. {{ number_format($records->sum('biaya'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
