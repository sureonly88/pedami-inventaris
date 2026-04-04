<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatServiceAcsRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatServiceAcs';
    protected static ?string $title = 'Riwayat Servis';
    protected static ?string $modelLabel = 'Riwayat Servis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_service')
                    ->label('Tanggal Servis')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('biaya')
                    ->label('Biaya')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('teknisi')
                    ->label('Teknisi'),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('bukti_foto')
                    ->disk('minio')
                    ->visibility('public')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jenis_pekerjaan')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_service')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_pekerjaan')
                    ->label('Pekerjaan'),
                Tables\Columns\TextColumn::make('biaya')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('teknisi')
                    ->label('Teknisi'),
                Tables\Columns\ImageColumn::make('bukti_foto')
                    ->disk('minio')
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
