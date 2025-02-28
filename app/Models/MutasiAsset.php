<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Karyawan;
use App\Models\Ruangan;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiAsset extends Model
{
    use HasFactory;

    protected $table = 'mutasi_assets';

    public function ruangan_a(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id_a', 'id');
    }

    public function penanggung_jawab_a(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'penanggung_jawab_id_a', 'id');
    }

    public function karyawan_a(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id_a', 'id');
    }

    public function ruangan_t(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id_t', 'id');
    }

    public function penanggung_jawab_t(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'penanggung_jawab_id_t', 'id');
    }

    public function karyawan_t(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id_t', 'id');
    }
    
}
