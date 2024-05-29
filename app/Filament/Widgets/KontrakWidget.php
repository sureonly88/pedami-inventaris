<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Kontrak;
use DB;

class KontrakWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                //data_r2r4::query()->where("kode_brg", 'KR001')
                Kontrak::query()->where(DB::raw("cast(now() as date)"), ">", DB::raw("DATE_SUB(tgl_akhir, INTERVAL 3 MONTH)"))
            )
            ->columns([
                Tables\Columns\TextColumn::make('no_kontrak'),
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('tgl_akhir'),
            ])
            ->defaultPaginationPageOption(5);
    }
}
