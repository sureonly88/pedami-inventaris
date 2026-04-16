<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Exports\KaryawanExport;
use App\Filament\Resources\KaryawanResource;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Subdivisi;
use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected static ?string $title = 'Karyawan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\KaryawanImporter::class)
                ->label('Import Karyawan Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray'),
            Actions\Action::make('export_excel')
                ->label('Export Karyawan Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->modalHeading('Export Data Karyawan')
                ->modalSubmitActionLabel('Unduh Excel')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('status_karyawan')
                            ->label('Status Karyawan')
                            ->options([
                                'all' => 'Semua Status',
                                'Aktif' => 'Aktif',
                                'Pengurus' => 'Pengurus',
                                'Pensiun' => 'Pensiun',
                                'Nonaktif' => 'Nonaktif',
                            ])
                            ->default('all')
                            ->required(),
                        Select::make('divisi_id')
                            ->label('Divisi')
                            ->options(Divisi::query()->pluck('nama_divisi', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('subdivisi_id', null))
                            ->placeholder('Semua Divisi'),
                        Select::make('subdivisi_id')
                            ->label('Subdivisi')
                            ->options(function (Get $get) {
                                $divisiId = $get('divisi_id');

                                return Subdivisi::query()
                                    ->when($divisiId, fn ($query) => $query->where('divisi_id', $divisiId))
                                    ->pluck('nama_sub', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Semua Subdivisi'),
                    ]),
                    CheckboxList::make('fields')
                        ->label('Field yang Ditampilkan')
                        ->options(KaryawanExport::getFieldOptions())
                        ->columns(3)
                        ->bulkToggleable()
                        ->searchable()
                        ->default(array_keys(KaryawanExport::getFieldOptions()))
                        ->required()
                        ->minItems(1),
                ])
                ->action(function (array $data) {
                    $query = Karyawan::query()
                        ->when(($data['status_karyawan'] ?? 'all') !== 'all', fn ($query) => $query->where('status_karyawan', $data['status_karyawan']))
                        ->when($data['divisi_id'] ?? null, fn ($query, $divisiId) => $query->whereHas('subdivisi', fn ($subQuery) => $subQuery->where('divisi_id', $divisiId)))
                        ->when($data['subdivisi_id'] ?? null, fn ($query, $subdivisiId) => $query->where('subdivisi_id', $subdivisiId));

                    $summaryParts = [];

                    $summaryParts[] = $data['status_karyawan'] === 'all'
                        ? 'Semua Status'
                        : 'Status: ' . $data['status_karyawan'];

                    if (! empty($data['divisi_id'])) {
                        $divisi = Divisi::query()->find($data['divisi_id']);

                        if ($divisi) {
                            $summaryParts[] = 'Divisi: ' . $divisi->nama_divisi;
                        }
                    }

                    if (! empty($data['subdivisi_id'])) {
                        $subdivisi = Subdivisi::query()->find($data['subdivisi_id']);

                        if ($subdivisi) {
                            $summaryParts[] = 'Subdivisi: ' . $subdivisi->nama_sub;
                        }
                    }

                    $selectedFields = $data['fields'] ?? array_keys(KaryawanExport::getFieldOptions());

                    return Excel::download(
                        new KaryawanExport(
                            query: $query,
                            title: 'DATA KARYAWAN',
                            subtitle: implode(' | ', array_merge($summaryParts, ['Tanggal Export: ' . now()->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y H:i')])),
                            selectedFields: $selectedFields,
                        ),
                        'Data_Karyawan_' . ($data['status_karyawan'] === 'all' ? 'Semua' : $data['status_karyawan']) . '_' . now()->format('Y-m-d_His') . '.xlsx'
                    );
                }),
            Actions\CreateAction::make(),
        ];
    }
}
