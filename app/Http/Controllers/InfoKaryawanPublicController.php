<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Storage;

class InfoKaryawanPublicController extends Controller
{
    public function show(Karyawan $karyawan)
    {
        $karyawan->loadMissing(['subdivisi.divisi', 'user']);

        $urlFoto = null;

        if (! empty($karyawan->foto)) {
            $urlFoto = str_starts_with($karyawan->foto, 'http')
                ? $karyawan->foto
                : Storage::disk('minio')->url(ltrim($karyawan->foto, '/'));
        }

        return view('public.info-karyawan', [
            'record' => $karyawan,
            'urlFoto' => $urlFoto,
        ]);
    }
}