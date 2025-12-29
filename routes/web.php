<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\InfoAssetPublic;
use App\Http\Controllers\PermohonanDisposalCetakController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/info-asset/{id}', InfoAssetPublic::class);

Route::get(
    '/permohonan-disposal/{record}/cetak',
    [PermohonanDisposalCetakController::class, 'cetak']
)->name('permohonan-disposal.cetak');