<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutasiKaryawanResource\Pages;
use App\Models\MutasiKaryawan;
use App\Models\Karyawan;
use App\Models\Divisi;
use App\Models\Subdivisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;

class MutasiKaryawanResource extends Resource
{
    protected static ?string $model = MutasiKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Mutasi Karyawan';
    protected static ?string $modelLabel = 'Mutasi Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Karyawan')
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Karyawan')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Karyawan::where('nama_karyawan', 'like', "%{$search}%")
                                    ->orWhere('nik', 'like', "%{$search}%")
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [$item->id => $item->nik . ' - ' . $item->nama_karyawan]);
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $k = Karyawan::find($value);
                                return $k ? $k->nik . ' - ' . $k->nama_karyawan : null;
                            })
                            ->required()
                            ->live()
                            ->afterStateHydrated(function (callable $set, $state) {
                                $karyawan = Karyawan::with('subdivisi.divisi')->find($state);

                                if ($karyawan) {
                                    $set('jabatan_asal', $karyawan->jabatan);
                                    $set('divisi_asal_id', $karyawan->subdivisi?->divisi?->id);
                                    $set('subdivisi_asal_id', $karyawan->subdivisi_id);
                                }
                            })
                            ->afterStateUpdated(function (callable $set, $state) {
                                $karyawan = Karyawan::with('subdivisi.divisi')->find($state);
                                if ($karyawan) {
                                    $set('jabatan_asal', $karyawan->jabatan);
                                    $set('divisi_asal_id', $karyawan->subdivisi?->divisi?->id);
                                    $set('subdivisi_asal_id', $karyawan->subdivisi_id);
                                }
                            }),

                        Forms\Components\Placeholder::make('karyawan_info')
                            ->label('Informasi Karyawan')
                            ->visible(fn($get) => $get('karyawan_id'))
                            ->content(function ($get) {
                                $karyawan = Karyawan::with('subdivisi.divisi')->find($get('karyawan_id'));
                                if (!$karyawan) return null;

                                return new \Illuminate\Support\HtmlString("
                                    <div class='grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700'>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>NIK</p>
                                            <p class='font-bold text-primary-600'>{$karyawan->nik}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Jabatan</p>
                                            <p class='font-semibold'>{$karyawan->jabatan}</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Divisi</p>
                                            <p class='font-semibold'>" . ($karyawan->subdivisi?->divisi?->nama_divisi ?? '-') . "</p>
                                        </div>
                                        <div>
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Sub Divisi</p>
                                            <p class='font-semibold'>" . ($karyawan->subdivisi?->nama_sub ?? '-') . "</p>
                                        </div>
                                    </div>
                                ");
                            }),

                        Forms\Components\TextInput::make('no_sk')
                            ->label('No. SK Mutasi')
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('tgl_mutasi')
                            ->label('Tanggal Mutasi')
                            ->required()
                            ->default(now()),
                    ]),

                Section::make('Posisi Asal')
                    ->description('Diambil otomatis dari data karyawan')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('jabatan_asal')
                                ->label('Jabatan Asal')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\Select::make('divisi_asal_id')
                                ->label('Divisi Asal')
                                ->options(Divisi::pluck('nama_divisi', 'id'))
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\Select::make('subdivisi_asal_id')
                                ->label('Sub Divisi Asal')
                                ->options(Subdivisi::pluck('nama_sub', 'id'))
                                ->disabled()
                                ->dehydrated(),
                        ]),
                    ]),

                Section::make('Posisi Tujuan')
                    ->description('Isi posisi baru karyawan setelah mutasi')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\Select::make('jabatan_tujuan')
                                ->label('Jabatan Baru')
                                ->options([
                                    'Ketua' => 'Ketua',
                                    'Bendahara' => 'Bendahara',
                                    'Sekretaris' => 'Sekretaris',
                                    'Manager' => 'Manager',
                                    'Kepala Divisi' => 'Kepala Divisi',
                                    'Koordinator' => 'Koordinator',
                                    'Staff' => 'Staf',
                                    'All Karyawan' => 'All Karyawan',
                                ])
                                ->required(),
                            Forms\Components\Select::make('divisi_tujuan_id')
                                ->label('Divisi Baru')
                                ->options(Divisi::pluck('nama_divisi', 'id'))
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('subdivisi_tujuan_id', null))
                                ->required(),
                            Forms\Components\Select::make('subdivisi_tujuan_id')
                                ->label('Sub Divisi Baru')
                                ->options(function (Get $get) {
                                    $divisiId = $get('divisi_tujuan_id');
                                    if (!$divisiId) return [];
                                    return Subdivisi::where('divisi_id', $divisiId)->pluck('nama_sub', 'id');
                                })
                                ->reactive()
                                ->required(),
                        ]),
                    ]),

                Forms\Components\Textarea::make('alasan')
                    ->label('Alasan / Keterangan Mutasi')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_mutasi')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_sk')
                    ->label('No SK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('karyawan.nama_karyawan')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jabatan_asal')
                    ->label('Jabatan Asal'),
                Tables\Columns\TextColumn::make('divisiAsal.nama_divisi')
                    ->label('Divisi Asal'),
                Tables\Columns\TextColumn::make('jabatan_tujuan')
                    ->label('Jabatan Baru')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('divisiTujuan.nama_divisi')
                    ->label('Divisi Baru')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMutasiKaryawans::route('/'),
            'create' => Pages\CreateMutasiKaryawan::route('/create'),
            'edit' => Pages\EditMutasiKaryawan::route('/{record}/edit'),
        ];
    }
}
