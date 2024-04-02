<?php

namespace App\Filament\Resources\DataR2r4Resource\Pages;

use App\Filament\Resources\DataR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataR2r4 extends EditRecord
{
    protected static string $resource = DataR2r4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
