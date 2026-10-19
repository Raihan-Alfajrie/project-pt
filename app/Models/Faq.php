<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'kategori',
        'urutan',
    ];

    /**
     * Get readable category name.
     */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'alat_rig' => 'Operasional Rig & Alat',
            'lokasi_peta' => 'Peta & Lapangan',
            'jadwal_gantt' => 'Jadwal Operasi & Gantt',
            'akun_akses' => 'Akun & Hak Akses',
            default => ucfirst(str_replace('_', ' ', $this->kategori)),
        };
    }
}
