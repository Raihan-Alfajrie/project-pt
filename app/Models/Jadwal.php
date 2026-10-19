<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'alat_id',
        'lokasi_id',
        'kategori_id',
        'label',
        'status',
        'periode_mulai',
        'durasi_bulan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'durasi_bulan' => 'integer',
        ];
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(DokumentasiItem::class, 'kategori_id');
    }

    /**
     * Get 0-based month index on the 19-month timeline (Jun 2026 = 0).
     */
    public function getBulanMulaiIndexAttribute(): int
    {
        if (!$this->periode_mulai) {
            return 0;
        }
        $y = intval($this->periode_mulai->format('Y'));
        $m = intval($this->periode_mulai->format('n'));
        $idx = ($y - 2026) * 12 + $m - 6;
        return max(0, min(18, $idx));
    }

    /**
     * Human readable period label (e.g. Jun-26 — Aug-26 (3 bulan)).
     */
    public function getPeriodeLabelAttribute(): string
    {
        $months = [
            'Jun-26', 'Jul-26', 'Aug-26', 'Sep-26', 'Oct-26', 'Nov-26', 'Dec-26',
            'Jan-27', 'Feb-27', 'Mar-27', 'Apr-27', 'May-27', 'Jun-27', 'Jul-27',
            'Aug-27', 'Sep-27', 'Oct-27', 'Nov-27', 'Dec-27'
        ];
        $start = $this->bulan_mulai_index;
        $end = min(18, $start + $this->durasi_bulan - 1);
        return "{$months[$start]} — {$months[$end]} ({$this->durasi_bulan} bulan)";
    }
}
