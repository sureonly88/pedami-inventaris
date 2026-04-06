<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatServiceAcResource\Pages;
use App\Models\RiwayatServiceAc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RiwayatServiceAcResource extends Resource
{
    protected static ?string $model = RiwayatServiceAc::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Riwayat Service Aset';
    protected static ?string $modelLabel = 'Riwayat Service Aset';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Aset')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->relationship('asset', 'nama_asset')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->kode_asset} - {$record->nama_asset}")
                            ->searchable()
                            ->preload()
                            ->label('Pilih Aset')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($set) => $set('asset_details', null)),

                        Forms\Components\Placeholder::make('asset_info')
                            ->label('Detail Informasi Aset')
                            ->visible(fn($get) => $get('asset_id'))
                            ->content(function ($get) {
                                $assetId = $get('asset_id');
                                if (!$assetId)
                                    return null;

                                $asset = \App\Models\Asset::find($assetId);
                                if (!$asset)
                                    return 'Aset tidak ditemukan.';

                                return new \Illuminate\Support\HtmlString("
                                    <div class='grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700'>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Kode Aset</p>
                                            <p class='font-bold text-primary-600'>{$asset->kode_asset}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Kondisi Saat Ini</p>
                                            <p class='font-semibold'>" . ($asset->status_barang ?? '-') . "</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Lokasi / Ruangan</p>
                                            <p class='font-semibold'>" . ($asset->ruangan?->ruangan ?? '-') . "</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Penanggung Jawab</p>
                                            <p class='font-semibold'>" . ($asset->penanggung_jawab?->nama_karyawan ?? '-') . "</p>
                                        </div>
                                    </div>
                                ");
                            }),
                    ]),

                Forms\Components\Section::make('Detail Pemeliharaan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_service')
                            ->label('Tanggal Servis')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('jenis_pekerjaan')
                            ->label('Jenis Pekerjaan')
                            ->placeholder('Contoh: Cuci AC, Tambah Freon, Ganti Sparepart')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('biaya')
                            ->label('Total Biaya')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('teknisi')
                            ->label('Nama Teknisi / Vendor')
                            ->placeholder('Contoh: CV Maju Jaya, Pak Budi')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('bukti_foto')
                            ->label('Foto Bukti / Nota')
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
                Tables\Columns\TextColumn::make('asset.nama_asset')
                    ->label('Nama Aset')
                    ->description(fn($record) => $record->asset?->kode_asset)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_service')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_pekerjaan')
                    ->label('Pekerjaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('biaya')
                    ->label('Biaya')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('teknisi')
                    ->label('Teknisi')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('bukti_foto')
                    ->label('Bukti')
                    ->disk('minio')
                    ->url(fn($record) => $record->bukti_foto ? \Illuminate\Support\Facades\Storage::disk('minio')->url($record->bukti_foto) : null, true)
                    ->circular(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('tidak ada riwayat service')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatServiceAcs::route('/'),
            'create' => Pages\CreateRiwayatServiceAc::route('/create'),
            'edit' => Pages\EditRiwayatServiceAc::route('/{record}/edit'),
        ];
    }
}
