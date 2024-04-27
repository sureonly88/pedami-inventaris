<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

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
}
