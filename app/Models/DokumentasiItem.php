<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DokumentasiItem extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi_item';

    protected $fillable = [
        'panel_id',
        'label',
        'gaya',
        'warna',
        'urutan',
    ];

    public function panel(): BelongsTo
    {
        return $this->belongsTo(DokumentasiPanel::class, 'panel_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kategori_id');
    }
}
