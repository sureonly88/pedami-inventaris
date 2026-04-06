<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPembayaranR2r4Resource\Pages;
use App\Models\RiwayatPembayaranR2r4;
use App\Models\data_r2r4;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class RiwayatPembayaranR2r4Resource extends Resource
{
    protected static ?string $model = RiwayatPembayaranR2r4::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Pajak / STNK / KIR';
    protected static ?string $modelLabel = 'Riwayat Pembayaran';
    protected static ?string $pluralModelLabel = 'Riwayat Pembayaran Pajak/STNK/KIR';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kendaraan')
                    ->schema([
                        Forms\Components\Select::make('data_r2r4_id')
                            ->relationship('dataR2r4', 'plat')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->plat} - {$record->nm_brg} ({$record->departemen})")
                            ->searchable()
                            ->preload()
                            ->label('Pilih Kendaraan')
                            ->required()
                            ->live(),

                        Forms\Components\Placeholder::make('vehicle_info')
                            ->label('Detail Informasi Kendaraan')
                            ->visible(fn($get) => $get('data_r2r4_id'))
                            ->content(function ($get) {
                                $vehicleId = $get('data_r2r4_id');
                                if (!$vehicleId) return null;

                                $vehicle = data_r2r4::find($vehicleId);
                                if (!$vehicle) return 'Data kendaraan tidak ditemukan.';

                                return new \Illuminate\Support\HtmlString("
                                    <div class='grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700'>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Plat Nomor</p>
                                            <p class='font-bold text-primary-600'>{$vehicle->plat}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Nama Kendaraan</p>
                                            <p class='font-semibold'>{$vehicle->nm_brg}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Merk / Tipe</p>
                                            <p class='font-semibold'>" . ($vehicle->merk ?? '-') . "</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Pemegang / Departemen</p>
                                            <p class='font-semibold'>{$vehicle->pemegang} / {$vehicle->departemen}</p>
                                        </div>
                                    </div>
                                ");
                            }),
                    ]),

                Forms\Components\Section::make('Rincian Pembayaran')
                    ->schema([
                        Forms\Components\Select::make('jenis_pembayaran')
                            ->options([
                                'Pajak' => 'Pajak Tahunan',
                                'STNK' => 'STNK / Ganti Plat (5 Tahun)',
                                'KIR' => 'KIR (Uji Berkala)',
                            ])
                            ->required()
                            ->label('Jenis Pembayaran'),
                        Forms\Components\DatePicker::make('tanggal_pembayaran')
                            ->label('Tanggal Pembayaran')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('biaya')
                            ->label('Total Biaya')
                            ->prefix('Rp')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Forms\Components\DatePicker::make('jatuh_tempo_berikutnya')
                            ->label('Jatuh Tempo Berikutnya')
                            ->helperText('Tanggal habis masa berlaku selanjutnya'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('bukti_foto')
                            ->label('Foto Bukti / Nota Pembayaran')
                            ->disk('minio')
                            ->visibility('public')
                            ->image()
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dataR2r4.plat')
                    ->label('Plat Nomor')
                    ->description(fn($record) => $record->dataR2r4?->nm_brg)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('jenis_pembayaran')
                    ->colors([
                        'primary' => 'Pajak',
                        'success' => 'STNK',
                        'warning' => 'KIR',
                    ])
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya')
                    ->label('Biaya')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('jatuh_tempo_berikutnya')
                    ->label('Tempo Berikutnya')
                    ->date('d M Y')
                    ->color(fn($record) => $record->jatuh_tempo_berikutnya < now() ? 'danger' : 'success')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('bukti_foto')
                    ->label('Bukti')
                    ->disk('minio')
                    ->url(fn($record) => $record->bukti_foto ? \Illuminate\Support\Facades\Storage::disk('minio')->url($record->bukti_foto) : null, true)
                    ->circular(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_pembayaran')
                    ->options([
                        'Pajak' => 'Pajak',
                        'STNK' => 'STNK',
                        'KIR' => 'KIR',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->modalHeading('Export Laporan Pembayaran')
                    ->modalSubmitActionLabel('Selesai & Unduh')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Sampai')
                            ->default(now()),
                        Forms\Components\Select::make('jenis_pembayaran_filter')
                            ->label('Kategori Pembayaran')
                            ->options([
                                'all' => 'Semua Kategori',
                                'Pajak' => 'Pajak Tahunan',
                                'STNK' => 'STNK / Ganti Plat',
                                'KIR' => 'KIR',
                            ])
                            ->default('all')
                            ->required(),
                        Forms\Components\Select::make('format')
                            ->options([
                                'pdf' => 'Dokumen (PDF)',
                                'excel' => 'Spreadsheet (EXCEL)',
                            ])
                            ->default('pdf')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $query = RiwayatPembayaranR2r4::query()
                            ->whereBetween('tanggal_pembayaran', [$data['start_date'], $data['end_date']])
                            ->when($data['jenis_pembayaran_filter'] !== 'all', function ($q) use ($data) {
                                return $q->where('jenis_pembayaran', $data['jenis_pembayaran_filter']);
                            })
                            ->orderBy('tanggal_pembayaran', 'asc');

                        if ($data['format'] === 'pdf') {
                            $records = $query->get();
                            return response()->streamDownload(function () use ($records, $data) {
                                echo Pdf::loadHtml(
                                    Blade::render('filament.reports.riwayat-pembayaran', [
                                        'records' => $records,
                                        'start_date' => $data['start_date'],
                                        'end_date' => $data['end_date'],
                                        'kategori' => $data['jenis_pembayaran_filter'],
                                    ])
                                )->setPaper('a4', 'landscape')->stream();
                            }, "Laporan-Pembayaran-Range-{$data['start_date']}-to-{$data['end_date']}.pdf");
                        }

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\RiwayatPembayaranExport($query, $data['start_date'], $data['end_date'], $data['jenis_pembayaran_filter']),
                            "Laporan-Pembayaran-Range-{$data['start_date']}-to-{$data['end_date']}.xlsx"
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Export Excel (Terpilih)'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatPembayaranR2r4s::route('/'),
            'create' => Pages\CreateRiwayatPembayaranR2r4::route('/create'),
            'edit' => Pages\EditRiwayatPembayaranR2r4::route('/{record}/edit'),
        ];
    }
}
