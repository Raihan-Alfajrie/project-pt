<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    /**
     * Judul notifikasi.
     */
    public function getJudulAttribute(): string
    {
        return (string) ($this->data['judul'] ?? $this->data['title'] ?? 'Pemberitahuan');
    }

    /**
     * Pesan / isi ringkasan notifikasi.
     */
    public function getPesanAttribute(): string
    {
        return (string) ($this->data['pesan'] ?? $this->data['message'] ?? '');
    }

    /**
     * Modul asal notifikasi (alat, jadwal, lokasi, import_export, user, sistem).
     */
    public function getModulAttribute(): string
    {
        return (string) ($this->data['modul'] ?? 'sistem');
    }

    /**
     * Label modul bahasa Indonesia yang rapi.
     */
    public function getModulLabelAttribute(): string
    {
        return match ($this->modul) {
            'alat' => 'Alat & Rig',
            'jadwal' => 'Jadwal Operasi',
            'lokasi' => 'Lokasi Lapangan',
            'import_export' => 'Import / Export',
            'user' => 'Pengguna & Akun',
            default => 'Sistem',
        };
    }

    /**
     * Tingkat / tipe notifikasi (info, success, warning, danger).
     */
    public function getTipeAttribute(): string
    {
        return (string) ($this->data['tipe'] ?? 'info');
    }

    /**
     * Tautan tujuan ketika notifikasi diklik.
     */
    public function getUrlAttribute(): string
    {
        return (string) ($this->data['url'] ?? '#');
    }

    /**
     * Ikon notifikasi (misal: alert-triangle, calendar, tool, file-spreadsheet, user-check, bell).
     */
    public function getIconAttribute(): string
    {
        return (string) ($this->data['icon'] ?? match ($this->modul) {
            'alat' => 'tool',
            'jadwal' => 'calendar',
            'lokasi' => 'map-pin',
            'import_export' => 'file-spreadsheet',
            'user' => 'user-check',
            default => 'bell',
        });
    }

    /**
     * Format tampilan waktu dalam bahasa Indonesia.
     */
    public function getWaktuDisplayAttribute(): string
    {
        if (! $this->created_at) {
            return '-';
        }

        $diffHours = $this->created_at->diffInHours(now());
        if ($diffHours < 24) {
            return $this->created_at->locale('id')->diffForHumans();
        }

        return $this->created_at->translatedFormat('d M Y, H:i');
    }

    /**
     * Cek apakah notifikasi sudah dibaca.
     */
    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }
}
