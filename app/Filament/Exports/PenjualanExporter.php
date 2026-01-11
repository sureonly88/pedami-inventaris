<?php

namespace App\Filament\Exports;

use App\Models\PenjualanR2r4;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PenjualanExporter extends Exporter
{
    protected static ?string $model = PenjualanR2r4::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('data_r2r4.plat')->label('Nopol'),
            ExportColumn::make('tgl_jual')->label('Tanggal Penjualan'),
            ExportColumn::make('hrg_jual')->label('Harga Penjualan'),
            ExportColumn::make('nm_pembeli')->label('Nama Pembeli'),

            // ✅ tambahan
            ExportColumn::make('data_r2r4.nm_brg')->label('Nama Barang'),
            ExportColumn::make('data_r2r4.thn')->label('Tahun'),
            ExportColumn::make('data_r2r4.no_rangka')->label('No Rangka'),
            ExportColumn::make('data_r2r4.no_mesin')->label('No Mesin'),
            ExportColumn::make('data_r2r4.warna')->label('Warna'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your penjualan export has completed and ' 
            . number_format($export->successful_rows) . ' ' 
            . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' 
                . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public static function shouldQueue(): bool
    {
        return false;
    }
}
