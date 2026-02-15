<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use App\Models\Ruangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use App\Filament\Exports\AssetExporter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Inventaris Aset';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_asset')
                    ->required()
                    ->readOnly(true)
                    ->default(function (?Asset $Asset): string {
                        $last_sub = $Asset::orderBy('kode_asset', 'desc')->first();
                        $next_num = 1;
                        if ($last_sub) {
                            $next_num = (int) substr($last_sub->kode_asset, 2, 3) + 1;
                        }

                        $next_sub = 'KA' . str_repeat('0', 3 - strlen($next_num)) . $next_num;
                        return $next_sub;
                    })
                    ->label('Kode Aset')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_asset')
                    ->required()
                    ->label('Nama Aset')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tgl_beli')
                    ->native(false)
                    ->label('Tanggal Pembelian'),
                Forms\Components\TextInput::make('hrg_beli')
                    ->prefix('Rp. ')
                    ->label('Harga Pembelian')
                    ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $angka = preg_replace('/[^0-9]/', '', $state);

                            if ($angka !== '') {
                            $set('hrg_beli', number_format($angka, 0, ',', '.'));
                        }
                        })
                        ->dehydrateStateUsing(function ($state) {
                            // Jika kosong / null → simpan 0
                            if (blank($state)) {
                                return 0;
                            }

                            // Jika ada nilai → simpan angka murni
                            return (int) str_replace('.', '', $state);
                        }),
                FileUpload::make('gambar')
                    ->image()
                    ->imageEditor()
                    ->disk('minio')
                    ->visibility('public')
                    ->getUploadedFileUrlUsing(fn ($state, $record) => Storage::disk('minio')->temporaryUrl($state, now()->addMinutes(10)));
                    ->label('Gambar')
                    ->downloadable(),
                Forms\Components\Select::make('kelompok_asset')
                    ->label('Kelompok Aset')
                    ->options([
                        'kantor' => 'Perabotan Kantor',
                        'komputer' => 'Peralatan Komputer',
                    ])->required(),

                Forms\Components\Select::make('ruangan_id')
                    ->relationship(name: 'ruangan', titleAttribute: 'ruangan')
                    //->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->getOptionLabelFromRecordUsing(fn(Ruangan $record) => "{$record->ruangan} - {$record->lokasi}")
                    ->label('Ruang/Lokasi'),

                Forms\Components\Select::make('penanggung_jawab_id')
                    ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                    ->searchable()
                    //->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->label('Penanggung_jawab'),
                //Forms\Components\TextInput::make('penanggung_jawab')
                //->required()
                //->maxLength(255),

                Forms\Components\Select::make('karyawan_id')
                    ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                    //->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->searchable()
                    ->label('Pemakai'),

                Forms\Components\Select::make('status_barang')
                    ->disabled(condition: fn (?Asset $record) => $record?->status_barang === 'Disposal')
                    //->dehydrated(fn () => Auth::user()->role === 'admin')
                    ->options(function (?Asset $record) {
                        $options = [
                            'Baik' => 'Baik',
                            'Rusak Ringan' => 'Rusak Ringan',
                        ];

                        // Jika sedang VIEW / EDIT data lama
                        // dan status-nya sudah Disposal, tetap tampilkan
                        if ($record?->status_barang === 'Disposal') {
                            $options['Disposal'] = 'Disposal';
                        }

                        return $options;
                    })
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->maxLength(255)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_asset')->searchable(),
                Tables\Columns\TextColumn::make('nama_asset')->searchable(),
                Tables\Columns\TextColumn::make('tgl_beli')->label('Tanggal Beli'),
                Tables\Columns\TextColumn::make('hrg_beli')
                ->label('Harga Beli')
                ->prefix('Rp. ')    
                ->numeric(decimalPlaces: 0),
                //Tables\Columns\TextColumn::make('gambar'),
                Tables\Columns\TextColumn::make('kelompok_asset'),
                Tables\Columns\TextColumn::make('ruangan.ruangan'),
                Tables\Columns\TextColumn::make('ruangan.lokasi')->label('Lokasi'),
                Tables\Columns\TextColumn::make('penanggung_jawab.nama_karyawan')->label('Penanggung Jawab')->searchable(),
                Tables\Columns\TextColumn::make('karyawan.nama_karyawan')->label('Pemakai')->searchable(),
                //Tables\Columns\TextColumn::make('karyawan.subdivisi.divisi.nama_divisi')->searchable(),
                Tables\Columns\TextColumn::make('penanggung_jawab.subdivisi.divisi.nama_divisi')->searchable(),
                Tables\Columns\TextColumn::make('status_barang')->searchable(),
                //Tables\Columns\TextColumn::make('deskripsi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelompok_asset')
                    ->options([
                        'kantor' => 'Perabotan Kantor',
                        'komputer' => 'Peralatan Komputer',
                        'kendaraan' => 'Kendaraan',
                    ])
            ])
            ->headerActions([
                // Tables\Actions\Action::make('Cetak Barcode')
                //  ->action(function () {
                // redirect('/admin/assets/cetak');
                //}),

                ExportAction::make(),

                Tables\Actions\Action::make('pdf')
                    ->label('Download')
                    ->accessSelectedRecords()
                    ->action(function (Collection $selectedRecords) {

                        $Assets = $selectedRecords->map(function (Asset $record) {
                            return $record;
                        });

                        return response()->streamDownload(function () use ($Assets) {
                            echo Pdf::loadHtml(
                                Blade::render('filament.modals.barcode-pdf', ['records' => $Assets])
                            )->stream();
                        }, 'Barcode.pdf');
                        // return response()->streamDownload(function () use ($selectedRecords) {
            

                        //     echo Pdf::loadHtml(
                        //         Blade::render('filament.modals.barcode-pdf', ['record' => $record])
                        //     )->stream();
                        // }, 'Barcode-' . $record->kode_asset . '.pdf');
                    }),
                // Tables\Actions\ExportAction::make('Export')
                //     ->exporter(AssetExporter::class)
                //     ->fileDisk('local')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('Barcode')
                    ->action(fn(Asset $record) => $record->advance())
                    ->modalContent(fn(Asset $record): View => view('filament.modals.barcode-modal', ['record' => $record], ))
                    ->icon('heroicon-s-squares-2x2')
                    ->modalSubmitAction(false)
                    ->modalWidth(MaxWidth::Medium),



            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
            'cetak' => Pages\CetakBarcodeAsset::route('/cetak'),

        ];
    }
}
