<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportExportLog extends Model
{
    use HasFactory;

    protected $table = 'import_export_logs';

    protected $fillable = [
        'user_id',
        'jenis',
        'modul',
        'nama_file',
        'baris_berhasil',
        'baris_total',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formatted datetime string in Indonesian locale (e.g., "11 Okt 2026, 13:30").
     */
    public function getWaktuDisplayAttribute(): string
    {
        return $this->created_at ? $this->created_at->translatedFormat('d M Y, H:i') : '-';
    }

    /**
     * Human-friendly label for module.
     */
    public function getModulLabelAttribute(): string
    {
        return match ($this->modul) {
            'alat' => 'Alat',
            'lokasi' => 'Lokasi',
            'jadwal' => 'Jadwal',
            'audit_log' => 'Audit Log',
            default => ucfirst($this->modul),
        };
    }

    /**
     * Formatted string for row count (e.g., "5 / 6" or "32").
     */
    public function getBarisDisplayAttribute(): string
    {
        if ($this->baris_total > 0 && $this->baris_total !== $this->baris_berhasil) {
            return "{$this->baris_berhasil} / {$this->baris_total}";
        }

        return (string) $this->baris_berhasil;
    }

    /**
     * Capitalized status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sukses' => 'Sukses',
            'sebagian' => 'Sebagian',
            'gagal' => 'Gagal',
            default => ucfirst($this->status),
        };
    }

    /**
     * Capitalized jenis label.
     */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'import' => 'Import',
            'export' => 'Export',
            default => ucfirst($this->jenis),
        };
    }
}
