<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';

    protected $fillable = [
        'nama',
        'kategori',
        'merek',
        'model',
        'tahun_perolehan',
        'no_seri',
        'status',
        'keterangan',
        'foto',
        'lokasi_id',
    ];

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'alat_id');
    }
}
