<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Asset;

class InfoAssetPublic extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.info-asset-public';

    protected static ?string $title = 'Informasi Asset';

    protected static bool $shouldRegisterNavigation = false; 

    public $record;

    public function mount($id)
    {
        $this->record = Asset::findOrFail($id);
    }

}
