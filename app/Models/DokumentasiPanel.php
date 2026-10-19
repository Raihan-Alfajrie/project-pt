<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DokumentasiPanel extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi_panel';

    protected $fillable = [
        'judul',
        'deskripsi',
        'urutan',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DokumentasiItem::class, 'panel_id')->orderBy('urutan');
    }
}
