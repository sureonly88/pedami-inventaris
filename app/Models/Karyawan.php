<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $appends = [
        'umur',
        'masa_kerja',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk_kerja' => 'date',
    ];

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'no_ktp',
        'no_hp',
        'no_rekening',
        'no_bpjs_ketenagakerjaan',
        'no_bpjs_kesehatan',
        'pendidikan_terakhir',
        'alamat',
        'tanggal_lahir',
        'tanggal_masuk_kerja',
        'tempat_lahir',
        'agama',
        'nama_bank',
        'kontak_darurat',
        'status_karyawan',
        'jabatan',
        'subdivisi_id',
        'jkel',
    ];

    public function getUmurAttribute(): ?string
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        $tanggalLahir = $this->tanggal_lahir instanceof Carbon
            ? $this->tanggal_lahir
            : Carbon::parse($this->tanggal_lahir);

        $selisih = $tanggalLahir->diff(now());

        return sprintf('%d tahun %d bulan', $selisih->y, $selisih->m);
    }

    public function getMasaKerjaAttribute(): ?string
    {
        if (! $this->tanggal_masuk_kerja) {
            return null;
        }

        $tanggalMasuk = $this->tanggal_masuk_kerja instanceof Carbon
            ? $this->tanggal_masuk_kerja
            : Carbon::parse($this->tanggal_masuk_kerja);

        $selisih = $tanggalMasuk->diff(now());

        return sprintf('%d tahun %d bulan %d hari', $selisih->y, $selisih->m, $selisih->d);
    }

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
