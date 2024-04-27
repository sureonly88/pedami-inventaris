<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataR2r4Resource\Pages;
use App\Filament\Resources\DataR2r4Resource\RelationManagers;
use App\Models\data_r2r4;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class DataR2r4Resource extends Resource
{
    protected static ?string $model = data_r2r4::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pendataan Roda 2 & 4';

    //protected static ?string $slug = 'data-mobil-motor';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode_brg')
                ->required()
                ->label('Kode Barang')
                ->readOnly(true)
                ->default(function (?data_r2r4 $Data_R2r4): String {
                    $last_sub = $Data_R2r4::orderBy('kode_brg','desc')->first();
                    $next_num = (int)substr($last_sub->kode_brg, 2, 3) + 1;
                    $next_sub = 'KR'. str_repeat('0', 3 - strlen($next_num)) . $next_num;
                    return $next_sub;
                })
                ->maxLength(255),
                Forms\Components\select::make('jns_brg')
                    ->options([
                        'R2' => 'R2',
                        'R4' => 'R4',
                    ])->required()
                    ->label('Jenis Barang'),
                Forms\Components\TextInput::make('plat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nm_brg')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('Gambar_Fisik')
                    ->image()
                    ->imageEditor(),
                Forms\Components\TextInput::make('no_kontrak')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('jangka_wkt_awl')
                    ->native(false)
                    ->required(),
                Forms\Components\DatePicker::make('jangka_wkt_akhir')
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('thn')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_rangka')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_mesin')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('pajak')
                    ->native(false)
                    ->required(),
                FileUpload::make('Gambar_Pajak')
                    ->image()
                    ->imageEditor(),
                Forms\Components\DatePicker::make('stnk')
                    ->native(false)
                    ->required(),
                FileUpload::make('Gambar_Stnk')
                    ->image()
                    ->imageEditor(),
                Forms\Components\TextInput::make('warna')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pemegang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('departemen')
                    ->required()
                    ->maxLength(255),
                Forms\Components\select::make('stat')
                    ->options([
                        'Dipakai' => 'Dipakai',
                        'Habis Kontrak' => 'Habis Kontrak',
                        'Diperpanjang' => 'Diperpanjang',
                        'Dikembalikan' => 'Dikembalikan',
                        'Dibeli' => 'Dibeli',
                    ])->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_brg'),
                Tables\Columns\TextColumn::make('jns_brg'),
                Tables\Columns\TextColumn::make('plat')->searchable(),
                Tables\Columns\TextColumn::make('nm_brg'),
                Tables\Columns\TextColumn::make('no_kontrak'),
                Tables\Columns\TextColumn::make('jangka_wkt_awl'),
                Tables\Columns\TextColumn::make('jangka_wkt_akhir'),
                Tables\Columns\TextColumn::make('thn'),
                Tables\Columns\TextColumn::make('no_rangka'),
                Tables\Columns\TextColumn::make('no_mesin'),
                Tables\Columns\TextColumn::make('pajak'),
                Tables\Columns\TextColumn::make('stnk'),
                Tables\Columns\TextColumn::make('warna'),
                Tables\Columns\TextColumn::make('pemegang'),
                Tables\Columns\TextColumn::make('departemen'),
                Tables\Columns\TextColumn::make('stat'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jns_brg')
                    ->options([
                        'R2' => 'R2',
                        'R4' => 'R4',
                    ])
            ])
            ->headerActions([
                
                ExportAction::make()
               
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // ...
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDataR2r4s::route('/'),
            'create' => Pages\CreateDataR2r4::route('/create'),
            'edit' => Pages\EditDataR2r4::route('/{record}/edit'),
           
        ];
    }
}
