<?php

namespace App\Filament\Resources\RiwayatPembayaranR2r4Resource\Pages;

use App\Filament\Resources\RiwayatPembayaranR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatPembayaranR2r4 extends EditRecord
{
    protected static string $resource = RiwayatPembayaranR2r4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
