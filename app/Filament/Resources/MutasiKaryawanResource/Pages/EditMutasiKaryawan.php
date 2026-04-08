<?php

namespace App\Filament\Resources\MutasiKaryawanResource\Pages;

use App\Filament\Resources\MutasiKaryawanResource;
use App\Models\Karyawan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutasiKaryawan extends EditRecord
{
    protected static string $resource = MutasiKaryawanResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $karyawan = Karyawan::with('subdivisi.divisi')->find($data['karyawan_id'] ?? null);

        if ($karyawan) {
            $data['jabatan_asal'] = $karyawan->jabatan;
            $data['divisi_asal_id'] = $karyawan->subdivisi?->divisi?->id;
            $data['subdivisi_asal_id'] = $karyawan->subdivisi_id;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $mutation = $this->record;
        
        // Update the employee's current position to reflect the edited mutation
        $karyawan = $mutation->karyawan;
        
        if ($karyawan) {
            $karyawan->update([
                'jabatan' => $mutation->jabatan_tujuan,
                'subdivisi_id' => $mutation->subdivisi_tujuan_id,
                'status_karyawan' => 'Aktif',
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
