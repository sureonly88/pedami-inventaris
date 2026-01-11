<?php

namespace App\Filament\Resources\PenjualanR2r4Resource\Pages;

use App\Filament\Resources\PenjualanR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenjualanR2r4s extends ListRecords
{
    protected static string $resource = PenjualanR2r4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
