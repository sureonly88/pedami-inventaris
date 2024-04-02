<?php

namespace App\Filament\Resources\SubdivisiResource\Pages;

use App\Filament\Resources\SubdivisiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubdivisi extends EditRecord
{
    protected static string $resource = SubdivisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
