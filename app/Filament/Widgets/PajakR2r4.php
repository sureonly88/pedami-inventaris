<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\data_r2r4;
use DB;

class PajakR2r4 extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Jadwal Pajak R2 & R4 (Akan Berakhir)';
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                //data_r2r4::query()->where("kode_brg", 'KR001')
                data_r2r4::query()->where(DB::raw("cast(now() as date)"), ">", DB::raw("DATE_SUB(pajak, INTERVAL 3 MONTH)"))
            )
            ->columns([
                Tables\Columns\TextColumn::make('plat'),
                Tables\Columns\TextColumn::make('nm_brg'),
                Tables\Columns\TextColumn::make('pajak')
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) {
                            return 'gray';
                        }

                        $pajakDate = \Carbon\Carbon::parse($state)->startOfDay();
                        $today = \Carbon\Carbon::now()->startOfDay();

                        if ($pajakDate->lt($today)) {
                            return 'danger'; // Kadaluarsa (Merah)
                        }

                        if ($today->diffInDays($pajakDate) <= 30) {
                            return 'warning'; // Hari ini sampai 30 hari (Kuning)
                        }

                        return 'success'; // Lebih dari 30 hari (Hijau)
                    }),
            ])
            ->defaultPaginationPageOption(5);
    }
}
