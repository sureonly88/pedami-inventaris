<?php

namespace App\Filament\Resources\DataR2r4Resource\Pages;

use App\Filament\Resources\DataR2r4Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataR2r4s extends ListRecords
{
    protected static string $resource = DataR2r4Resource::class;

    protected static ?string $title = 'Data Roda 2 & Roda 4';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
