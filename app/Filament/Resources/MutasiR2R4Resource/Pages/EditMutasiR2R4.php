<?php

namespace App\Filament\Resources\MutasiR2R4Resource\Pages;

use App\Filament\Resources\MutasiR2R4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutasiR2R4 extends EditRecord
{
    protected static string $resource = MutasiR2R4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
