<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;

class InfoAssetPublicController extends Controller
{
    public function show($id)
    {
        $record = Asset::with(['ruangan', 'divisi', 'karyawan', 'penanggung_jawab'])->findOrFail($id);

        $urlGambar = null;

        if (! empty($record->gambar)) {
            $urlGambar = str_starts_with($record->gambar, 'http')
                ? $record->gambar
                : Storage::disk('minio')->url($record->gambar);
        }

        return view('public.info-asset', [
            'record' => $record,
            'urlGambar' => $urlGambar,
        ]);
    }
}