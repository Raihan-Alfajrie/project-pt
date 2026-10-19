<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Record a new audit log entry into the database.
     */
    public static function log(string $aksi, string $modul, string $keterangan, ?User $user = null): ?AuditLog
    {
        try {
            $currentUser = $user ?? Auth::user();

            return AuditLog::create([
                'user_id' => $currentUser?->id,
                'username_snapshot' => $currentUser?->username ?? 'system',
                'email_snapshot' => $currentUser?->email ?? 'system@pertamina-zona4.local',
                'jabatan_snapshot' => $currentUser?->jabatan ?? 'administrator',
                'aksi' => $aksi,
                'modul' => $modul,
                'keterangan' => $keterangan,
                'waktu' => now(),
            ]);
        } catch (\Throwable $e) {
            // Fail safely without disrupting the main user flow
            report($e);
            return null;
        }
    }
}
