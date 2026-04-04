<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Kontrak;
use App\Models\data_r2r4;
use Carbon\Carbon;
use DB;

class KontrakWidget extends BaseWidget
{
    protected static ?int $sort = 9;
    protected static ?string $heading = 'Kontrak Sewa (Akan Berakhir)';
    protected static string $view = 'filament.widgets.kontrak-table-widget';
    protected int | string | array $columnSpan = 'full';

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'table' => $this->table($this->makeTable()),
            'heading' => static::$heading,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                data_r2r4::query()
                    ->where('stat', 'Sewa - Kontrak Berjalan')
                    ->whereHas('kontrak_detail.kontrak', function ($q) {
                        $q->whereDate('tgl_akhir', '>=', now());
                    })
                    ->with(['kontrak_detail.kontrak'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('plat')
                    ->label('No Plat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nm_brg')
                    ->label('Nama Barang'),
                Tables\Columns\TextColumn::make('kontrak_detail')
                    ->label('No Kontrak')
                    ->getStateUsing(fn ($record) => $record->kontrak_detail->last()?->kontrak?->no_kontrak ?? '-'),
                Tables\Columns\TextColumn::make('tgl_akhir')
                    ->label('Tgl Akhir')
                    ->getStateUsing(fn ($record) => $record->kontrak_detail->last()?->kontrak?->tgl_akhir ?? '-')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(function ($state, $record) {
                        try {
                            $tgl_akhir = $record->kontrak_detail->last()?->kontrak?->tgl_akhir;
                            if (!$tgl_akhir) return 'gray';
                            
                            $date = Carbon::parse($tgl_akhir)->startOfDay();
                            $today = now()->startOfDay();
                            $thirtyDaysLater = now()->startOfDay()->addDays(30);

                            // Jika tanggal akhir lebih besar dari 30 hari dari sekarang -> HIJAU
                            if ($date->gt($thirtyDaysLater)) {
                                return 'success';
                            }
                            
                            // Jika tanggal akhir berada di rentang hari ini s/d 30 hari ke depan -> KUNING
                            if ($date->lte($thirtyDaysLater) && $date->gte($today)) {
                                return 'warning';
                            }

                            return 'danger';
                        } catch (\Exception $e) {
                            return 'gray';
                        }
                    }),
            ])
            ->defaultPaginationPageOption(5);
    }
}
