<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataR2r4Resource\Pages;
use App\Filament\Resources\DataR2r4Resource\RelationManagers;
use App\Models\data_r2r4;
use App\Models\Kontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class DataR2r4Resource extends Resource
{
    protected static ?string $model = data_r2r4::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pendataan Roda 2 & 4';

    //protected static ?string $slug = 'data-mobil-motor';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_brg')
                    ->required()
                    ->label('Kode Barang')
                    ->readOnly(true)
                    ->default(function (?data_r2r4 $Data_R2r4): string {
                        $last_sub = $Data_R2r4::orderBy('kode_brg', 'desc')->first();
                        $next_num = (int) substr($last_sub->kode_brg, 2, 3) + 1;
                        $next_sub = 'KR' . str_repeat('0', 3 - strlen($next_num)) . $next_num;
                        return $next_sub;
                    })
                    ->maxLength(255),
                Forms\Components\Select::make('jns_brg')
                    ->options([
                        'R2 Operasional' => 'R2 Operasional',
                        'R4 Operasional' => 'R4 Operasional',
                        'R2 Dinas' => 'R2 Dinas',
                        'R4 Dinas' => 'R4 Dinas',
                    ])->required()
                    ->label('Jenis Barang'),
                Forms\Components\TextInput::make('plat')
                    ->required()
                    ->maxLength(255)
                    ->label('No Plat'),
                Forms\Components\TextInput::make('nm_brg')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Barang'),
                Forms\Components\TextInput::make('no_bpkb')
                    ->maxLength(255)
                    ->label('No BPKB'),

                // Forms\Components\Select::make('kontrak_id')
                //     ->label('Kontrak')
                //     // ->options(function (Get $get): array {
                //     //     return Kontrak::all()->pluck('judul', 'id')->all();
                //     // })
                //     ->relationship(name: 'kontrak', titleAttribute: 'no_kontrak')
                //     ->getOptionLabelFromRecordUsing(fn(Kontrak $record) => "{$record->no_kontrak} - {$record->judul}"),

                // Forms\Components\TextInput::make('judul_kontrak')
                //     ->default(function (Get $get): string {
                //         $Kontrak = $get('kontrak_id');
                //         if(!$Kontrak){
                //             $Kontrak = "";
                //         }
                //         //dd($Kontrak);
                //         return $Kontrak;
                //     })
                //     ->readOnly(true)
                //     ->label('Judul Kontrak'),

                // ViewField::make('kontrak_id')
                //     ->view('filament.customs.select-kontrak')
                //     ->viewData([
                //         'kontraks' => Kontrak::all()->pluck('no_kontrak', 'id')->all()

                //     ])
                //     ->live(),

                // Forms\Components\DatePicker::make('jangka_wkt_awl')
                //     ->native(false)
                //     ->required()
                //     ->label('Jangka Waktu Awal'),
                // Forms\Components\DatePicker::make('jangka_wkt_akhir')
                //     ->native(false)
                //     ->required()
                //     ->label('Jangka Waktu Akhir'),
                Forms\Components\TextInput::make('thn')
                    ->maxLength(255)
                    ->label('Tahun'),
                Forms\Components\TextInput::make('no_rangka')
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_mesin')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('pajak')
                    ->native(false),
                FileUpload::make('gambar_pajak')
                    ->disk('minio')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->label('Gambar Pajak')
                    ->downloadable(),
                Forms\Components\DatePicker::make('stnk')
                ->native(false),
                FileUpload::make('gambar_stnk')
                    ->disk('minio')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->label('Gambar STNK')
                    ->downloadable(),
                FileUpload::make('bpkb')
                    ->disk('minio')
                    ->visibility('public')
                    ->downloadable(),
                Forms\Components\DatePicker::make('tgl_akhir_kir')
                    ->native(false)
                    ->label('Tanggal Akhir Kir'),
                Forms\Components\TextInput::make('warna')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('service')
                    ->label('Tanggal Akhir Service')
                    ->native(false),
                FileUpload::make('foto')
                    ->disk('minio')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->label('Foto Service')
                    ->downloadable(),
                Forms\Components\TextInput::make('pemegang')
                    ->maxLength(255),
                Forms\Components\TextInput::make('departemen')
                    ->maxLength(255),
                // Forms\Components\Select::make('stat')
                //     ->options([
                //         'Habis Kontrak' => 'Habis Kontrak',
                //         'Di pakai - Tidak ada Kontrak' => 'Di pakai - Tidak ada Kontrak',
                //         'Sewa - Kontrak Berjalan' => 'Sewa - Kontrak Berjalan',
                //         'Operasional Pedami' => 'Operasional Pedami',
                //         'Terjual' => 'Terjual',
                    
                //     ]),
                    Forms\Components\Select::make('stat')
                    ->disabled(condition: fn (?data_r2r4 $record) => $record?->stat === 'Terjual')
                    //->dehydrated(fn () => Auth::user()->role === 'admin')
                    ->options(function (?data_r2r4 $record) {
                        $options = [
                        'Habis Kontrak' => 'Habis Kontrak',
                        'Di pakai - Tidak ada Kontrak' => 'Di pakai - Tidak ada Kontrak',
                        'Sewa - Kontrak Berjalan' => 'Sewa - Kontrak Berjalan',
                        'Operasional Pedami' => 'Operasional Pedami',
                        ];

                        // Jika sedang VIEW / EDIT data lama
                        // dan status-nya sudah Disposal, tetap tampilkan
                        if ($record?->stat === 'Terjual') {
                            $options['Terjual'] = 'Terjual';
                        }

                        return $options;
                    })
                    ->required(),
                        
                    Forms\Components\TextInput::make('hrg_sewa')
                    ->prefix('Rp. ')
                    ->label('Harga Sewa')
                    ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $angka = preg_replace('/[^0-9]/', '', $state);

                            if ($angka !== '') {
                            $set('hrg_sewa', number_format($angka, 0, ',', '.'));
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
                    Forms\Components\TextInput::make('deskripsi')
                        ->maxLength(255)
                        ->columns(2),

                FileUpload::make('gambar_fisik')
                    ->disk('minio')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    //->multiple()
                    ->label('Foto Fisik')
                    ->downloadable(),
            ]);
    }

    // protected function handleRecordCreation(array $data): data_r2r4
    // {
    //     unset($data['judul_kontrak']);
    //     return static::getModel()::create($data);
    // }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     dd($data);
    //     return $data;
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_brg')->label('Kode Barang'),
                Tables\Columns\TextColumn::make('jns_brg')->label('Jenis Barang')->searchable(),
                Tables\Columns\TextColumn::make('plat')->searchable(),
                Tables\Columns\TextColumn::make('nm_brg')->label('Nama Barang')->searchable(),
                //Tables\Columns\TextColumn::make('no_bpkb')->label('No BPKB')->searchable(),
                // Tables\Columns\TextColumn::make('kontrak.no_kontrak'),
                // Tables\Columns\TextColumn::make('jangka_wkt_awl'),
                // Tables\Columns\TextColumn::make('jangka_wkt_akhir'),
                Tables\Columns\TextColumn::make('thn')->label('Tahun')->searchable(),
                Tables\Columns\TextColumn::make('no_rangka')->searchable(),
                Tables\Columns\TextColumn::make('no_mesin')->searchable(),
                Tables\Columns\TextColumn::make('pajak'),
                Tables\Columns\TextColumn::make('stnk'),
                //Tables\Columns\TextColumn::make('file'),
                Tables\Columns\TextColumn::make('tgl_akhir_kir')->label('Tanggal Akhir Kir'),  
                Tables\Columns\TextColumn::make('warna'),
                //Tables\Columns\TextColumn::make('service'),
                //Tables\Columns\TextColumn::make('foto'),
                Tables\Columns\TextColumn::make('pemegang')->searchable(),
                Tables\Columns\TextColumn::make('departemen')->searchable(),
                
                Tables\Columns\TextColumn::make('kontrak_all')
    ->label('Kontrak')
    ->html()
    ->wrap()
    ->getStateUsing(function ($record) {

        if ($record->kontrak_detail->isEmpty()) {
            return '-';
        }

        $today = Carbon::today();

        $html = $record->kontrak_detail
            ->filter(fn ($d) => $d->kontrak)
            ->map(function ($d) use ($today) {

                $k = $d->kontrak;

                $aktif = $today->between(
                    Carbon::parse($k->tgl_awal),
                    Carbon::parse($k->tgl_akhir)
                );

                $badge = $aktif
                    ? '<span class="fi-badge fi-color-success">AKTIF</span>'
                    : '<span class="fi-badge fi-color-gray">EXPIRED</span>';

                return sprintf(
                    '%s <b>%s</b> <span class="text-xs text-gray-500">(%s)</span>',
                    $badge,
                    e($k->no_kontrak),
                    Carbon::parse($k->tgl_akhir)->format('d-m-Y')
                );
            })
            ->implode('<br>');

        return new HtmlString($html);
    }),
                //Tables\Columns\TextColumn::make('kontrak_detail.kontrak.tgl_akhir'),
                Tables\Columns\TextColumn::make('stat')->label('Status')->searchable(),
                Tables\Columns\TextColumn::make('hrg_sewa')->label('Harga Sewa')->numeric(decimalPlaces: 0)
                ->prefix('Rp. '),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jns_brg')
                    ->options([
                        'R2 Operasional' => 'R2 Operasional',
                        'R4 Operasional' => 'R4 Operasional',
                        'R2 Dinas' => 'R2 Dinas',
                        'R4 Dinas' => 'R4 Dinas',
                    ])
            ])
            ->headerActions([

                ExportAction::make(),

                Tables\Actions\Action::make('pdf')
                    ->label('Download')
                    ->accessSelectedRecords()
                    ->action(function (Collection $selectedRecords) {

                        $Assets = $selectedRecords->map(function (data_r2r4 $record) {
                            return $record;
                        });

                        return response()->streamDownload(function () use ($Assets) {
                            echo Pdf::loadHtml(
                                Blade::render('filament.modals.barcode-r2r4', ['records' => $Assets])
                            )->stream();
                        }, 'Barcode.pdf');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // ...
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
            'index' => Pages\ListDataR2r4s::route('/'),
            'create' => Pages\CreateDataR2r4::route('/create'),
            'edit' => Pages\EditDataR2r4::route('/{record}/edit'),

        ];
    }
}
