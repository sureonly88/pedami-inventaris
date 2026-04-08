<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MutasiKaryawan;
use App\Models\PensiunKaryawan;
use App\Models\Subdivisi;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'no_ktp',
        'no_hp',
        'no_rekening',
        'alamat',
        'tanggal_lahir',
        'tanggal_masuk_kerja',
        'tempat_lahir',
        'nama_bank',
        'kontak_darurat',
        'status_karyawan',
        'jabatan',
        'subdivisi_id',
        'jkel',
    ];

    public function subdivisi(): BelongsTo
    {
        return $this->belongsTo(Subdivisi::class);
    }

    public function mutasiKaryawans(): HasMany
    {
        return $this->hasMany(MutasiKaryawan::class);
    }

    public function pensiunKaryawans(): HasMany
    {
        return $this->hasMany(PensiunKaryawan::class);
    }
}
