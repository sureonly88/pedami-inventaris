<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Filament\Resources\KaryawanResource\RelationManagers;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Subdivisi;
use App\Models\Divisi;
use Filament\Forms\Get;


class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Karyawan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static function formatMasaKerja(?string $tanggalMasukKerja): ?string
    {
        if (blank($tanggalMasukKerja)) {
            return null;
        }

        $selisih = Carbon::parse($tanggalMasukKerja)->diff(now());

        return sprintf('%d tahun %d bulan %d hari', $selisih->y, $selisih->m, $selisih->d);
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_karyawan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_ktp')
                    ->label('No KTP')
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No HP')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_rekening')
                    ->label('No Rekening')
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->native(false),
                Forms\Components\DatePicker::make('tanggal_masuk_kerja')
                    ->label('Tanggal Masuk Kerja')
                    ->native(false)
                    ->live()
                    ->afterStateHydrated(fn (callable $set, $state) => $set('masa_kerja', static::formatMasaKerja($state)))
                    ->afterStateUpdated(fn (callable $set, $state) => $set('masa_kerja', static::formatMasaKerja($state))),
                Forms\Components\TextInput::make('masa_kerja')
                    ->label('Masa Kerja')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Otomatis dihitung dari tanggal masuk kerja'),
                Forms\Components\TextInput::make('nama_bank')
                    ->label('Nama Bank')
                    ->maxLength(255),
                Forms\Components\TextInput::make('kontak_darurat')
                    ->label('Kontak Darurat')
                    ->maxLength(255),
                Forms\Components\Select::make('status_karyawan')
                    ->label('Status Karyawan')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Pensiun' => 'Pensiun',
                        'Nonaktif' => 'Nonaktif',
                    ])
                    ->default('Aktif')
                    ->required(),
                Forms\Components\Select::make('jabatan')
                    ->options([
                        'Ketua' => 'Ketua',
                        'Bendahara' => 'Bendahara',
                        'Sekretaris' => 'Sekretaris',
                        'Manager'=> 'Manager',
                        'Kepala Divisi' => 'Kepala Divisi',
                        'Koordinator' => 'Koordinator',
                        'Staff' => 'Staf',
                        'All Karyawan' => 'All Karyawan',
                    ])->required(),

                Forms\Components\Select::make('divisi_id')
                    ->label('Divisi')
                    ->options(Divisi::pluck('nama_divisi', 'id'))
                    ->dehydrated(false)
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('subdivisi_id', null)),

                Forms\Components\Select::make('subdivisi_id')
                    ->label('Sub Divisi')
                    ->options(function (Get $get) {
                        $divisiId = $get('divisi_id');

                        if (!$divisiId) {
                            return [];
                        }

                        return Subdivisi::where('divisi_id', $divisiId)
                            ->pluck('nama_sub', 'id');
                    })
                    ->reactive()
                    ->required(),

                Forms\Components\Select::make('jkel')
                ->options([
                    'Laki-Laki' => 'Laki - Laki',
                    'Perempuan' => 'Perempuan',
                    'L/P' => 'L/P',
                ])->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik'),
                Tables\Columns\TextColumn::make('nama_karyawan')->searchable(),
                Tables\Columns\TextColumn::make('no_ktp')->label('No KTP')->searchable(),
                Tables\Columns\TextColumn::make('no_hp')->label('No HP')->searchable(),
                Tables\Columns\TextColumn::make('no_rekening')->label('No Rekening')->searchable(),
                Tables\Columns\TextColumn::make('nama_bank')->label('Nama Bank')->searchable(),
                Tables\Columns\TextColumn::make('tempat_lahir')->label('Tempat Lahir')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')->label('Tgl Lahir')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tanggal_masuk_kerja')->label('Tgl Masuk')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('masa_kerja')->label('Masa Kerja'),
                Tables\Columns\TextColumn::make('kontak_darurat')->label('Kontak Darurat')->searchable(),
                Tables\Columns\TextColumn::make('status_karyawan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Pensiun' => 'danger',
                        'Nonaktif' => 'gray',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan'),
                Tables\Columns\TextColumn::make('subdivisi.divisi.nama_divisi'),
                Tables\Columns\TextColumn::make('subdivisi.nama_sub'),
                Tables\Columns\TextColumn::make('jkel'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_karyawan')
                    ->label('Status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Pensiun' => 'Pensiun',
                        'Nonaktif' => 'Nonaktif',
                    ]),
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
        return [
            RelationManagers\MutasiKaryawansRelationManager::class,
            RelationManagers\PensiunKaryawansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
