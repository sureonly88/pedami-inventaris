<?php

namespace App\Filament\Resources\RiwayatServisR2r4Resource\Pages;

use App\Filament\Resources\RiwayatServisR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatServisR2r4 extends EditRecord
{
    protected static string $resource = RiwayatServisR2r4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
