<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use App\Filament\Exports\AssetExporter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\Collection;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Inventaris Asset';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_asset')
                ->required()
                ->readOnly(true)
                ->default(function (?Asset $Asset): String {
                    $last_sub = $Asset::orderBy('kode_asset','desc')->first();
                    $next_num = 1;
                    if ($last_sub) {
                        $next_num = (int)substr($last_sub->kode_asset, 2, 3) + 1;
                    }
                    
                    $next_sub = 'KA'. str_repeat('0', 3 - strlen($next_num)) . $next_num;
                    return $next_sub;
                })
                ->maxLength(255),
              
                Forms\Components\TextInput::make('nama_asset')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('kelompok_asset')
                    ->options([
                        'kantor' => 'Perabotan Kantor',
                        'komputer' => 'Peralatan Komputer',
                        'Kendaraan Operasional'=> 'Kendaraan Operasional',
                    ])->required(),
                Forms\Components\Select::make('lokasi')
                    ->options([
                        'Kantor A. Yani' => 'Kantor A. Yani',
                        'Loket Kasir A. Yani' => 'Loket Kasir A. Yani',
                        'Loket Kasir S. Parman' => 'Loket Kasir S. Parman',
                        'Loket Kasir Beruntung' => 'Loket Kasir Beruntung',
                        'Loket Kasir Sutoyo' => 'Loket Kasir Sutoyo',
                        'Loket Kasir Cemara' => 'Loket Kasir Cemara',
                    ])->required(),

                Forms\Components\Select::make('penanggung_jawab_id')
                    ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                    ->searchable()
                    ->label('Penanggung_jawab'),
                //Forms\Components\TextInput::make('penanggung_jawab')
                    //->required()
                    //->maxLength(255),

                Forms\Components\Select::make('karyawan_id')
                    ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                    ->searchable()
                    ->label('Pemakai'),

                //Forms\Components\TextInput::make('divisi')
                // ->required()
                //->maxLength(255),
                Forms\Components\Select::make('status_barang')
                    ->options([
                        'Dipakai' => 'Dipakai',
                        'Rusak' => 'Rusak',
                        'Dijual' => 'Dijual',
                    ])->required()
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_asset'),
                Tables\Columns\TextColumn::make('nama_asset')->searchable(),
                Tables\Columns\TextColumn::make('kelompok_asset'),
                Tables\Columns\TextColumn::make('lokasi'),
                Tables\Columns\TextColumn::make('penanggung_jawab.nama_karyawan')->label('Penanggung_jawab'),
                Tables\Columns\TextColumn::make('karyawan.nama_karyawan')->label('Pemakai'),
                Tables\Columns\TextColumn::make('karyawan.subdivisi.divisi.nama_divisi')->searchable(),
                Tables\Columns\TextColumn::make('status_barang'),
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
                Tables\Actions\Action::make('Cetak Barcode')
                    ->action(function () {
                        redirect('/admin/assets/cetak');
                    }),

                ExportAction::make(),

                Tables\Actions\Action::make('pdf')
                    ->label('Download')
                    ->accessSelectedRecords()
                    ->action(function (Collection $selectedRecords) {

                        // $lsAsset = [];
                        // $i = 0;
                        // $selectedRecords->map(function (Asset $record) use($lsAsset, $i) {
                        //     //global $lsAsset;
                        //     //global $i;
                            
                        //     $lsAsset[$i] = $record->kode_asset;
                        //     $i++;
                        // });

                        $Assets = $selectedRecords->map(function (Asset $record){
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
