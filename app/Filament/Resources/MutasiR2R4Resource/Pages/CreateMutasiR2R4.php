<?php

namespace App\Filament\Resources\MutasiR2R4Resource\Pages;

use App\Filament\Resources\MutasiR2R4Resource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMutasiR2R4 extends CreateRecord
{
    protected static string $resource = MutasiR2R4Resource::class;

    protected static ?string $title = 'Create Mutasi Kendaraan';

    protected function handleRecordCreation(array $data): Model
    {
        $vehicle = \App\Models\data_r2r4::findOrFail($data['data_r2r4_id']);
        
        // Populate "awal" data logic securely from the backend to ensure consistency
        $data['pemegang_awal'] = $vehicle->pemegang;
        $data['departemen_awal'] = $vehicle->departemen;

        // Update the vehicle's current holder
        $vehicle->pemegang = $data['pemegang_tujuan'];
        $vehicle->departemen = $data['departemen_tujuan'];
        $vehicle->save();

        return static::getModel()::create($data);
    }
}
