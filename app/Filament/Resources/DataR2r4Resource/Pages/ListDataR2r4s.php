<?php

namespace App\Filament\Resources\DataR2r4Resource\Pages;

use App\Filament\Resources\DataR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataR2r4s extends ListRecords
{
    protected static string $resource = DataR2r4Resource::class;

    protected static ?string $title = 'Data Roda 2 & Roda 4';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->modalHeading('Cetak Laporan Pendataan R2 & R4')
                ->modalSubmitActionLabel('Unduh Laporan')
                ->modalWidth(\Filament\Support\Enums\MaxWidth::TwoExtraLarge)
                ->form([
                    \Filament\Forms\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\Select::make('jns_brg')
                            ->label('Jenis Kendaraan')
                            ->options([
                                'R2 Operasional' => 'R2 Operasional',
                                'R4 Operasional' => 'R4 Operasional',
                                'R2 Dinas' => 'R2 Dinas',
                                'R4 Dinas' => 'R4 Dinas',
                            ])
                            ->placeholder('Semua Jenis'),
                        \Filament\Forms\Components\Select::make('stat')
                            ->label('Status')
                            ->options([
                                'Habis Kontrak' => 'Habis Kontrak',
                                'Di pakai - Tidak ada Kontrak' => 'Di pakai - Tidak ada Kontrak',
                                'Sewa - Kontrak Berjalan' => 'Sewa - Kontrak Berjalan',
                                'Operasional Pedami' => 'Operasional Pedami',
                                'Terjual' => 'Terjual',
                            ])
                            ->placeholder('Semua Status'),
                    ]),
                    \Filament\Forms\Components\Select::make('format')
                        ->label('Format Laporan')
                        ->options([
                            'excel' => 'Excel (.xlsx)',
                            'pdf' => 'PDF (.pdf)',
                        ])
                        ->default('pdf')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filters = [
                        'jns_brg' => $data['jns_brg'],
                        'stat' => $data['stat'],
                    ];

                    if ($data['format'] === 'excel') {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\DataR2r4ReportExport($filters),
                            'Laporan_Pendataan_R2R4_' . date('Y-m-d') . '.xlsx'
                        );
                    } else {
                        $records = \App\Models\data_r2r4::query()
                            ->when($filters['jns_brg'], fn ($q, $jenis) => $q->where('jns_brg', $jenis))
                            ->when($filters['stat'], fn ($q, $status) => $q->where('stat', $status))
                            ->get();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setPaper('a4', 'landscape')->loadHtml(
                            \Illuminate\Support\Facades\Blade::render('filament.reports.r2r4-report', [
                                'records' => $records,
                            ])
                        );

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'Laporan_Pendataan_R2R4_' . date('Y-m-d') . '.pdf');
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
