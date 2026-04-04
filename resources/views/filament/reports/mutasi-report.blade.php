<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Aset</title>
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
        .sub-header { font-size: 7pt; color: #666; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 7pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MUTASI ASET</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Tgl Mutasi</th>
                <th rowspan="2">Aset</th>
                <th colspan="3">Dari (Asal)</th>
                <th colspan="3">Ke (Tujuan)</th>
            </tr>
            <tr>
                <th>Ruangan</th>
                <th>PJ</th>
                <th>Pemakai</th>
                <th>Ruangan</th>
                <th>PJ</th>
                <th>Pemakai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row->tgl_mutasi ? date('d/m/Y', strtotime($row->tgl_mutasi)) : '-' }}</td>
                <td>
                    <b>{{ $row->asset?->kode_asset }}</b><br>
                    <span class="sub-header">{{ $row->asset?->nama_asset }}</span>
                </td>
                <td>{{ $row->ruangan_a?->ruangan }}</td>
                <td>{{ $row->penanggung_jawab_a?->nama_karyawan }}</td>
                <td>{{ $row->karyawan_a?->nama_karyawan }}</td>
                <td>{{ $row->ruangan_t?->ruangan }}</td>
                <td>{{ $row->penanggung_jawab_t?->nama_karyawan }}</td>
                <td>{{ $row->karyawan_t?->nama_karyawan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
