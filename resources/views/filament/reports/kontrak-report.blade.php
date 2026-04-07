<!DOCTYPE html>
<html>
<head>
    <title>Laporan Daftar Kontrak</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 14pt; color: #000; }
        .header h2 { margin: 5px 0; font-size: 12pt; color: #000; }
        .header p { margin: 0; font-size: 10pt; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px 4px; text-align: left; }
        th { background-color: #E5E7EB; color: #000; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 8pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DAFTAR KONTRAK</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Kontrak</th>
                <th>Judul Kontrak</th>
                <th>Tgl Awal</th>
                <th>Tgl Akhir</th>
                <th>Masa Sewa</th>
                <th>Unit Kendaraan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->no_kontrak }}</td>
                <td>{{ $row->judul }}</td>
                <td class="text-center">{{ $row->tgl_awal ? date('d/m/Y', strtotime($row->tgl_awal)) : '-' }}</td>
                <td class="text-center">{{ $row->tgl_akhir ? date('d/m/Y', strtotime($row->tgl_akhir)) : '-' }}</td>
                <td class="text-center">
                    @if($row->tgl_awal && $row->tgl_akhir)
                        {{ (int) \Carbon\Carbon::parse($row->tgl_awal)->diffInMonths(\Carbon\Carbon::parse($row->tgl_akhir)) }} Bulan
                    @else
                        -
                    @endif
                </td>
                <td>
                    @foreach($row->kontrakDetails as $detail)
                        {{ $detail->dataR2r4?->plat }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
