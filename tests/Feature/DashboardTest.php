<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_with_real_data(): void
    {
        // Seed full database
        $this->seed(DatabaseSeeder::class);

        $user = User::first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);

        // Check topbar & layout components
        $response->assertSee('Pertamina');
        $response->assertSee('Admin Panel');
        $response->assertSee('Dashboard');
        $response->assertSee('Pantau status, jadwal dan progres');

        // Check KPI cards (calculated from DB)
        $response->assertSee('Total Alat');
        $response->assertSee('16');
        $response->assertSee('Alat Aktif');
        $response->assertSee('13');
        $response->assertSee('81%');
        $response->assertSee('Alat Tidak Aktif');
        $response->assertSee('2');
        $response->assertSee('Alat Bermasalah');
        $response->assertSee('1');

        // Check Gantt Chart & Table
        $response->assertSee('Jadwal &amp; Progres', false);
        $response->assertSee('Daftar Alat');
        $response->assertSee('PDSI #36.1/SKYTOP650-M');
        $response->assertSee('Pekerjaan WI Zona 4');
        $response->assertSee('AIRLANGGA #55/550 HP');
        $response->assertSee('TMMJ #23/550 HP');
        $response->assertSee('BKB-AR.016');
    }
}
