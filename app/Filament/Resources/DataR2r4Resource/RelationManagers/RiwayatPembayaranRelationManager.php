<?php

namespace App\Filament\Resources\DataR2r4Resource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RiwayatPembayaranRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatPembayaran';
    protected static ?string $title = 'Riwayat Pembayaran Pajak/STNK/KIR';
    protected static ?string $modelLabel = 'Riwayat Pembayaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_pembayaran')
                    ->options([
                        'Pajak' => 'Pajak Tahunan',
                        'STNK' => 'STNK / Ganti Plat (5 Tahun)',
                        'KIR' => 'KIR (Uji Berkala)',
                    ])
                    ->required()
                    ->label('Jenis Pembayaran'),
                Forms\Components\DatePicker::make('tanggal_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('biaya')
                    ->label('Total Biaya')
                    ->prefix('Rp')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\DatePicker::make('jatuh_tempo_berikutnya')
                    ->label('Jatuh Tempo Berikutnya')
                    ->helperText('Tanggal habis masa berlaku selanjutnya'),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Catatan')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('bukti_foto')
                    ->label('Foto Bukti / Nota Pembayaran')
                    ->disk('minio')
                    ->visibility('public')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_pembayaran')
            ->columns([
                Tables\Columns\BadgeColumn::make('jenis_pembayaran')
                    ->colors([
                        'primary' => 'Pajak',
                        'success' => 'STNK',
                        'warning' => 'KIR',
                    ])
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('biaya')
                    ->label('Biaya')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('jatuh_tempo_berikutnya')
                    ->label('Tempo Berikutnya')
                    ->date('d M Y')
                    ->color(fn($record) => $record->jatuh_tempo_berikutnya < now() ? 'danger' : 'success'),
                Tables\Columns\ImageColumn::make('bukti_foto')
                    ->label('Bukti')
                    ->disk('minio')
                    ->url(fn($record) => $record->bukti_foto ? \Illuminate\Support\Facades\Storage::disk('minio')->url($record->bukti_foto) : null, true)
                    ->circular(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
