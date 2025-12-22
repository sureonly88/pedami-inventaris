<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanDisposal extends Model
{
    use HasFactory;

     protected $table = 'permohonan_disposal';

     public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
