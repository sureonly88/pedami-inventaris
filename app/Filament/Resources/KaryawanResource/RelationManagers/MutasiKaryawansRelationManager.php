<?php

namespace App\Filament\Resources\KaryawanResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MutasiKaryawansRelationManager extends RelationManager
{
    protected static string $relationship = 'mutasiKaryawans';

    protected static ?string $title = 'Riwayat Mutasi';

    protected static ?string $modelLabel = 'Riwayat Mutasi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_sk')
            ->columns([
                Tables\Columns\TextColumn::make('tgl_mutasi')
                    ->label('Tanggal Mutasi')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_sk')
                    ->label('No SK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan_asal')
                    ->label('Jabatan Asal'),
                Tables\Columns\TextColumn::make('divisiAsal.nama_divisi')
                    ->label('Divisi Asal'),
                Tables\Columns\TextColumn::make('subdivisiAsal.nama_sub')
                    ->label('Sub Divisi Asal'),
                Tables\Columns\TextColumn::make('jabatan_tujuan')
                    ->label('Jabatan Tujuan')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('divisiTujuan.nama_divisi')
                    ->label('Divisi Tujuan'),
                Tables\Columns\TextColumn::make('subdivisiTujuan.nama_sub')
                    ->label('Sub Divisi Tujuan'),
                Tables\Columns\TextColumn::make('alasan')
                    ->label('Alasan')
                    ->wrap()
                    ->limit(50),
            ])
            ->defaultSort('tgl_mutasi', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}