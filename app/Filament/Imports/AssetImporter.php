<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('kode_asset')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama_asset')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('gambar')
                ->rules(['max:100']),
            ImportColumn::make('tgl_beli')
                ->rules(['date']),
            ImportColumn::make('hrg_beli')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('kelompok_asset')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('ruangan')
                ->relationship(),
            ImportColumn::make('penanggung_jawab')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('pemakai')
                ->rules(['max:50']),
            ImportColumn::make('divisi')
                ->rules(['max:100']),
            ImportColumn::make('status_barang')
                ->requiredMapping()
                ->rules(['required', 'max:15']),
            ImportColumn::make('karyawan')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('foto')
                ->rules(['max:100']),
            ImportColumn::make('deskripsi')
                ->rules(['max:250']),
            ImportColumn::make('kode_nama')
                ->rules(['max:100']),
        ];
    }

    public function resolveRecord(): ?Asset
    {
        // return Asset::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Asset();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your asset import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
