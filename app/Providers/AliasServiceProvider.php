<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AliasServiceProvider extends ServiceProvider
{
    /**π
     * Register services.
     */
    public function register(): void
    {
        //

        $loader = AliasLoader::getInstance();

        // Add your aliases
        //$loader->alias('DNS1D', Milon\Barcode\Facades\DNS1DFacade::class);
        //$loader->alias('DNS2D', Milon\Barcode\Facades\DNS2DFacade::class, );

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
