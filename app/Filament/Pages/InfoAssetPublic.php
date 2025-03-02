<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Asset;
use Illuminate\Support\Facades\Storage;

class InfoAssetPublic extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.info-asset-public';

    protected static ?string $title = 'KOPERASI KONSUMEN PEDAMI';

    protected static bool $shouldRegisterNavigation = false; 

    public $record;

    public $urlGambar;

    public function mount($id)
    {
        $this->record = Asset::findOrFail($id);
        $this->urlGambar = Storage::url(Asset::findOrFail($id)->gambar);
    }

}
