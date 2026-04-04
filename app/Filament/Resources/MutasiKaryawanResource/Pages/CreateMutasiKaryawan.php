<?php

namespace App\Filament\Resources\MutasiKaryawanResource\Pages;

use App\Filament\Resources\MutasiKaryawanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMutasiKaryawan extends CreateRecord
{
    protected static string $resource = MutasiKaryawanResource::class;
    protected static ?string $title = 'Tambah Mutasi Karyawan';

    protected function afterCreate(): void
    {
        $mutation = $this->record;
        
        // Update the employee's current position
        $karyawan = $mutation->karyawan;
        
        if ($karyawan) {
            $karyawan->update([
                'jabatan' => $mutation->jabatan_tujuan,
                'subdivisi_id' => $mutation->subdivisi_tujuan_id,
            ]);
        }
    }
}
