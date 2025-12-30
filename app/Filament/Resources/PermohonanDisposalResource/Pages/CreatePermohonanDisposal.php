<?php

namespace App\Filament\Resources\PermohonanDisposalResource\Pages;

use App\Filament\Resources\PermohonanDisposalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;

class CreatePermohonanDisposal extends CreateRecord
{
    protected static string $resource = PermohonanDisposalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dibuat_oleh'] = auth()->user()->karyawan->id;
        $data['verif_manager'] = 0;
        $data['verif_ketua'] = 0;

        // Ambil Ketua
        $ketua = Karyawan::where('jabatan', 'Ketua')->first();
        $data['ketua_id'] = $ketua?->id;

        // Ambil Manager
        $manager = Karyawan::where('jabatan', 'Manager')->first();
        $data['manager_id'] = $manager?->id;

        return $data;
    }
}
