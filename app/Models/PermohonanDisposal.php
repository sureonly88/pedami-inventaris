<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Karyawan;

class PermohonanDisposal extends Model
{
    use HasFactory;

     protected $table = 'permohonan_disposal';

     public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(
            Karyawan::class,
            'dibuat_oleh', // foreign key di tabel ini
            'id'           // primary key di tabel karyawan
        );
    }
}
