<?php

namespace App\Filament\Resources\MutasiAssetResource\Pages;

use App\Filament\Resources\MutasiAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMutasiAssets extends ListRecords
{
    protected static string $resource = MutasiAssetResource::class;
    protected static ?string $title = 'Mutasi Aset';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
