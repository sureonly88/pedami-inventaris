<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutasiAssetResource\Pages;
use App\Filament\Resources\MutasiAssetResource\RelationManagers;
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

class MutasiAssetResource extends Resource
{
    protected static ?string $model = MutasiAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('asset_id')
                    ->relationship(name: 'asset', titleAttribute: 'kode_asset')
                    ->getOptionLabelFromRecordUsing(fn(Asset $record) => "{$record->id} - {$record->kode_asset} - {$record->nama_asset}")
                    ->searchable()
                    ->label('Kode Asset'),

                Section::make('Sebelum Mutasi')
                    //->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        Forms\Components\Select::make('ruangan_id_a')
                            ->relationship(name: 'ruangan', titleAttribute: 'ruangan')
                            ->getOptionLabelFromRecordUsing(fn(Ruangan $record) => "{$record->ruangan} - {$record->lokasi}")
                            ->label('Ruang/Lokasi Awal'),

                        Forms\Components\Select::make('penanggung_jawab_id_a')
                            ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                            ->searchable()
                            ->label('Penanggung Jawab Awal'),

                        Forms\Components\Select::make('karyawan_id_a')
                            ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                            ->searchable()
                            ->label('Pemakai Awal'),
                    ]),
                
                

                Section::make('Sesudah Mutasi')
                //->description('Prevent abuse by limiting the number of requests per period')
                ->schema([
                    Forms\Components\Select::make('ruangan_id_t')
                        ->relationship(name: 'ruangan', titleAttribute: 'ruangan')
                        ->getOptionLabelFromRecordUsing(fn(Ruangan $record) => "{$record->ruangan} - {$record->lokasi}")
                        ->label('Ruang/Lokasi Tujuan'),

                    Forms\Components\Select::make('penanggung_jawab_id_t')
                        ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                        ->searchable()
                        ->label('Penanggung Jawab Tujuan'),

                    Forms\Components\Select::make('karyawan_id_t')
                        ->relationship(name: 'karyawan', titleAttribute: 'nama_karyawan')
                        ->searchable()
                        ->label('Pemakai Tujuan'),
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
                Tables\Columns\TextColumn::make('ruangan_id_a')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penanggung_jawab_id_a')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan_id_a')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangan_id_t')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penanggung_jawab_id_t')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan_id_t')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_mutasi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMutasiAssets::route('/'),
            'create' => Pages\CreateMutasiAsset::route('/create'),
            'edit' => Pages\EditMutasiAsset::route('/{record}/edit'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
{
        $data['asset_id'] = explode("-",$data['asset_id'])[0];
        return $data;
    }
}
