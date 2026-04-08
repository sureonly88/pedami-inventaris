<?php

namespace App\Filament\Resources\PensiunKaryawanResource\Pages;

use App\Filament\Resources\PensiunKaryawanResource;
use App\Models\Karyawan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPensiunKaryawan extends EditRecord
{
    protected static string $resource = PensiunKaryawanResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $karyawan = Karyawan::with('subdivisi.divisi')->find($data['karyawan_id'] ?? null);

        if ($karyawan) {
            $data['jabatan_terakhir'] = $karyawan->jabatan;
            $data['divisi_terakhir_id'] = $karyawan->subdivisi?->divisi?->id;
            $data['subdivisi_terakhir_id'] = $karyawan->subdivisi_id;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $pensiun = $this->record;
        $karyawan = $pensiun->karyawan;

        if ($karyawan) {
            $karyawan->update([
                'status_karyawan' => 'Pensiun',
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
