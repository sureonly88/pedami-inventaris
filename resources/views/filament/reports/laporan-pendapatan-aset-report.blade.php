<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan Aset</title>
    <style>
        @page { margin: 18px 16px; }
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 14pt; color: #000; }
        .header h2 { margin: 4px 0; font-size: 12pt; color: #000; }
        .header p { margin: 0; font-size: 10pt; }
        .section-title { margin-top: 18px; margin-bottom: 8px; font-size: 11pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px 3px; word-wrap: break-word; }
        th { background-color: #E5E7EB; color: #000; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: -20px; width: 100%; text-align: right; font-size: 7pt; color: #777; }
        ul { margin: 6px 0 0 16px; padding: 0; }
        li { margin-bottom: 4px; }
        .graph-cell { text-align: center; }
        .sparkline { display: inline-block; width: 110px; height: 28px; }
        .table-small th,
        .table-small td { font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENDAPATAN ASET</h1>
        <h2>KOPERASI KONSUMEN PEDAMI</h2>
        <p>Periode: {{ $period }}</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now('Asia/Makassar')->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="section-title">Total Pendapatan Aset</div>
    <table class="table-small">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th>Jenis Pendapatan</th>
                @foreach ($monthLabels as $monthLabel)
                    <th>{{ strtoupper($monthLabel) }}</th>
                @endforeach
                <th style="width: 12%;">TOTAL</th>
                <th style="width: 14%;">GRAFIK</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incomeRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['label'] }}</td>
                    @foreach ($row['months'] as $value)
                        <td class="text-right">{{ $value ? number_format($value, 0, ',', '.') : '-' }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    <td class="graph-cell">
                        @if ($incomeSparklineBuilder($row['months']))
                            <img class="sparkline" src="{{ $incomeSparklineBuilder($row['months']) }}" alt="Grafik {{ $row['label'] }}">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($monthLabels) + 4 }}" class="text-center">Tidak ada data pendapatan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL PENDAPATAN</th>
                @foreach (array_keys($monthLabels) as $month)
                    <th class="text-right">{{ number_format($incomeTotalsByMonth[$month] ?? 0, 0, ',', '.') }}</th>
                @endforeach
                <th class="text-right">{{ number_format($incomeGrandTotal, 0, ',', '.') }}</th>
                <th class="graph-cell">
                    @if ($incomeTotalSparkline)
                        <img class="sparkline" src="{{ $incomeTotalSparkline }}" alt="Grafik total pendapatan">
                    @else
                        -
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Jumlah Unit Aktif Tagihan</div>
    <table class="table-small">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th>Jenis Pendapatan</th>
                @foreach ($monthLabels as $monthLabel)
                    <th>{{ strtoupper($monthLabel) }}</th>
                @endforeach
                <th style="width: 12%;">TOTAL</th>
                <th style="width: 14%;">GRAFIK</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($unitRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['label'] }}</td>
                    @foreach ($row['months'] as $value)
                        <td class="text-center">{{ $value ?: '-' }}</td>
                    @endforeach
                    <td class="text-center">{{ $row['total'] }}</td>
                    <td class="graph-cell">
                        @if ($unitSparklineBuilder($row['months']))
                            <img class="sparkline" src="{{ $unitSparklineBuilder($row['months']) }}" alt="Grafik {{ $row['label'] }}">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($monthLabels) + 4 }}" class="text-center">Tidak ada data unit.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL JUMLAH UNIT</th>
                @foreach (array_keys($monthLabels) as $month)
                    <th class="text-center">{{ $unitTotalsByMonth[$month] ?? 0 }}</th>
                @endforeach
                <th class="text-center">{{ $unitGrandTotal }}</th>
                <th class="graph-cell">
                    @if ($unitTotalSparkline)
                        <img class="sparkline" src="{{ $unitTotalSparkline }}" alt="Grafik total unit">
                    @else
                        -
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Catatan Tren</div>
    <table>
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Roda Dua (R2)</strong>
                    <ul>
                        @foreach ($roda2Notes as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Roda Empat (R4)</strong>
                    <ul>
                        @foreach ($roda4Notes as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now('Asia/Makassar')->format('d-m-Y H:i:s') }}<br>
        Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>