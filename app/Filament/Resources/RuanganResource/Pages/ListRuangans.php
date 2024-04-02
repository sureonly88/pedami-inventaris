<?php

namespace App\Filament\Resources\RuanganResource\Pages;

use App\Filament\Resources\RuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListRuangans extends ListRecords
{
    protected static string $resource = RuanganResource::class;

    protected static ?string $title = 'Manage Data Ruangan';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->requiresConfirmation()
                ->action(function () {
                    
                }),
            Actions\CreateAction::make(),
        ];
    }
}
