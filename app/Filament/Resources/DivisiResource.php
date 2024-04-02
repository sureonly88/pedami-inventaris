<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisiResource\Pages;
use App\Filament\Resources\DivisiResource\RelationManagers;
use App\Models\Divisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DivisiResource extends Resource
{
    protected static ?string $model = Divisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('kode_divisi')
                ->required()
                ->readOnly(true)
                ->default(function (?Divisi $Divisi): String {
                    $last_sub = $Divisi::orderBy('kode_divisi','desc')->first();
                    $next_num = (int)substr($last_sub->kode_divisi, 2, 3) + 1;
                    $next_sub = 'KD'. str_repeat('0', 3 - strlen($next_num)) . $next_num;
                    return $next_sub;
                })
                ->maxLength(255),
            Forms\Components\TextInput::make('nama_divisi')
                ->required()
                ->maxLength(255)
            ->columns(2)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_divisi'),
                Tables\Columns\TextColumn::make('nama_divisi'),
            ])
            ->filters([
            Tables\Filters\SelectFilter::make('kelompok_asset')
                ->options([ 
                    'Diversifikasi Usaha' => 'Diversifikasi Usaha',
                    'HRD' => 'HRD',
                    'Keuangan' => 'Keuangan',
                    'Pelayanan Pelanggan' => 'Pelayanan Pelanggan',
                    'Pengadaan' => 'Pengadaan',
                    'Simpan Pinjam' => 'Simpan Pinjam',
                    'Tehnik' => 'Tehnik',
                ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisis::route('/'),
            'create' => Pages\CreateDivisi::route('/create'),
            'edit' => Pages\EditDivisi::route('/{record}/edit'),
        ];
    }
}
