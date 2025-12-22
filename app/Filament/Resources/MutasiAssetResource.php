<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutasiAssetResource\Pages;
use App\Filament\Resources\MutasiAssetResource\RelationManagers;
use App\Models\Karyawan;
use App\Models\MutasiAsset;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Ruangan;
use App\Models\Asset;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Forms\Components\DatePicker;

class MutasiAssetResource extends Resource
{
    protected static ?string $model = MutasiAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaksi';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('asset_id')
                    ->label('Kode Asset')
                   // ->getOptionLabelFromRecordUsing(fn(Asset $record) => "{$record->id} - {$record->kode_asset} - {$record->nama_asset}")
                    ->options(function (): array {
                        return Asset::all()->pluck('kode_nama', 'id')->all();
                })
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state, Get $get) 
                {
                    $asset = Asset::find($get('asset_id'));
                    $set('ruangan_id_a', $asset->ruangan_id);
                    $set('penanggung_jawab_id_a', $asset->penanggung_jawab_id);
                    $set('karyawan_id_a', $asset->karyawan_id);

                }),
                

                Section::make('Sebelum Mutasi')
                    //->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        Forms\Components\Select::make('ruangan_id_a')
                            ->label('Ruangan Tujuan')
                            ->options(function (): array {
                                return Ruangan::all()->pluck('ruangan', 'id')->all();
                        })
                        ->disabled(),

                        Forms\Components\Select::make('penanggung_jawab_id_a')
                            ->label('Penanggung Jawab Tujuan')
                            ->options(function (): array {
                                return Karyawan::all()->pluck('nama_karyawan', 'id')->all();
                        })
                        ->disabled(),

                        Forms\Components\Select::make('karyawan_id_a')
                            ->label('Pemakai Tujuan')
                            ->options(function (): array {
                                return Karyawan::all()->pluck('nama_karyawan', 'id')->all();
                        })
                        ->disabled(),
                    ]),
                
                Section::make('Sesudah Mutasi')
                //->description('Prevent abuse by limiting the number of requests per period')
                ->schema([
                        Forms\Components\Select::make('ruangan_id_t')
                            ->label('Ruangan Tujuan')
                            ->options(function (): array {
                                return Ruangan::all()->pluck('ruangan', 'id')->all();
                        }),

                        Forms\Components\Select::make('penanggung_jawab_id_t')
                            ->label('Penanggung Jawab Tujuan')
                            ->options(function (): array {
                                return Karyawan::all()->pluck('nama_karyawan', 'id')->all();
                        }),

                        Forms\Components\Select::make('karyawan_id_t')
                            ->label('Pemakai Tujuan')
                            ->options(function (): array {
                                return Karyawan::all()->pluck('nama_karyawan', 'id')->all();
                        }),
                ]),

                Forms\Components\DatePicker::make('tgl_mutasi')
                    ->label('Tanggal Mutasi')
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->required()
                    ->maxLength(100),
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
                Tables\Columns\TextColumn::make('ruangan_a.ruangan')
                    ->label("Ruangan Awal")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penanggung_jawab_a.nama_karyawan')
                    ->label("Penanggung Jawab Awal")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan_a.nama_karyawan')
                    ->label("Pemakai Awal")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangan_t.ruangan')
                    ->label("Ruangan Tujuan") 
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penanggung_jawab_t.nama_karyawan')
                    ->label("Penanggung Jawab Tujuan")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan_t.nama_karyawan')
                    ->label("Pemakai Tujuan")
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_mutasi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tgl_mutasi')
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
                            fn ($query) => $query->whereDate('tgl_mutasi', '>=', $data['from'])
                        )
                        ->when(
                            $data['until'],
                            fn ($query) => $query->whereDate('tgl_mutasi', '<=', $data['until'])
                        );
                }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListMutasiAssets::route('/'),
            'create' => Pages\CreateMutasiAsset::route('/create'),
            //'edit' => Pages\EditMutasiAsset::route('/{record}/edit'),
        ];
    }

    

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     //dd($data['ruangan_id_a']);die();

        
    //     return $data;
    // }
}
