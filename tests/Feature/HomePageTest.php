<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_access_home_page_and_see_dynamic_stats(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verifikasi stats strip dinamis
        $response->assertSee('Rig Termonitor');
        $response->assertSee('Kode Lapangan');
        $response->assertSee('Item Jadwal Aktif');
        $response->assertSee('Alat &amp; Rig Terdata', false);

        // Verifikasi fleet status ticker
        $response->assertSee('Kesiapan Armada Rig Zona 4:');
        $response->assertSee('Aktif Beroperasi:');
        $response->assertSee('Standby / Siaga:');
        $response->assertSee('Total Armada:');
    }

    public function test_home_page_renders_full_gantt_chart_and_controls(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verifikasi komponen Gantt chart publik
        $response->assertSee('Jadwal &amp; Progres Operasi Rig', false);
        $response->assertSee('publicGanttGrid');
        $response->assertSee('publicRigSearch');
        $response->assertSee('publicRigStatusFilter');
        $response->assertSee('homeTip');

        // Verifikasi 4 menu inti tetap ada
        $response->assertSee('Empat menu inti, satu alur kerja');
    }

    public function test_authenticated_user_sees_buka_dashboard_shortcut_on_home_page(): void
    {
        $user = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Buka Dashboard');
    }
}
