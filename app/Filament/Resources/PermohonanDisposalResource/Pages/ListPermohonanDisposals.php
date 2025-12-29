<?php

namespace App\Filament\Resources\PermohonanDisposalResource\Pages;

use App\Filament\Resources\PermohonanDisposalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanDisposals extends ListRecords
{
    protected static string $resource = PermohonanDisposalResource::class;

    protected static ?string $title = 'Permohonan Disposal';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
