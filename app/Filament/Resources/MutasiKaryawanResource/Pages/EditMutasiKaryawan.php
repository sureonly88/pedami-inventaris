<?php

namespace App\Filament\Resources\MutasiKaryawanResource\Pages;

use App\Filament\Resources\MutasiKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutasiKaryawan extends EditRecord
{
    protected static string $resource = MutasiKaryawanResource::class;

    protected function afterSave(): void
    {
        $mutation = $this->record;
        
        // Update the employee's current position to reflect the edited mutation
        $karyawan = $mutation->karyawan;
        
        if ($karyawan) {
            $karyawan->update([
                'jabatan' => $mutation->jabatan_tujuan,
                'subdivisi_id' => $mutation->subdivisi_tujuan_id,
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
