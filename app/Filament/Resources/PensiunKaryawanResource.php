<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PensiunKaryawanResource\Pages;
use App\Models\PensiunKaryawan;
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

class PensiunKaryawanResource extends Resource
{
    protected static ?string $model = PensiunKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-minus';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pensiun Karyawan';
    protected static ?string $modelLabel = 'Pensiun Karyawan';

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
                                    $set('jabatan_terakhir', $karyawan->jabatan);
                                    $set('divisi_terakhir_id', $karyawan->subdivisi?->divisi?->id);
                                    $set('subdivisi_terakhir_id', $karyawan->subdivisi_id);
                                }
                            })
                            ->afterStateUpdated(function (callable $set, $state) {
                                $karyawan = Karyawan::with('subdivisi.divisi')->find($state);
                                if ($karyawan) {
                                    $set('jabatan_terakhir', $karyawan->jabatan);
                                    $set('divisi_terakhir_id', $karyawan->subdivisi?->divisi?->id);
                                    $set('subdivisi_terakhir_id', $karyawan->subdivisi_id);
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
                                            <p class='text-xs text-gray-500 uppercase tracking-wider'>Jenis Kelamin</p>
                                            <p class='font-semibold'>{$karyawan->jkel}</p>
                                        </div>
                                    </div>
                                ");
                            }),
                    ]),

                Section::make('Detail Pensiun')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('tgl_pensiun')
                                ->label('Tanggal Pensiun')
                                ->required()
                                ->default(now()),
                            Forms\Components\Select::make('jenis_pensiun')
                                ->label('Jenis Pensiun')
                                ->options([
                                    'Normal' => 'Pensiun Normal (Usia)',
                                    'Dini' => 'Pensiun Dini (Permohonan Sendiri)',
                                    'Sakit' => 'Pensiun Karena Sakit',
                                    'Meninggal' => 'Meninggal Dunia',
                                    'Diberhentikan' => 'Diberhentikan Dengan Hormat',
                                    'Tidak Hormat' => 'Diberhentikan Tidak Dengan Hormat',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('no_sk')
                                ->label('No. SK Pensiun')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('pesangon')
                                ->label('Pesangon / Uang Penghargaan')
                                ->prefix('Rp')
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),

                Section::make('Posisi Terakhir')
                    ->description('Diambil otomatis dari data karyawan')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('jabatan_terakhir')
                                ->label('Jabatan Terakhir')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\Select::make('divisi_terakhir_id')
                                ->label('Divisi Terakhir')
                                ->options(Divisi::pluck('nama_divisi', 'id'))
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\Select::make('subdivisi_terakhir_id')
                                ->label('Sub Divisi Terakhir')
                                ->options(Subdivisi::pluck('nama_sub', 'id'))
                                ->disabled()
                                ->dehydrated(),
                        ]),
                    ]),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan Tambahan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_pensiun')
                    ->label('Tgl Pensiun')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_sk')
                    ->label('No SK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('karyawan.nama_karyawan')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jabatan_terakhir')
                    ->label('Jabatan'),
                Tables\Columns\TextColumn::make('divisiTerakhir.nama_divisi')
                    ->label('Divisi'),
                Tables\Columns\TextColumn::make('jenis_pensiun')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Normal' => 'success',
                        'Dini' => 'warning',
                        'Sakit' => 'danger',
                        'Meninggal' => 'gray',
                        'Diberhentikan' => 'info',
                        'Tidak Hormat' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pesangon')
                    ->label('Pesangon')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
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
            'index' => Pages\ListPensiunKaryawans::route('/'),
            'create' => Pages\CreatePensiunKaryawan::route('/create'),
            'edit' => Pages\EditPensiunKaryawan::route('/{record}/edit'),
        ];
    }
}
