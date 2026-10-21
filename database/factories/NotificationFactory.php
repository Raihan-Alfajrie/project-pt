<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'type' => 'App\Notifications\SystemNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => User::factory(),
            'data' => [
                'judul' => 'Notifikasi Sistem',
                'pesan' => 'Ini adalah pesan uji notifikasi.',
                'modul' => 'sistem',
                'tipe' => 'info',
                'url' => '#',
                'icon' => 'bell',
            ],
            'read_at' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn () => [
            'read_at' => now(),
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn () => [
            'read_at' => null,
        ]);
    }

    public function alat(string $judul = 'Rig Bermasalah', string $tipe = 'danger'): static
    {
        return $this->state(fn () => [
            'data' => [
                'judul' => $judul,
                'pesan' => 'Rig N-80UE di Lapangan Limau mengalami kendala.',
                'modul' => 'alat',
                'tipe' => $tipe,
                'url' => '/alat',
                'icon' => 'tool',
            ],
        ]);
    }

    public function jadwal(string $judul = 'Jadwal Operasi Tertunda', string $tipe = 'warning'): static
    {
        return $this->state(fn () => [
            'data' => [
                'judul' => $judul,
                'pesan' => 'Jadwal operasi Rig Rig-01 tertunda akibat tapak sumur belum siap.',
                'modul' => 'jadwal',
                'tipe' => $tipe,
                'url' => '/jadwal',
                'icon' => 'calendar',
            ],
        ]);
    }

    public function importExport(string $judul = 'Impor Selesai', string $tipe = 'success'): static
    {
        return $this->state(fn () => [
            'data' => [
                'judul' => $judul,
                'pesan' => 'Impor data massal 15 Alat berhasil diproses.',
                'modul' => 'import_export',
                'tipe' => $tipe,
                'url' => '/import-export',
                'icon' => 'file-spreadsheet',
            ],
        ]);
    }
}
