<?php

namespace App\Filament\Resources\PermohonanDisposalResource\Pages;

use App\Filament\Resources\PermohonanDisposalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermohonanDisposal extends EditRecord
{
    protected static string $resource = PermohonanDisposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
