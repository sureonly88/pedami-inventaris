<?php

namespace App\Filament\Resources\KaryawanResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PensiunKaryawansRelationManager extends RelationManager
{
    protected static string $relationship = 'pensiunKaryawans';

    protected static ?string $title = 'Riwayat Pensiun';

    protected static ?string $modelLabel = 'Riwayat Pensiun';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_sk')
            ->columns([
                Tables\Columns\TextColumn::make('tgl_pensiun')
                    ->label('Tanggal Pensiun')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_sk')
                    ->label('No SK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_pensiun')
                    ->label('Jenis Pensiun')
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
                Tables\Columns\TextColumn::make('jabatan_terakhir')
                    ->label('Jabatan Terakhir'),
                Tables\Columns\TextColumn::make('divisiTerakhir.nama_divisi')
                    ->label('Divisi Terakhir'),
                Tables\Columns\TextColumn::make('subdivisiTerakhir.nama_sub')
                    ->label('Sub Divisi Terakhir'),
                Tables\Columns\TextColumn::make('pesangon')
                    ->label('Pesangon')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50),
            ])
            ->defaultSort('tgl_pensiun', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}