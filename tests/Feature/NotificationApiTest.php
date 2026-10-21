<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_notification_endpoints(): void
    {
        $this->getJson('/notifications')->assertUnauthorized();
        $this->getJson('/notifications/unread-count')->assertUnauthorized();
        $this->postJson('/notifications/mark-all-read')->assertUnauthorized();
        $this->postJson('/notifications/some-id/read')->assertUnauthorized();
        $this->deleteJson('/notifications/some-id')->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_notifications_list(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->create([
            'data' => [
                'judul' => 'Rig N-80UE Bermasalah',
                'pesan' => 'Tekanan pompa lumpur abnormal.',
                'modul' => 'alat',
                'tipe' => 'danger',
                'url' => '/alat',
                'icon' => 'alert-triangle',
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)->getJson('/notifications');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'unread_count' => 1,
                'total' => 1,
            ])
            ->assertJsonStructure([
                'success',
                'unread_count',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'judul',
                        'pesan',
                        'modul',
                        'modul_label',
                        'tipe',
                        'url',
                        'icon',
                        'waktu_display',
                        'is_read',
                        'read_at',
                        'created_at',
                    ],
                ],
            ]);

        $this->assertSame('Rig N-80UE Bermasalah', $response->json('data.0.judul'));
        $this->assertSame('Alat & Rig', $response->json('data.0.modul_label'));
        $this->assertFalse($response->json('data.0.is_read'));
    }

    public function test_user_only_sees_their_own_notifications(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Notification::factory()->forUser($userA)->count(2)->create();
        Notification::factory()->forUser($userB)->count(3)->create();

        $responseA = $this->actingAs($userA)->getJson('/notifications');
        $responseA->assertOk()->assertJsonCount(2, 'data');

        $responseB = $this->actingAs($userB)->getJson('/notifications');
        $responseB->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_filter_unread_notifications(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->unread()->count(2)->create();
        Notification::factory()->forUser($user)->read()->count(2)->create();

        // Filter unread
        $responseUnread = $this->actingAs($user)->getJson('/notifications?filter=unread');
        $responseUnread->assertOk()->assertJsonCount(2, 'data');

        // Filter all
        $responseAll = $this->actingAs($user)->getJson('/notifications?filter=all');
        $responseAll->assertOk()->assertJsonCount(4, 'data');
    }

    public function test_unread_count_endpoint(): void
    {
        $user = User::factory()->create();

        Notification::factory()->forUser($user)->unread()->count(3)->create();
        Notification::factory()->forUser($user)->read()->count(1)->create();

        $response = $this->actingAs($user)->getJson('/notifications/unread-count');
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'count' => 3,
            ]);
    }

    public function test_mark_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notif = Notification::factory()->forUser($user)->unread()->create();

        $this->assertNull($notif->read_at);

        $response = $this->actingAs($user)->postJson("/notifications/{$notif->id}/read");
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca.',
                'unread_count' => 0,
            ]);

        $this->assertNotNull($notif->fresh()->read_at);
        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_cannot_mark_other_users_notification_as_read(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $notifB = Notification::factory()->forUser($userB)->unread()->create();

        $response = $this->actingAs($userA)->postJson("/notifications/{$notifB->id}/read");
        $response->assertNotFound();

        $this->assertNull($notifB->fresh()->read_at);
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        Notification::factory()->forUser($user)->unread()->count(3)->create();

        $this->assertSame(3, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->postJson('/notifications/mark-all-read');
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_delete_own_notification(): void
    {
        $user = User::factory()->create();
        $notif = Notification::factory()->forUser($user)->create();

        $response = $this->actingAs($user)->deleteJson("/notifications/{$notif->id}");
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('notifications', ['id' => $notif->id]);
    }

    public function test_cannot_delete_other_users_notification(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $notifB = Notification::factory()->forUser($userB)->create();

        $response = $this->actingAs($userA)->deleteJson("/notifications/{$notifB->id}");
        $response->assertNotFound();

        $this->assertDatabaseHas('notifications', ['id' => $notifB->id]);
    }
}
