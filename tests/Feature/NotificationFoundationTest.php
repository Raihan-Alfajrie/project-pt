<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Database\Seeders\NotificationSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('notifications'));
        $this->assertTrue(Schema::hasColumns('notifications', [
            'id',
            'type',
            'notifiable_type',
            'notifiable_id',
            'data',
            'read_at',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_user_has_notifications_relation_returning_custom_model(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->forUser($user)->create([
            'data' => [
                'judul' => 'Uji Notifikasi',
                'pesan' => 'Pesan pengujian sistem.',
                'modul' => 'alat',
                'tipe' => 'danger',
                'url' => '/alat',
                'icon' => 'tool',
            ],
        ]);

        $this->assertInstanceOf(Notification::class, $user->notifications->first());
        $this->assertSame($notification->id, $user->notifications->first()->id);
        $this->assertSame('Uji Notifikasi', $user->notifications->first()->judul);
    }

    public function test_notification_accessors_format_data_correctly(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->forUser($user)->create([
            'data' => [
                'judul' => 'Rig N-80UE Bermasalah',
                'pesan' => 'Terjadi penurunan tekanan sirkulasi.',
                'modul' => 'alat',
                'tipe' => 'danger',
                'url' => '/alat?id=1',
                'icon' => 'alert-triangle',
            ],
            'read_at' => null,
            'created_at' => now()->subMinutes(10),
        ]);

        $this->assertSame('Rig N-80UE Bermasalah', $notification->judul);
        $this->assertSame('Terjadi penurunan tekanan sirkulasi.', $notification->pesan);
        $this->assertSame('alat', $notification->modul);
        $this->assertSame('Alat & Rig', $notification->modul_label);
        $this->assertSame('danger', $notification->tipe);
        $this->assertSame('/alat?id=1', $notification->url);
        $this->assertSame('alert-triangle', $notification->icon);
        $this->assertFalse($notification->is_read);
        $this->assertNotEmpty($notification->waktu_display);
    }

    public function test_unread_and_read_notifications_scopes_work(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->unread()->create();
        Notification::factory()->forUser($user)->unread()->create();
        Notification::factory()->forUser($user)->read()->create();

        $this->assertCount(3, $user->notifications);
        $this->assertCount(2, $user->unreadNotifications);
        $this->assertCount(1, $user->readNotifications);
    }

    public function test_mark_as_read_and_unread_methods(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->forUser($user)->unread()->create();
        $this->assertNull($notification->read_at);
        $this->assertFalse($notification->is_read);

        $notification->markAsRead();
        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertTrue($notification->fresh()->is_read);

        $notification->markAsUnread();
        $this->assertNull($notification->fresh()->read_at);
        $this->assertFalse($notification->fresh()->is_read);
    }

    public function test_notification_factory_states_work(): void
    {
        $user = User::factory()->create();

        $alatNotif = Notification::factory()->forUser($user)->alat()->create();
        $this->assertSame('alat', $alatNotif->modul);
        $this->assertSame('danger', $alatNotif->tipe);

        $jadwalNotif = Notification::factory()->forUser($user)->jadwal()->create();
        $this->assertSame('jadwal', $jadwalNotif->modul);
        $this->assertSame('warning', $jadwalNotif->tipe);

        $importNotif = Notification::factory()->forUser($user)->importExport()->create();
        $this->assertSame('import_export', $importNotif->modul);
        $this->assertSame('success', $importNotif->tipe);
    }

    public function test_notification_seeder_populates_data_properly(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(NotificationSeeder::class);

        $admin = User::where('username', 'budi.santoso')->first();
        $this->assertNotNull($admin);
        $this->assertSame(4, $admin->notifications()->count());
        $this->assertSame(3, $admin->unreadNotifications()->count());
        $this->assertSame(1, $admin->readNotifications()->count());

        $worker = User::where('username', 'agus.wijaya')->first();
        $this->assertNotNull($worker);
        $this->assertSame(2, $worker->notifications()->count());
        $this->assertSame(2, $worker->unreadNotifications()->count());
    }
}
