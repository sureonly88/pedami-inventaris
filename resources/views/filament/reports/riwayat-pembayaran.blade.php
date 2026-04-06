<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembayaran Kendaraan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #000; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #000; }
        .header p { margin: 5px 0; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f2f2; color: #000; padding: 10px; border: 1px solid #000; text-align: left; }
        td { padding: 8px; border: 1px solid #000; color: #000; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; color: #999; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMBAYARAN PAJAK, STNK & KIR</h1>
        <p>PEDAMI INVENTARIS - Sistem Manajemen Aset</p>
        @if(isset($start_date) && isset($end_date))
            <p style="font-weight: bold;">Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
        @endif
        @if(isset($kategori))
            <p style="font-weight: bold;">Kategori: {{ $kategori === 'all' ? 'Semua Kategori' : $kategori }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <p>Oleh: {{ auth()->user()->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kendaraan (Plat)</th>
                <th>Jenis Pembayaran</th>
                <th>Tanggal Bayar</th>
                <th>Jatuh Tempo Berikutnya</th>
                <th class="text-right">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($records as $index => $record)
                @php $total += $record->biaya; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->dataR2r4->nm_brg }} ({{ $record->dataR2r4->plat }})</td>
                    <td>{{ $record->jenis_pembayaran }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->tanggal_pembayaran)->format('d/m/Y') }}</td>
                    <td>{{ $record->jatuh_tempo_berikutnya ? \Carbon\Carbon::parse($record->jatuh_tempo_berikutnya)->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">{{ number_format($record->biaya, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Halaman 1 dari 1 | PEDAMI INVENTARIS
    </div>
</body>
</html>
