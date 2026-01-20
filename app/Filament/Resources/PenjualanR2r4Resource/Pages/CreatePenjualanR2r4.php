<?php

namespace App\Filament\Resources\PenjualanR2r4Resource\Pages;

use App\Filament\Resources\PenjualanR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\data_r2r4;

class CreatePenjualanR2r4 extends CreateRecord
{
    protected static string $resource = PenjualanR2r4Resource::class;

    protected function afterCreate(): void
    {
        data_r2r4::where('id', $this->record->data_r2r4_id)
            ->update(['stat' => 'Terjual']);
    }
}
