<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\Jadwal;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Kirim notifikasi ke sekumpulan pengguna.
     */
    public static function sendToUsers(iterable $users, array $data): void
    {
        foreach ($users as $user) {
            Notification::create([
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => $data,
                'read_at' => null,
            ]);
        }
    }

    /**
     * Ambil pengguna yang memiliki akses ke modul tertentu (Admin + Pekerja berizin).
     */
    public static function getUsersForModule(string $modul): Collection
    {
        return User::with('permissions')
            ->get()
            ->filter(fn (User $user) => $user->hasModuleAccess($modul));
    }

    /**
     * Ambil seluruh administrator.
     */
    public static function getAdmins(): Collection
    {
        return User::where('jabatan', 'administrator')->get();
    }

    /**
     * Pemicu: Alat baru ditambahkan.
     */
    public static function notifyAlatCreated(Alat $alat): void
    {
        $recipients = self::getUsersForModule('alat');
        self::sendToUsers($recipients, [
            'judul' => "Alat Baru: {$alat->nama}",
            'pesan' => "Alat baru dengan nomor seri {$alat->no_seri} ({$alat->merek}) telah ditambahkan ke sistem.",
            'modul' => 'alat',
            'tipe' => 'info',
            'url' => '/alat',
            'icon' => 'tool',
        ]);
    }

    /**
     * Pemicu: Status alat berubah (khusus bermasalah / perbaikan).
     */
    public static function notifyAlatStatusChanged(Alat $alat, string $oldStatus, string $newStatus): void
    {
        if (! in_array($newStatus, ['bermasalah', 'perbaikan'])) {
            return;
        }

        $recipients = self::getUsersForModule('alat');
        self::sendToUsers($recipients, [
            'judul' => "Perhatian: {$alat->nama} Bermasalah",
            'pesan' => "Status operasional {$alat->nama} diubah dari '{$oldStatus}' menjadi '{$newStatus}'.",
            'modul' => 'alat',
            'tipe' => $newStatus === 'bermasalah' ? 'danger' : 'warning',
            'url' => '/alat',
            'icon' => 'alert-triangle',
        ]);
    }

    /**
     * Pemicu: Jadwal operasi baru ditambahkan.
     */
    public static function notifyJadwalCreated(Jadwal $jadwal): void
    {
        $jadwal->loadMissing(['alat', 'lokasi']);
        $rigName = $jadwal->alat?->nama ?? 'Rig';
        $recipients = self::getUsersForModule('jadwal');

        self::sendToUsers($recipients, [
            'judul' => "Jadwal Operasi Baru: {$jadwal->label}",
            'pesan' => "Jadwal operasi baru telah ditambahkan untuk {$rigName}.",
            'modul' => 'jadwal',
            'tipe' => 'info',
            'url' => '/jadwal',
            'icon' => 'calendar',
        ]);
    }

    /**
     * Pemicu: Status jadwal operasi berubah (khusus kendala).
     */
    public static function notifyJadwalStatusChanged(Jadwal $jadwal, string $oldStatus, string $newStatus): void
    {
        if ($newStatus !== 'kendala') {
            return;
        }

        $recipients = self::getUsersForModule('jadwal');
        self::sendToUsers($recipients, [
            'judul' => "Kendala Operasi: {$jadwal->label}",
            'pesan' => "Status jadwal operasi {$jadwal->label} ditandai memiliki kendala operasional.",
            'modul' => 'jadwal',
            'tipe' => 'warning',
            'url' => '/jadwal',
            'icon' => 'alert-triangle',
        ]);
    }

    /**
     * Pemicu: Proses Import data massal selesai.
     */
    public static function notifyImportCompleted(User $user, string $modul, int $successCount, int $totalCount, string $status): void
    {
        $modulLabel = match ($modul) {
            'alat' => 'Alat & Rig',
            'lokasi' => 'Lokasi Lapangan',
            'jadwal' => 'Jadwal Operasi',
            default => ucfirst($modul),
        };

        $isSuccess = $status === 'sukses';

        $data = [
            'judul' => $isSuccess ? "Impor Data {$modulLabel} Berhasil" : "Impor Data {$modulLabel} Selesai Sebagian",
            'pesan' => $isSuccess
                ? "Sebanyak {$successCount} data {$modulLabel} berhasil diproses dan disimpan."
                : "Sebanyak {$successCount} dari {$totalCount} baris data {$modulLabel} berhasil diproses.",
            'modul' => 'import_export',
            'tipe' => $isSuccess ? 'success' : 'warning',
            'url' => '/import-export',
            'icon' => 'file-spreadsheet',
        ];

        // Kirim ke admin pelaksana dan admin lainnya
        self::sendToUsers([$user], $data);
    }

    /**
     * Pemicu: Pengguna baru terdaftar.
     */
    public static function notifyUserRegistered(User $newUser): void
    {
        $admins = self::getAdmins()->where('id', '!=', $newUser->id);
        self::sendToUsers($admins, [
            'judul' => "Pengguna Baru Terdaftar",
            'pesan' => "Akun {$newUser->nama} (@{$newUser->username}) telah didaftarkan sebagai {$newUser->jabatan}.",
            'modul' => 'user',
            'tipe' => 'info',
            'url' => '/users',
            'icon' => 'user-check',
        ]);
    }

    /**
     * Pemicu: Hak akses permission pengguna diperbarui oleh admin.
     */
    public static function notifyUserPermissionsUpdated(User $targetUser): void
    {
        self::sendToUsers([$targetUser], [
            'judul' => "Hak Akses Diperbarui",
            'pesan' => "Hak akses modul operasional akun Anda telah diperbarui oleh Administrator.",
            'modul' => 'user',
            'tipe' => 'info',
            'url' => '/profil',
            'icon' => 'user-check',
        ]);
    }
}
