<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_about_page(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Platform Monitoring Terintegrasi');
        $response->assertSee('Wilayah Kerja Zona 4');
        $response->assertSee('Masuk ke Platform');
    }

    public function test_authenticated_user_sees_dashboard_shortcut_on_about_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Buka Dashboard');
    }

    public function test_about_page_renders_all_production_fields(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Prabumulih Field');
        $response->assertSee('Limau Field');
        $response->assertSee('Pendopo Field');
        $response->assertSee('Adera Field');
        $response->assertSee('Ramba Field');
        $response->assertSee('Ogan Komering Field');
        $response->assertSee('Raja / Tempirai Field');
    }

    public function test_about_page_renders_system_capabilities(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Eksekutif');
        $response->assertSee('Manajemen Aset &amp; Rig', false);
        $response->assertSee('Pemetaan Lokasi GIS');
        $response->assertSee('Timeline &amp; Gantt Jadwal', false);
        $response->assertSee('Otomasi Import &amp; Export', false);
        $response->assertSee('Sistem Notifikasi Terpadu');
    }

    public function test_about_page_renders_governance_and_rig_types(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Rig Drilling (Pemboran)');
        $response->assertSee('Rig Workover &amp; Well Service', false);
        $response->assertSee('Otorisasi RBAC Berlapis');
        $response->assertSee('Audit Trail Transparan');
        $response->assertSee('Proteksi Formula &amp; Enkripsi', false);
    }

    public function test_about_page_renders_interactive_leaflet_map_and_theme_listeners(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('leafletAboutMap');
        $response->assertSee('Peta Interaktif Sebaran Wilayah Kerja Zona 4');
        $response->assertSee('getMapThemeUrls');
        $response->assertSee('theme-changed');
        $response->assertSee('L.map');
    }

    public function test_guest_can_submit_contact_helpdesk_form_and_triggers_admin_notification(): void
    {
        $admin = User::factory()->create([
            'jabatan' => 'administrator',
        ]);

        $response = $this->from('/about')->post('/kontak-bantuan', [
            'nama' => 'Ir. Hartono',
            'email' => 'hartono@mitra.com',
            'no_hp' => '081298765432',
            'instansi' => 'PT Mitra Rig Drilling',
            'kategori' => 'operasional',
            'perihal' => 'Permohonan Koordinasi Moving Rig ke Lapangan Limau',
            'pesan' => 'Mohon bantuan konfirmasi jadwal moving rig pemboran menuju lokasi sumur baru.',
        ]);

        $response->assertRedirect('/about#bantuan');
        $response->assertSessionHas('success_kontak');

        $this->assertDatabaseHas('kontak_bantuan', [
            'nama' => 'Ir. Hartono',
            'email' => 'hartono@mitra.com',
            'kategori' => 'operasional',
            'perihal' => 'Permohonan Koordinasi Moving Rig ke Lapangan Limau',
        ]);
    }

    public function test_contact_form_validation_rejects_empty_required_fields(): void
    {
        $response = $this->from('/about')->post('/kontak-bantuan', [
            'nama' => '',
            'email' => 'bukan-email',
            'kategori' => 'kategori_palsu',
            'perihal' => '',
            'pesan' => 'pendek',
        ]);

        $response->assertSessionHasErrors(['nama', 'email', 'kategori', 'perihal', 'pesan']);
    }
}
