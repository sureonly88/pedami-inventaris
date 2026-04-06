<?php

namespace App\Filament\Resources\RiwayatPembayaranR2r4Resource\Pages;

use App\Filament\Resources\RiwayatPembayaranR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPembayaranR2r4s extends ListRecords
{
    protected static string $resource = RiwayatPembayaranR2r4Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
