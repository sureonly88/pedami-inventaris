<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Livewire::setUpdateRoute(function ($handle) {
        //     return Route::post('pedami-inventaris/public/livewire/update', $handle);
        // });

        // Livewire::setScriptRoute(function ($handle) {
        //     return Route::get('pedami-inventaris/public/livewire/livewire.js', $handle);
        // });

        Carbon::setLocale('id');

        Model::unguard();
    }
}
