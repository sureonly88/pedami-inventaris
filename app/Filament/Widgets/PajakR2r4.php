<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\data_r2r4;

class PajakR2r4 extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                //data_r2r4::query()->where("kode_brg", 'KR001')
                data_r2r4::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('kode_brg'),
                Tables\Columns\TextColumn::make('nm_brg'),
                Tables\Columns\TextColumn::make('pajak'),
            ]);
    }
}
