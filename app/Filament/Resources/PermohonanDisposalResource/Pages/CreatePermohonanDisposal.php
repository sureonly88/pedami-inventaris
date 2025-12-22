<?php

namespace App\Filament\Resources\PermohonanDisposalResource\Pages;

use App\Filament\Resources\PermohonanDisposalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePermohonanDisposal extends CreateRecord
{
    protected static string $resource = PermohonanDisposalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dibuat_oleh'] = Auth::id();

        return $data;
    }
}
