<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiR2R4 extends Model
{
    use HasFactory;

    protected $table = 'mutasi_r2r4s';

    protected $fillable = [
        'data_r2r4_id',
        'pemegang_awal',
        'departemen_awal',
        'pemegang_tujuan',
        'departemen_tujuan',
        'tgl_mutasi',
        'deskripsi',
    ];

    public function data_r2r4()
    {
        return $this->belongsTo(data_r2r4::class, 'data_r2r4_id', 'id');
    }
}
