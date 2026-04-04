<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\data_r2r4;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class ServiceWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    protected static ?string $heading = 'Jadwal Service Kendaraan (Akan Berakhir)';
    protected static string $view = 'filament.widgets.service-table-widget';
    protected int | string | array $columnSpan = 1;

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
                    ->whereNotNull('service')
                    ->whereDate('service', '>=', now()->startOfDay())
            )
            ->columns([
                Tables\Columns\TextColumn::make('plat')
                    ->label('No Plat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nm_brg')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pemegang')
                    ->label('Pemegang'),
                Tables\Columns\TextColumn::make('service')
                    ->label('Tgl Service')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(function ($state) {
                        try {
                            if (!$state) return 'gray';
                            
                            $date = Carbon::parse($state)->startOfDay();
                            $today = now()->startOfDay();
                            $thirtyDaysLater = now()->startOfDay()->addDays(30);

                            // Jika sisa masa service lebih dari 30 hari -> HIJAU
                            if ($date->gt($thirtyDaysLater)) {
                                return 'success';
                            }
                            
                            // Jika masa service habis dalam 30 hari kedepan -> KUNING
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
