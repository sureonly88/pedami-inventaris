<?php

namespace App\Filament\Resources\SubdivisiResource\Pages;

use App\Filament\Resources\SubdivisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubdivisis extends ListRecords
{
    protected static string $resource = SubdivisiResource::class;

    protected static ?string $title = 'Subdivisi';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
