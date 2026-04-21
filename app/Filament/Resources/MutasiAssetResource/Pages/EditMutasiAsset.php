<?php

namespace App\Filament\Resources\MutasiAssetResource\Pages;

use App\Filament\Resources\MutasiAssetResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutasiAsset extends EditRecord
{
    protected static string $resource = MutasiAssetResource::class;

    protected static ?string $title = 'Edit Mutasi Aset';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['gambar_awal'] = filled($data['gambar_awal'] ?? null) ? [$data['gambar_awal']] : [];
        $data['gambar_terbaru'] = filled($data['gambar_terbaru'] ?? null) ? [$data['gambar_terbaru']] : [];

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $asset = Asset::findOrFail($record->asset_id);
        $gambarTerbaru = $data['gambar_terbaru'] ?? null;

        if (is_array($gambarTerbaru)) {
            $gambarTerbaru = count($gambarTerbaru) ? reset($gambarTerbaru) : null;
        }

        $data['asset_id'] = $record->asset_id;
        $data['gambar_awal'] = $record->gambar_awal ?: $asset->gambar;
        $data['gambar_terbaru'] = $gambarTerbaru;

        $asset->ruangan_id = $data['ruangan_id_t'];
        $asset->penanggung_jawab_id = $data['penanggung_jawab_id_t'];
        $asset->karyawan_id = $data['karyawan_id_t'];
        $asset->gambar = $gambarTerbaru ?: $asset->gambar;
        $asset->save();

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
