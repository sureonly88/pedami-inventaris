<?php

namespace App\Filament\Resources\RiwayatServisR2r4Resource\Pages;

use App\Filament\Resources\RiwayatServisR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Exports\RiwayatServisR2r4Export;
use App\Models\RiwayatServisR2r4;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;

class ListRiwayatServisR2r4s extends ListRecords
{
    protected static string $resource = RiwayatServisR2r4Resource::class;

    protected static ?string $title = 'Riwayat Service Kendaraan';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Laporan Riwayat Service Kendaraan')
                ->modalSubmitActionLabel('Unduh Laporan')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('filter_type')
                            ->label('Tipe Filter')
                            ->options([
                                'range' => 'Rentang Tanggal',
                                'monthly' => 'Bulan & Tahun',
                            ])
                            ->default('range')
                            ->reactive()
                            ->required(),
                        Select::make('format')
                            ->label('Format Laporan')
                            ->options([
                                'excel' => 'Excel (.xlsx)',
                                'pdf' => 'PDF (.pdf)',
                            ])
                            ->default('pdf')
                            ->required(),
                    ]),
                    Section::make('Rentang Tanggal')
                        ->visible(fn($get) => $get('filter_type') === 'range')
                        ->schema([
                            Grid::make(2)->schema([
                                DatePicker::make('from')->label('Dari Tanggal'),
                                DatePicker::make('until')->label('Sampai Tanggal'),
                            ]),
                        ]),
                    Section::make('Bulan & Tahun')
                        ->visible(fn($get) => $get('filter_type') === 'monthly')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('month')
                                    ->label('Bulan')
                                    ->options([
                                        '01' => 'Januari',
                                        '02' => 'Februari',
                                        '03' => 'Maret',
                                        '04' => 'April',
                                        '05' => 'Mei',
                                        '06' => 'Juni',
                                        '07' => 'Juli',
                                        '08' => 'Agustus',
                                        '09' => 'September',
                                        '10' => 'Oktober',
                                        '11' => 'November',
                                        '12' => 'Desember',
                                    ]),
                                Select::make('year')
                                    ->label('Tahun')
                                    ->options(array_combine(range(date('Y'), 2020), range(date('Y'), 2020)))
                                    ->default(date('Y')),
                            ]),
                        ]),
                ])
                ->action(function (array $data) {
                    $filters = [];
                    $period = 'Semua Periode';

                    if ($data['filter_type'] === 'range') {
                        $filters['from'] = $data['from'];
                        $filters['until'] = $data['until'];
                        if ($data['from'] && $data['until']) {
                            $period = date('d/m/Y', strtotime($data['from'])) . ' - ' . date('d/m/Y', strtotime($data['until']));
                        }
                    } else {
                        $filters['month'] = $data['month'];
                        $filters['year'] = $data['year'];
                        if ($data['month'] && $data['year']) {
                            $period = Carbon::create(null, $data['month'])->translatedFormat('F') . ' ' . $data['year'];
                        }
                    }

                    if ($data['format'] === 'excel') {
                        return Excel::download(
                            new RiwayatServisR2r4Export($filters, $period),
                            'Laporan_Service_Kendaraan_' . str_replace(' ', '_', $period) . '.xlsx'
                        );
                    } else {
                        $records = RiwayatServisR2r4::with('dataR2r4')
                            ->when($filters['from'] ?? null, fn($q, $from) => $q->whereDate('tanggal_servis', '>=', $from))
                            ->when($filters['until'] ?? null, fn($q, $until) => $q->whereDate('tanggal_servis', '<=', $until))
                            ->when($filters['month'] ?? null, fn($q, $month) => $q->whereMonth('tanggal_servis', $month))
                            ->when($filters['year'] ?? null, fn($q, $year) => $q->whereYear('tanggal_servis', $year))
                            ->get();

                        $pdf = Pdf::setPaper('a4', 'landscape')->loadHtml(
                            Blade::render('filament.reports.service-r2r4-report', [
                                'records' => $records,
                                'period' => $period
                            ])
                        );

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'Laporan_Service_Kendaraan_' . str_replace(' ', '_', $period) . '.pdf');
                    }
                }),
        ];
    }
}
