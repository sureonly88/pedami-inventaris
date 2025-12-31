<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermohonanDisposalResource\Pages;
use App\Filament\Resources\PermohonanDisposalResource\RelationManagers;
use App\Models\PermohonanDisposal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Asset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class PermohonanDisposalResource extends Resource
{
    protected static ?string $model = PermohonanDisposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Disposal Aset';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor')
                    ->label('Nomor Surat')
                    ->required()
                    ->maxLength(3) // hanya XXX
                    ->reactive()
                    ->helperText('Isi hanya kode awal (contoh: 001)')
                    ->dehydrateStateUsing(function ($state) {
                        if (!$state) {
                            return null;
                        }

                        $bulanRomawi = [
                            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
                            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
                            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
                        ];

                        $bulan = $bulanRomawi[now()->month];
                        $tahun = now()->year;

                        return strtoupper($state) . ".20/KK-PEDAMI/{$bulan}/{$tahun}";
                    })
                    ->formatStateUsing(function ($state) {
                        // agar saat edit data lama tetap tampil XXX saja
                        if (!$state) {
                            return null;
                        }

                    return Str::before($state, '.');
                }),
                Select::make('asset_id')
                    ->label('Aset')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->options(fn () =>
                        Asset::query()
                            ->get()
                            ->mapWithKeys(fn ($asset) => [
                                $asset->id => $asset->kode_asset . ' - ' . $asset->nama_asset,
                            ])
                    )
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set) {
                        if (!$state) {
                            return;
                        }

                        $asset = Asset::find($state);

                        if ($asset) {
                            $set('nama_asset', $asset->nama_asset);
                            $set('hrg_beli', $asset->hrg_beli);

                            $set('gambar_asset',
                                $asset->gambar ? [$asset->gambar] : []
                            );
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        $asset = Asset::find($state);

                        if ($asset) {
                            $set('nama_asset', $asset->nama_asset);
                            $set('hrg_beli', $asset->hrg_beli);

                            //dd($asset->gambar);
                            if(!is_null($asset->gambar)){
                                $set('gambar_asset', [$asset->gambar]);
                            }else{
                                $set('gambar_asset', []);
                            }
                            
                        }
                    }),
                Section::make('Informasi Aset')
                    ->schema([
                        TextInput::make('nama_asset')
                            ->label('Nama Aset')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('hrg_beli')
                            ->label('Harga Beli')
                            ->disabled()
                            ->formatStateUsing(fn ($state) =>
                                $state ? 'Rp ' . number_format($state, 0, ',', '.') : null
                            )
                            ->dehydrated(false),

                        FileUpload::make('gambar_asset')
                            ->label('Gambar Aset')
                            ->image()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->visible(fn ($get) => filled($get('asset_id'))),

                Forms\Components\DatePicker::make('tgl_pengajuan')
                    ->required(),

                FileUpload::make('gambar')
                    ->image()
                    ->imageEditor()
                    ->downloadable(),
                
                Forms\Components\Select::make('kondisi')
                    //->disabled(fn () => Auth::user()->role !== 'admin')
                    //->dehydrated(fn () => Auth::user()->role === 'admin')
                    ->options([
                        'Rusak Sebagian' => 'Rusak Sebagian',
                        'Rusak Total' => 'Rusak Total',
                    ])->required(),
                
                Forms\Components\TextInput::make('keterangan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')
                    ->sortable(),               
                Tables\Columns\TextColumn::make('asset.nama_asset')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_pengajuan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dibuatOleh.nama_karyawan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verif_manager')
                    ->label('Verifikasi Manager')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return ($state == 1)
                            ? 'Sudah Verifikasi'
                            : 'Belum Verifikasi';
                    })
                    ->color(function ($state) {
                        return ($state == 1) ? 'success' : 'danger';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_verif_manager'),
                Tables\Columns\TextColumn::make('verif_ketua')
                    ->label('Verifikasi Ketua')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return ($state == 1)
                            ? 'Sudah Verifikasi'
                            : 'Belum Verifikasi';
                    })
                    ->color(function ($state) {
                        return ($state == 1) ? 'success' : 'danger';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_verif_ketua'),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn ($record) =>
                    auth()->user()->role === 'admin'
                    && $record->verif_manager == 0
                    && $record->verif_ketua == 0
                ),
                Tables\Actions\Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->url(fn ($record) => static::getUrl('verify', ['record' => $record])),
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn ($record) => route('permohonan-disposal.cetak', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) =>
                        $record->verif_manager == 1 &&
                        $record->verif_ketua == 1
                    ),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPermohonanDisposals::route('/'),
            'create' => Pages\CreatePermohonanDisposal::route('/create'),
            'verify' => Pages\VerifyPermohonanDisposal::route('/{record}/verify'),
            'edit' => Pages\EditPermohonanDisposal::route('/{record}/edit'),
        ];
    }
}
