<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiKaryawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id', 'tgl_mutasi', 'jabatan_asal', 'jabatan_tujuan',
        'divisi_asal_id', 'subdivisi_asal_id', 'divisi_tujuan_id', 'subdivisi_tujuan_id',
        'alasan', 'no_sk',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function divisiAsal(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_asal_id');
    }

    public function subdivisiAsal(): BelongsTo
    {
        return $this->belongsTo(Subdivisi::class, 'subdivisi_asal_id');
    }

    public function divisiTujuan(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_tujuan_id');
    }

    public function subdivisiTujuan(): BelongsTo
    {
        return $this->belongsTo(Subdivisi::class, 'subdivisi_tujuan_id');
    }
}
