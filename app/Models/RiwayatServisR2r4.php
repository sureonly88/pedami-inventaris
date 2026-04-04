<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\data_r2r4;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatServisR2r4 extends Model
{
    use HasFactory;

    protected $table = 'riwayat_servis_r2r4s';
    
    protected $guarded = [];

    public function dataR2r4(): BelongsTo
    {
        return $this->belongsTo(data_r2r4::class, 'data_r2r4_id');
    }
}
