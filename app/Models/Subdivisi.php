<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subdivisi extends Model
{
    use HasFactory;

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}
