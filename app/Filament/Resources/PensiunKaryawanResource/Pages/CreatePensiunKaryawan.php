<?php

namespace App\Filament\Resources\PensiunKaryawanResource\Pages;

use App\Filament\Resources\PensiunKaryawanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePensiunKaryawan extends CreateRecord
{
    protected static string $resource = PensiunKaryawanResource::class;
    protected static ?string $title = 'Tambah Data Pensiun';

    protected function afterCreate(): void
    {
        $pensiun = $this->record;
        $karyawan = $pensiun->karyawan;

        if ($karyawan) {
            $karyawan->update([
                'status_karyawan' => 'Pensiun',
            ]);
        }
    }
}
