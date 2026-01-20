<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kontrak;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\KontrakDetail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class data_r2r4 extends Model
{
    use HasFactory;

    public function kontrak(): BelongsTo
    {
        return $this->belongsTo(Kontrak::class);
    }

    public function kontrak_detail(): HasMany
    {
        return $this->hasMany(KontrakDetail::class);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['kontrak_detail.kontrak']);
    }
}
