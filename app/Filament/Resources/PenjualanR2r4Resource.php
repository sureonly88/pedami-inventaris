<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanR2r4Resource\Pages;
use App\Filament\Resources\PenjualanR2r4Resource\RelationManagers;
use App\Models\data_r2r4;
use App\Models\PenjualanR2r4;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenjualanDirectExport;
use Illuminate\Support\Facades\Redirect;

class PenjualanR2r4Resource extends Resource
{
    protected static ?string $model = PenjualanR2r4::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Penjualan R2 & R4';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('data_r2r4_id')
            ->label('Pilih Kendaraan')
            ->relationship('data_r2r4', 'plat')
            ->searchable()
            ->getOptionLabelFromRecordUsing(fn ($record) =>
                "{$record->kode_brg} - {$record->plat}"
            )
            ->reactive()
            ->afterStateHydrated(function ($state, callable $set) {
                $data = data_r2r4::find($state);

                if ($data) {
                    $set('jns_brg', $data->jns_brg);
                    $set('nm_brg', $data->nm_brg);
                    $set('gambar_pajak', [$data->gambar_pajak]);
                    $set('gambar_stnk', [$data->gambar_stnk]);
                    $set('gambar_fisik', [$data->gambar_fisik]);
                    $set('thn', $data->thn);
                    $set('no_rangka', $data->no_rangka);
                    $set('no_mesin', $data->no_mesin);
                    $set('warna', $data->warna);
                    $set('bpkb', $data->bpkb);
                }
            })
            ->afterStateUpdated(function ($state, callable $set) {
                $data = data_r2r4::find($state);

                if ($data) {
                    $set('jns_brg', $data->jns_brg);
                    $set('nm_brg', $data->nm_brg);
                    $set('gambar_pajak', [$data->gambar_pajak]);
                    $set('gambar_stnk', [$data->gambar_stnk]);
                    $set('gambar_fisik', [$data->gambar_fisik]);
                    $set('thn', $data->thn);
                    $set('no_rangka', $data->no_rangka);
                    $set('no_mesin', $data->no_mesin);
                    $set('warna', $data->warna);
                    $set('bpkb', $data->bpkb);
                }
            }),

                    Forms\Components\Section::make('Informasi Kendaraan')
            ->visible(fn (callable $get) => filled($get('data_r2r4_id')))
            ->schema([

                Forms\Components\TextInput::make('jns_brg')
                    ->label('Jenis Barang')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\TextInput::make('nm_brg')
                    ->label('Nama Barang')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\TextInput::make('thn')
                    ->label('Tahun')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\TextInput::make('no_rangka')
                    ->label('No Rangka')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\TextInput::make('no_mesin')
                    ->label('No Mesin')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\TextInput::make('warna')
                    ->label('Warna')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\TextInput::make('bpkb')
                    ->label('BPKB')
                    ->dehydrated(false)
                    ->disabled(),

                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\FileUpload::make('gambar_pajak')
                        ->disk('minio')
                        ->visibility('public')
                        ->label('Gambar Pajak')
                        ->dehydrated(false)
                        ->image()
                        ->disabled(),

                    Forms\Components\FileUpload::make('gambar_stnk')
                        ->disk('minio')
                        ->visibility('public')
                        ->label('Gambar STNK')
                        ->dehydrated(false)
                        ->image()
                        ->disabled(),

                    Forms\Components\FileUpload::make('gambar_fisik')
                        ->disk('minio')
                        ->visibility('public')
                        ->label('Gambar Fisik')
                        ->dehydrated(false)
                        ->image()
                        ->disabled(),
                ]),
            ]),

                Forms\Components\DatePicker::make('tgl_jual')
                    ->label('Tanggal Jual'),
                Forms\Components\TextInput::make('hrg_jual')
    ->label('Harga Jual')
    ->prefix('Rp. ')
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $angka = preg_replace('/[^0-9]/', '', $state);

        if ($angka !== '') {
            $set('hrg_jual', number_format($angka, 0, ',', '.'));
        }
    })
             ->dehydrateStateUsing(fn ($state) => str_replace('.', '', $state)),

                Forms\Components\TextInput::make('nm_pembeli')
                    ->label('Nama Pembeli')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('data_r2r4.plat')
                    ->numeric()
                    ->label('Nopol')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_jual')
                    ->date()
                    ->label('Tanggal Penjualan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hrg_jual')
                    ->numeric()
                    ->prefix('Rp. ')
                    ->label('Harga Penjualan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nm_pembeli')
                    ->label('Nama Pembeli')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tgl_jual')
                ->form([
                    DatePicker::make('from')
                        ->label('Dari Tanggal'),
                    DatePicker::make('until')
                        ->label('Sampai Tanggal'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when(
                            $data['from'],
                            fn ($query) => $query->whereDate('tgl_jual', '>=', $data['from'])
                        )
                        ->when(
                            $data['until'],
                            fn ($query) => $query->whereDate('tgl_jual', '<=', $data['until'])
                        );
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('downloadSelected')
    ->label('Download Excel')
    ->icon('heroicon-o-arrow-down-tray')
    ->action(function ($records) {
        $ids = $records->pluck('id')->implode(',');

        return Redirect::away(
            route('download.penjualan.selected', $ids)
        );
    })
    
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
            'index' => Pages\ListPenjualanR2r4s::route('/'),
            'create' => Pages\CreatePenjualanR2r4::route('/create'),
            'edit' => Pages\EditPenjualanR2r4::route('/{record}/edit'),
        ];
    }
}
