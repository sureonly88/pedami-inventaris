<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use App\Models\Karyawan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKaryawan extends EditRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $karyawan = Karyawan::with('subdivisi.divisi')->find($this->record->id);

        if ($karyawan) {
            $data['divisi_id'] = $karyawan->subdivisi?->divisi?->id;
            $data['subdivisi_id'] = $karyawan->subdivisi_id;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
