<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/audit-log');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_audit_log_page(): void
    {
        $user = User::where('jabatan', 'pekerja')->first();

        $response = $this->actingAs($user)->get('/audit-log');

        $response->assertOk();
        $response->assertSee('Audit Log');
        $response->assertSee('Riwayat Aktivitas');
    }

    public function test_audit_log_displays_seeded_entries(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/audit-log');

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('KRG-PJ1');
    }

    public function test_audit_log_is_read_only_and_shows_action_tags(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/audit-log');

        $response->assertOk();
        $response->assertSee('Semua aksi');
        $response->assertSee('Semua modul');
        $response->assertSee('Semua jabatan');
    }
}
