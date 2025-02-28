<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\InfoAssetPublic;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/info-asset/{id}', InfoAssetPublic::class);