<?php

namespace App\Filament\Resources\DataR2r4Resource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatServisRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatServis';

    protected static ?string $title = 'Riwayat Service Kendaraan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_servis')
                    ->label('Tanggal Servis')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('jenis_servis')
                    ->label('Jenis Pekerjaan/Servis')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\TextInput::make('biaya')
                    ->label('Total Biaya')
                    ->prefix('Rp')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('bengkel')
                    ->label('Nama Bengkel/Toko')
                    ->maxLength(255),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('struk_foto')
                    ->disk('minio')
                    ->visibility('public')
                    ->label('Foto Nota/Struk (Opsional)')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_servis')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_servis')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_servis')
                    ->label('Pekerjaan')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('biaya')
                    ->label('Total Biaya')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('bengkel')
                    ->label('Bengkel')
                    ->searchable(),
            ])
            ->emptyStateHeading('tidak ada riwayat service')
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
