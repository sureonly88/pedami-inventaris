<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PensiunKaryawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id', 'tgl_pensiun', 'jenis_pensiun', 'no_sk',
        'jabatan_terakhir', 'divisi_terakhir_id', 'subdivisi_terakhir_id',
        'pesangon', 'keterangan',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function divisiTerakhir(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_terakhir_id');
    }

    public function subdivisiTerakhir(): BelongsTo
    {
        return $this->belongsTo(Subdivisi::class, 'subdivisi_terakhir_id');
    }
}
