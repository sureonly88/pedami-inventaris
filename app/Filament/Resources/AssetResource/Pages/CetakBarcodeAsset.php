<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Resources\Pages\Page;

class CetakBarcodeAsset extends Page
{
    protected static string $resource = AssetResource::class;

    protected static string $view = 'filament.resources.asset-resource.pages.cetak-barcode-asset';
}
