<?php

namespace App\Filament\Imports;

use App\Models\Karyawan;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class KaryawanImporter extends Importer
{
    protected static ?string $model = Karyawan::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nik')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama_karyawan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('no_ktp')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('no_hp')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('no_rekening')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('no_bpjs_ketenagakerjaan')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('no_bpjs_kesehatan')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('pendidikan_terakhir')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('alamat'),
            ImportColumn::make('tempat_lahir')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('tanggal_lahir')
                ->rules(['nullable', 'date']),
            ImportColumn::make('tanggal_masuk_kerja')
                ->rules(['nullable', 'date']),
            ImportColumn::make('nama_bank')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('kontak_darurat')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('status_karyawan')
                ->requiredMapping()
                ->rules(['required', 'in:Aktif,Pensiun,Nonaktif']),
            ImportColumn::make('jabatan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('subdivisi_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'exists:subdivisis,id']),
            ImportColumn::make('jkel')
                ->requiredMapping()
                ->rules(['required', 'in:Laki-Laki,Perempuan,L/P']),
        ];
    }

    public function resolveRecord(): ?Karyawan
    {
        return Karyawan::firstOrNew([
            'nik' => $this->data['nik'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data karyawan selesai, ' . number_format($import->successful_rows) . ' baris berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}