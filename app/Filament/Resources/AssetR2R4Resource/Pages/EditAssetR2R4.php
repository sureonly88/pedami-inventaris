<?php

namespace App\Filament\Resources\AssetR2R4Resource\Pages;

use App\Filament\Resources\AssetR2R4Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetR2R4 extends EditRecord
{
    protected static string $resource = AssetR2R4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
