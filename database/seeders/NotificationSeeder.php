<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('username', 'budi.santoso')->first();
        $worker = User::where('username', 'agus.wijaya')->first();

        if ($admin) {
            $adminNotifications = [
                [
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\SystemNotification',
                    'data' => [
                        'judul' => 'Rig N-80UE Mengalami Masalah',
                        'pesan' => 'Rig N-80UE di Lapangan Limau terdeteksi bermasalah pada sistem sirkulasi lumpur.',
                        'modul' => 'alat',
                        'tipe' => 'danger',
                        'url' => '/alat',
                        'icon' => 'alert-triangle',
                    ],
                    'read_at' => null,
                    'created_at' => now()->subMinutes(15),
                    'updated_at' => now()->subMinutes(15),
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\SystemNotification',
                    'data' => [
                        'judul' => 'Jadwal Operasi Tertunda',
                        'pesan' => 'Operasi Tajak Sumur LMU-102 pada Rig-01 tertunda akibat kendala tapak sumur.',
                        'modul' => 'jadwal',
                        'tipe' => 'warning',
                        'url' => '/jadwal',
                        'icon' => 'calendar',
                    ],
                    'read_at' => null,
                    'created_at' => now()->subHours(1),
                    'updated_at' => now()->subHours(1),
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\SystemNotification',
                    'data' => [
                        'judul' => 'Impor Data Selesai',
                        'pesan' => 'Impor data massal 15 baris data Alat berhasil disimpan ke basis data.',
                        'modul' => 'import_export',
                        'tipe' => 'success',
                        'url' => '/import-export',
                        'icon' => 'file-spreadsheet',
                    ],
                    'read_at' => null,
                    'created_at' => now()->subHours(3),
                    'updated_at' => now()->subHours(3),
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\SystemNotification',
                    'data' => [
                        'judul' => 'Pembaruan Hak Akses Pengguna',
                        'pesan' => 'Hak akses modul Lokasi telah diperbarui untuk akun Dewi Lestari.',
                        'modul' => 'user',
                        'tipe' => 'info',
                        'url' => '/users',
                        'icon' => 'user-check',
                    ],
                    'read_at' => now()->subHours(12),
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subHours(12),
                ],
            ];

            foreach ($adminNotifications as $notif) {
                Notification::updateOrCreate(
                    ['id' => $notif['id']],
                    array_merge($notif, [
                        'notifiable_type' => User::class,
                        'notifiable_id' => $admin->id,
                    ])
                );
            }
        }

        if ($worker) {
            $workerNotifications = [
                [
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\SystemNotification',
                    'data' => [
                        'judul' => 'Perhatian: Rig N-80UE Bermasalah',
                        'pesan' => 'Status operasional Rig N-80UE di Lapangan Limau diubah menjadi bermasalah.',
                        'modul' => 'alat',
                        'tipe' => 'danger',
                        'url' => '/alat',
                        'icon' => 'alert-triangle',
                    ],
                    'read_at' => null,
                    'created_at' => now()->subMinutes(30),
                    'updated_at' => now()->subMinutes(30),
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\SystemNotification',
                    'data' => [
                        'judul' => 'Kendala Jadwal LMU-102',
                        'pesan' => 'Status jadwal Tajak Sumur LMU-102 ditandai kendala operasional.',
                        'modul' => 'jadwal',
                        'tipe' => 'warning',
                        'url' => '/jadwal',
                        'icon' => 'calendar',
                    ],
                    'read_at' => null,
                    'created_at' => now()->subHours(2),
                    'updated_at' => now()->subHours(2),
                ],
            ];

            foreach ($workerNotifications as $notif) {
                Notification::updateOrCreate(
                    ['id' => $notif['id']],
                    array_merge($notif, [
                        'notifiable_type' => User::class,
                        'notifiable_id' => $worker->id,
                    ])
                );
            }
        }
    }
}
