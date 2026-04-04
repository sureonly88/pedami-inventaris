<?php

namespace App\Filament\Resources\RiwayatServiceAcResource\Pages;

use App\Filament\Resources\RiwayatServiceAcResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatServiceAc extends EditRecord
{
    protected static string $resource = RiwayatServiceAcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
