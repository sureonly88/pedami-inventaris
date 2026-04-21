<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MutasiAssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'mutasiAssets';

    protected static ?string $title = 'Riwayat Mutasi';

    protected static ?string $modelLabel = 'Riwayat Mutasi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('deskripsi')
            ->columns([
                Tables\Columns\TextColumn::make('tgl_mutasi')
                    ->label('Tanggal Mutasi')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('gambar_awal')
                    ->label('Gambar Awal')
                    ->disk('minio'),
                Tables\Columns\ImageColumn::make('gambar_terbaru')
                    ->label('Gambar Terbaru')
                    ->disk('minio'),
                Tables\Columns\TextColumn::make('ruangan_a.ruangan')
                    ->label('Ruangan Awal')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('penanggung_jawab_a.nama_karyawan')
                    ->label('Penanggung Jawab Awal')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('karyawan_a.nama_karyawan')
                    ->label('Pemakai Awal')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('ruangan_t.ruangan')
                    ->label('Ruangan Tujuan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('penanggung_jawab_t.nama_karyawan')
                    ->label('Penanggung Jawab Tujuan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('karyawan_t.nama_karyawan')
                    ->label('Pemakai Tujuan')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Keterangan')
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->defaultSort('tgl_mutasi', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}