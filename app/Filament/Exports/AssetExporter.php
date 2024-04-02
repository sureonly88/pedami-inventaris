<?php

namespace App\Filament\Exports;

use App\Models\Asset;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssetExporter extends Exporter
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make("kode_asset"),
            ExportColumn::make("nama_asset"),
            ExportColumn::make("kelompok_asset"),
            ExportColumn::make("penanggung_jawab"),
            // ExportColumn::make("karyawan.nama_karyawan"),
            // ExportColumn::make("karyawan.subdivisi.divisi.nama_divisi"),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
