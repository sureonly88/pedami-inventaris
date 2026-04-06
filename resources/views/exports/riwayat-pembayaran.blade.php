<table>
    <thead>
    <tr>
        <th colspan="6" style="text-align: center; font-weight: bold; font-size: 14pt;">LAPORAN PEMBAYARAN PAJAK, STNK & KIR</th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: center;">PEDAMI INVENTARIS - Sistem Manajemen Aset</th>
    </tr>
    @if(isset($start_date) && isset($end_date))
    <tr>
        <th colspan="6" style="text-align: center; font-weight: bold;">Periode: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</th>
    </tr>
    @endif
    @if(isset($kategori))
    <tr>
        <th colspan="6" style="text-align: center; font-weight: bold;">Kategori: {{ $kategori === 'all' ? 'Semua Kategori' : $kategori }}</th>
    </tr>
    @endif
    <tr>
        <th colspan="6" style="text-align: center;">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</th>
    </tr>
    <tr>
        <th colspan="6" style="text-align: center;">Oleh: {{ auth()->user()->name }}</th>
    </tr>
    <tr></tr>
    <tr>
        <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; color: #000000;">No</th>
        <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; color: #000000;">Kendaraan (Plat)</th>
        <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; color: #000000;">Jenis Pembayaran</th>
        <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; color: #000000;">Tanggal Bayar</th>
        <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; color: #000000;">Jatuh Tempo Berikutnya</th>
        <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; color: #000000;">Biaya (Rp)</th>
    </tr>
    </thead>
    <tbody>
    @php $total = 0; @endphp
    @foreach($records as $index => $record)
        @php $total += $record->biaya; @endphp
        <tr>
            <td style="border: 1px solid #000000; color: #000000;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000; color: #000000;">{{ $record->dataR2r4->nm_brg }} ({{ $record->dataR2r4->plat }})</td>
            <td style="border: 1px solid #000000; color: #000000;">{{ $record->jenis_pembayaran }}</td>
            <td style="border: 1px solid #000000; color: #000000;">{{ \Carbon\Carbon::parse($record->tanggal_pembayaran)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000000; color: #000000;">{{ $record->jatuh_tempo_berikutnya ? \Carbon\Carbon::parse($record->jatuh_tempo_berikutnya)->format('d/m/Y') : '-' }}</td>
            <td style="border: 1px solid #000000; text-align: right; color: #000000;">{{ $record->biaya }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5" style="border: 1px solid #000000; text-align: right; font-weight: bold; color: #000000;">TOTAL KESELURUHAN</td>
        <td style="border: 1px solid #000000; text-align: right; font-weight: bold; color: #000000;">{{ $total }}</td>
    </tr>
    </tfoot>
</table>
