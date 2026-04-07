<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected static ?string $title = 'Inventaris Aset';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\AssetImporter::class)
                ->label('Import Aset Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray'),
            \Filament\Actions\Action::make('cetak_laporan')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->modalHeading('Cetak Laporan Inventaris Aset')
                ->modalSubmitActionLabel('Unduh Laporan')
                ->modalWidth(\Filament\Support\Enums\MaxWidth::TwoExtraLarge)
                ->form([
                    \Filament\Forms\Components\Grid::make(3)->schema([
                        \Filament\Forms\Components\Select::make('kelompok_asset')
                            ->label('Kelompok Aset')
                            ->options([
                                'kantor' => 'Perabotan Kantor',
                                'komputer' => 'Peralatan Komputer',
                            ])
                            ->placeholder('Semua Kelompok'),
                        \Filament\Forms\Components\Select::make('ruangan_id')
                            ->label('Ruangan')
                            ->options(\App\Models\Ruangan::all()->pluck('ruangan', 'id'))
                            ->searchable()
                            ->placeholder('Semua Ruangan'),
                        \Filament\Forms\Components\Select::make('status_barang')
                            ->label('Status Barang')
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak Ringan' => 'Rusak Ringan',
                                'Disposal' => 'Disposal',
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
                        'kelompok_asset' => $data['kelompok_asset'],
                        'ruangan_id' => $data['ruangan_id'],
                        'status_barang' => $data['status_barang'],
                    ];

                    if ($data['format'] === 'excel') {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\AssetReportExport($filters),
                            'Laporan_Inventaris_Aset_' . date('Y-m-d') . '.xlsx'
                        );
                    } else {
                        $records = \App\Models\Asset::with(['ruangan', 'penanggung_jawab', 'karyawan'])
                            ->when($filters['kelompok_asset'], fn ($q, $kelompok) => $q->where('kelompok_asset', $kelompok))
                            ->when($filters['ruangan_id'], fn ($q, $ruangan) => $q->where('ruangan_id', $ruangan))
                            ->when($filters['status_barang'], fn ($q, $status) => $q->where('status_barang', $status))
                            ->get();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setPaper('a4', 'landscape')->loadHtml(
                            \Illuminate\Support\Facades\Blade::render('filament.reports.asset-report', [
                                'records' => $records,
                            ])
                        );

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'Laporan_Inventaris_Aset_' . date('Y-m-d') . '.pdf');
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
