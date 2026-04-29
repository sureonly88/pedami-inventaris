<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pengeluaran Biaya Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 14pt; color: #000; }
        .header h2 { margin: 4px 0; font-size: 12pt; color: #000; }
        .header p { margin: 0; font-size: 10pt; }
        .section-title { margin-top: 18px; margin-bottom: 8px; font-size: 11pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 5px 4px; }
        th { background-color: #E5E7EB; color: #000; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 7pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP PENGELUARAN BIAYA ASET</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
        <p>Divisi: {{ $selectedDivisi ?? 'Semua Divisi' }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section-title">Rekap per Divisi</div>
    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th>Divisi</th>
                <th style="width: 15%;">Total Aset</th>
                <th style="width: 24%;">Total Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['divisi'] }}</td>
                    <td class="text-center">{{ $row['total_aset'] }}</td>
                    <td class="text-right">Rp {{ number_format($row['total_pengeluaran'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data rekap.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Rincian Pengeluaran</div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th>Nama Aset</th>
                <th style="width: 18%;">Divisi</th>
                <th style="width: 18%;">Penanggung Jawab</th>
                <th style="width: 16%;">Pemakai</th>
                <th style="width: 16%;">Harga Beli</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->tgl_beli ? date('d/m/Y', strtotime($row->tgl_beli)) : '-' }}</td>
                    <td>{{ $row->nama_asset }}</td>
                    <td>{{ $row->penanggung_jawab?->subdivisi?->divisi?->nama_divisi ?? 'Tanpa Divisi' }}</td>
                    <td>{{ $row->penanggung_jawab?->nama_karyawan ?? '-' }}</td>
                    <td>{{ $row->karyawan?->nama_karyawan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format((float) ($row->hrg_beli ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada rincian pengeluaran untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total Pengeluaran</th>
                <th class="text-right">Rp {{ number_format($details->sum('hrg_beli'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d-m-Y H:i:s') }}<br>
        Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>