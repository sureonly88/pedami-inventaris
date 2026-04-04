<?php

namespace App\Filament\Resources\PensiunKaryawanResource\Pages;

use App\Filament\Resources\PensiunKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPensiunKaryawan extends EditRecord
{
    protected static string $resource = PensiunKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
