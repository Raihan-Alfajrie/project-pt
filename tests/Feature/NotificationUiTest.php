<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_sees_notification_bell_in_navbar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('id="notif-bell-btn"', false);
        $response->assertSee('id="notif-dropdown-panel"', false);
        $response->assertSee('notificationDropdown(0)', false);
    }

    public function test_badge_count_is_rendered_when_user_has_unread_notifications(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->unread()->count(3)->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('notificationDropdown(3)', false);
        $response->assertSee('id="notif-badge-count"', false);
    }

    public function test_authenticated_user_can_access_notifications_page(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->create([
            'data' => [
                'judul' => 'Rig N-80UE Bermasalah',
                'pesan' => 'Tekanan sirkulasi hidrolik drop.',
                'modul' => 'alat',
                'tipe' => 'danger',
                'url' => '/alat',
                'icon' => 'alert-triangle',
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/notifikasi');

        $response->assertOk();
        $response->assertSee('Daftar Pemberitahuan');
        $response->assertSee('Rig N-80UE Bermasalah');
        $response->assertSee('Tekanan sirkulasi hidrolik drop.');
        $response->assertSee('Alat &amp; Rig', false);
    }

    public function test_notifications_page_can_be_filtered(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->unread()->alat('Rig Bermasalah')->create();
        Notification::factory()->forUser($user)->read()->jadwal('Jadwal Tertunda')->create();

        $responseUnread = $this->actingAs($user)->get('/notifikasi?filter=unread');
        $responseUnread->assertOk();
        $responseUnread->assertSee('Rig Bermasalah');
        $responseUnread->assertDontSee('Jadwal Tertunda');

        $responseModul = $this->actingAs($user)->get('/notifikasi?modul=alat');
        $responseModul->assertOk();
        $responseModul->assertSee('Rig Bermasalah');
        $responseModul->assertDontSee('Jadwal Tertunda');
    }

    public function test_unauthenticated_user_is_redirected_from_notifications_page(): void
    {
        $response = $this->get('/notifikasi');
        $response->assertRedirect('/login');
    }
}
