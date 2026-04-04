<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Karyawan</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN MUTASI KARYAWAN</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Tgl Mutasi</th>
                <th rowspan="2">No SK</th>
                <th rowspan="2">NIK</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="3">Posisi Asal</th>
                <th colspan="3">Posisi Baru</th>
                <th rowspan="2">Alasan</th>
            </tr>
            <tr>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Sub Divisi</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Sub Divisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->tgl_mutasi ? date('d/m/Y', strtotime($row->tgl_mutasi)) : '-' }}</td>
                <td>{{ $row->no_sk }}</td>
                <td class="text-center">{{ $row->karyawan?->nik }}</td>
                <td>{{ $row->karyawan?->nama_karyawan }}</td>
                <td>{{ $row->jabatan_asal }}</td>
                <td>{{ $row->divisiAsal?->nama_divisi }}</td>
                <td>{{ $row->subdivisiAsal?->nama_sub }}</td>
                <td>{{ $row->jabatan_tujuan }}</td>
                <td>{{ $row->divisiTujuan?->nama_divisi }}</td>
                <td>{{ $row->subdivisiTujuan?->nama_sub }}</td>
                <td>{{ $row->alasan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}
    </div>
</body>
</html>
