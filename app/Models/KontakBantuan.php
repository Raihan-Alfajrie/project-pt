<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakBantuan extends Model
{
    use HasFactory;

    protected $table = 'kontak_bantuan';

    protected $attributes = [
        'kategori' => 'bantuan',
        'status' => 'baru',
    ];

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'instansi',
        'kategori',
        'perihal',
        'pesan',
        'status',
        'catatan_admin',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Label kategori yang ramah dibaca.
     */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'bantuan' => 'Permohonan Bantuan / Layanan',
            'pertanyaan_faq' => 'Pertanyaan / FAQ Publik',
            'operasional' => 'Operasional Lapangan & Rig',
            'saran' => 'Kritik & Saran',
            default => ucfirst(str_replace('_', ' ', (string) $this->kategori)),
        };
    }

    /**
     * Label status yang ramah dibaca.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Baru',
            'dibaca' => 'Sudah Dibaca',
            'dibalas' => 'Sudah Dibalas',
            'diarsipkan' => 'Diarsipkan',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Scope: Hanya pesan dengan status baru.
     */
    public function scopeBaru(Builder $query): Builder
    {
        return $query->where('status', 'baru');
    }

    /**
     * Scope: Filter berdasarkan kategori.
     */
    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope: Pencarian berdasarkan kata kunci.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('nama', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('perihal', 'like', "%{$term}%")
                ->orWhere('pesan', 'like', "%{$term}%")
                ->orWhere('instansi', 'like', "%{$term}%");
        });
    }
}
