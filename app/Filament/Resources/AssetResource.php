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
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_asset')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('kelompok_asset')
                    ->options([
                        'kantor' => 'Perabotan Kantor',
                        'komputer' => 'Peralatan Komputer',
                        'kendaraan' => 'Kendaraan',
                    ])->required(),
                Forms\Components\Select::make('lokasi')
                    ->options([
                        'Kantor A. Yani' => 'Kantor A Yani',
                        'Kantor S. Parman' => 'Kantor S Parman',
                        'Kantor Beruntung' => 'Kantor Beruntung',
                        'Kantor Sutoyo' => 'Kantor Sutoyo',
                        'Kantor Cemara' => 'Kantor Cemara',
                    ])->required(),
                Forms\Components\TextInput::make('penanggung_jawab')
                    ->required()
                    ->maxLength(255),

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
                Tables\Columns\TextColumn::make('penanggung_jawab'),
                Tables\Columns\TextColumn::make('karyawan.nama_karyawan')->label('Pemakai'),
                Tables\Columns\TextColumn::make('karyawan.subdivisi.divisi.nama_divisi'),
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

                ExportAction::make()
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

                Action::make('pdf')
                    ->label('Download')
                    ->color('success')
                    ->icon('heroicon-s-squares-2x2')
                    ->action(function (Asset $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('filament.modals.barcode-pdf', ['record' => $record])
                            )->stream();
                        }, 'Barcode-' . $record->kode_asset . '.pdf');
                    }),

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
