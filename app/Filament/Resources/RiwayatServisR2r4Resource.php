<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatServisR2r4Resource\Pages;
use App\Models\RiwayatServisR2r4;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RiwayatServisR2r4Resource extends Resource
{
    protected static ?string $model = RiwayatServisR2r4::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationLabel = 'Riwayat Service Kendaraan';
    protected static ?string $modelLabel = 'Riwayat Service Kendaraan';

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

                                $vehicle = \App\Models\data_r2r4::find($vehicleId);
                                if (!$vehicle) return 'Data kendaraan tidak ditemukan.';

                                return new \Illuminate\Support\HtmlString("
                                    <div class='grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700'>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Plat Nomor / Kode</p>
                                            <p class='font-bold text-primary-600'>{$vehicle->plat} / {$vehicle->kode_brg}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Nama Kendaraan</p>
                                            <p class='font-semibold'>{$vehicle->nm_brg}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Merk / Tipe / Tahun</p>
                                            <p class='font-semibold'>" . ($vehicle->merk ?? '-') . " / " . ($vehicle->thn ?? '-') . "</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Pemegang / Departemen</p>
                                            <p class='font-semibold'>{$vehicle->pemegang} / {$vehicle->departemen}</p>
                                        </div>
                                    </div>
                                ");
                            }),
                    ]),

                Forms\Components\Section::make('Detail Servis')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_servis')
                            ->label('Tanggal Servis')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('jenis_servis')
                            ->label('Jenis Pekerjaan/Servis')
                            ->placeholder('Contoh: Ganti Oli, Perbaikan AC, Ban Baru')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('biaya')
                            ->label('Total Biaya')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('bengkel')
                            ->label('Nama Bengkel/Toko')
                            ->placeholder('Contoh: Auto2000, Bengkel Mandiri')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('struk_foto')
                            ->disk('minio')
                            ->visibility('public')
                            ->label('Foto Nota/Struk (Opsional)')
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dataR2r4.plat')
                    ->label('Plat / Kendaraan')
                    ->description(fn($record) => $record->dataR2r4?->nm_brg ?? 'Tidak ada data')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_servis')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_servis')
                    ->label('Pekerjaan')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('biaya')
                    ->label('Total Biaya')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('bengkel')
                    ->label('Bengkel')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('struk_foto')
                    ->label('Struk')
                    ->disk('minio')
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
            'index' => Pages\ListRiwayatServisR2r4s::route('/'),
            'create' => Pages\CreateRiwayatServisR2r4::route('/create'),
            'edit' => Pages\EditRiwayatServisR2r4::route('/{record}/edit'),
        ];
    }
}
