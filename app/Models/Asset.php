<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Divisi;
use Illuminate\Support\Facades\Storage;

class Asset extends Model
{
    use HasFactory;

    public function getGambarUrlAttribute(): ?string
    {
        if (!$this->gambar) return null;

        return Storage::disk('minio')->temporaryUrl(
            $this->gambar,
            now()->addMinutes(10) // URL berlaku 10 menit
        );
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function penanggung_jawab(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}
