<?php

namespace App\Filament\Resources\PenjualanR2r4Resource\Pages;

use App\Filament\Resources\PenjualanR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenjualanR2r4 extends EditRecord
{
    protected static string $resource = PenjualanR2r4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
