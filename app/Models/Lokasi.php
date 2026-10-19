<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'kode',
        'nama_tempat',
        'provinsi',
        'kota',
        'kecamatan',
        'keterangan',
        'tanggal_dipetakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dipetakan' => 'date',
        ];
    }

    public function alats(): HasMany
    {
        return $this->hasMany(Alat::class, 'lokasi_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'lokasi_id');
    }

    /**
     * Get list of sumur for this location (from related jadwals and keterangan).
     */
    public function getSumurListAttribute(): array
    {
        $sumurs = [];

        // 1. From related jadwals
        if ($this->relationLoaded('jadwals') || $this->jadwals()->exists()) {
            foreach ($this->jadwals as $jadwal) {
                $label = $jadwal->label;
                // Parse well codes like MJ-A21, BKB-AR.016, KRG-PN2 / PN4, ROSELLA SHALLOW SOUTH
                if (preg_match('/^([A-Za-z0-9_\-\.\/\s]+?)(?:\s*\(|\s+[\d\.]+-IP|\s*—|$)/i', $label, $m)) {
                    $candidate = trim($m[1]);
                    // Ignore common operational prefixes
                    if (!preg_match('/^(Pekerjaan|Proses|Konstruksi|Kontrak|Amandemen)/i', $candidate)) {
                        $parts = preg_split('/\s*\/\s*/', $candidate);
                        foreach ($parts as $p) {
                            $p = trim($p);
                            if (strlen($p) >= 2 && !in_array($p, $sumurs)) {
                                $sumurs[] = $p;
                            }
                        }
                    }
                }
            }
        }

        // 2. Parse explicit sumurs from keterangan if mentioned (e.g. Cluster sumur MJ-A9, MJ-A21... or Sumur TLJT-04)
        if (!empty($this->keterangan)) {
            if (preg_match('/(?:Cluster sumur|Sumur)\s+([A-Za-z0-9_\-,\.\/\s]+?)(?:\.|$)/i', $this->keterangan, $km)) {
                $rawList = $km[1];
                $items = preg_split('/[,\/]\s*|\s+dan\s+/i', $rawList);
                foreach ($items as $item) {
                    $item = trim($item, " \t\n\r\0\x0B.");
                    if (strlen($item) >= 2 && !in_array($item, $sumurs) && !preg_match('/^(pengganti|eksplorasi)/i', $item)) {
                        $sumurs[] = $item;
                    }
                }
            }
        }

        return array_values($sumurs);
    }

    /**
     * Get list of rigs operating at this location.
     */
    public function getRigListAttribute(): array
    {
        if ($this->relationLoaded('alats')) {
            return $this->alats->where('kategori', 'rig')->pluck('nama')->values()->toArray();
        }
        return $this->alats()->where('kategori', 'rig')->pluck('nama')->values()->toArray();
    }
}

