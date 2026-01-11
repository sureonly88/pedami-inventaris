<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\data_r2r4;


class PenjualanR2r4 extends Model
{
    use HasFactory;

    public function data_r2r4(): BelongsTo
    {
        return $this->belongsTo(data_r2r4::class);
    }

}
