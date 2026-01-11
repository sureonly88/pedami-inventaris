<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\InfoAssetPublic;
use App\Http\Controllers\PermohonanDisposalCetakController;
use App\Exports\PenjualanDirectExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/info-asset/{id}', InfoAssetPublic::class);

Route::get(
    '/permohonan-disposal/{record}/cetak',
    [PermohonanDisposalCetakController::class, 'cetak']
)->name('permohonan-disposal.cetak');

Route::get('/download/penjualan-selected/{ids}', function ($ids) {
    $ids = explode(',', $ids);

    return Excel::download(
        new PenjualanDirectExport($ids),
        'data-penjualan-terpilih.xlsx'
    );
})->name('download.penjualan.selected');