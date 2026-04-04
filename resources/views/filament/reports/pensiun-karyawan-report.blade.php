<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pensiun Karyawan</title>
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
        <h1>LAPORAN PENSIUN KARYAWAN</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Pensiun</th>
                <th>No SK</th>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Jenis</th>
                <th>Pesangon</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->tgl_pensiun ? date('d/m/Y', strtotime($row->tgl_pensiun)) : '-' }}</td>
                <td>{{ $row->no_sk }}</td>
                <td class="text-center">{{ $row->karyawan?->nik }}</td>
                <td>{{ $row->karyawan?->nama_karyawan }}</td>
                <td>{{ $row->jabatan_terakhir }}</td>
                <td>{{ $row->divisiTerakhir?->nama_divisi }}</td>
                <td class="text-center">{{ $row->jenis_pensiun }}</td>
                <td class="text-right">Rp. {{ number_format($row->pesangon, 0, ',', '.') }}</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" style="text-align: right; background-color: #f9fafb;">Total Pesangon:</th>
                <th colspan="2" style="background-color: #f9fafb;">Rp. {{ number_format($records->sum('pesangon'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
