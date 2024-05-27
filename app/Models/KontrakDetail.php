<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Kontrak;
use App\Models\data_r2r4;

class KontrakDetail extends Model
{
    use HasFactory;

    public function kontrak(): BelongsTo
    {
        return $this->belongsTo(Kontrak::class);
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(data_r2r4::class);
    }
}
