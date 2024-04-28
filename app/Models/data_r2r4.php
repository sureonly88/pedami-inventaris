<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kontrak;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class data_r2r4 extends Model
{
    use HasFactory;

    public function kontrak(): BelongsTo
    {
        return $this->belongsTo(Kontrak::class);
    }
}
