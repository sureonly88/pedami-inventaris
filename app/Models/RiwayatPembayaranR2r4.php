<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPembayaranR2r4 extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_r2r4_id',
        'jenis_pembayaran',
        'tanggal_pembayaran',
        'biaya',
        'jatuh_tempo_berikutnya',
        'keterangan',
        'bukti_foto',
    ];

    public function dataR2r4(): BelongsTo
    {
        return $this->belongsTo(data_r2r4::class, 'data_r2r4_id');
    }
}
