<?php

namespace App\Filament\Resources\MutasiAssetResource\Pages;

use App\Filament\Resources\MutasiAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;

class CreateMutasiAsset extends CreateRecord
{
    protected static string $resource = MutasiAssetResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        $data['ruangan_id_a'] = explode("-",$data['ruangan_id_a'])[0];
        $data['penanggung_jawab_id_a'] = explode("-",$data['penanggung_jawab_id_a'])[0];
        $data['karyawan_id_a'] = explode("-",$data['karyawan_id_a'])[0];

        $asset = Asset::find($data['asset_id']);
        $asset->ruangan_id = $data['ruangan_id_t'];
        $asset->penanggung_jawab_id = $data['penanggung_jawab_id_t'];
        $asset->karyawan_id = $data['karyawan_id_t'];
        $asset->save();

        return static::getModel()::create($data);
    }

    // protected function afterSave(): void
    // {
    //     // Lakukan query update tambahan

    //     $asset = Asset::find($this->record->asset_id);
    //     $asset->ruangan_id = $this->record->ruangan_id_t;
    //     $asset->penanggung_jawab_id = $this->record->penanggung_jawab_id_t;
    //     $asset->karyawan_id = $this->record->karyawan_id_t;
    //     $asset->save();
    // }
}
