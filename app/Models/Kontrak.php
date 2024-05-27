<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\KontrakDetail;

class Kontrak extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'list_kendaraan' => 'array',
        ];
    }

    public function kontrakDetails(): HasMany
    {
        return $this->hasMany(KontrakDetail::class);
    }
}
