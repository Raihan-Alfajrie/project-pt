<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestNavigationAndFooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_updated_navbar_order_and_faq_link(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verifikasi urutan dan tautan navbar publik
        $content = $response->getContent();
        $this->assertStringContainsString('Home', $content);
        $this->assertStringContainsString('About', $content);
        $this->assertStringContainsString('FAQ', $content);

        // Verifikasi tombol mode terang/gelap
        $this->assertStringContainsString('btn-theme-guest', $content);
        $this->assertStringContainsString('theme-icon-sun', $content);
        $this->assertStringContainsString('theme-icon-moon', $content);

        // Verifikasi aksi autentikasi publik
        $this->assertStringContainsString('id="btnOpenLogin"', $content);
        $this->assertStringContainsString('id="btnOpenRegister"', $content);
    }

    public function test_authenticated_user_sees_dashboard_shortcut_in_navbar(): void
    {
        $user = User::factory()->create([
            'jabatan' => 'administrator',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ke Dashboard');
        $response->assertSee('Keluar');
        $response->assertDontSee('id="btnOpenLogin"', false);
    }

    public function test_guest_layout_renders_mobile_hamburger_drawer(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('mobile-menu-btn');
        $response->assertSee('mobileDrawerOpen');
        $response->assertSee('drawer-panel');
        $response->assertSee('drawer-nav');
        $response->assertSee('Pusat Operasi WK Zona 4: Aktif');
    }

    public function test_enhanced_corporate_footer_renders_all_required_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Identitas Korporat
        $response->assertSee('PT Pertamina Hulu Rokan');
        $response->assertSee('Zona 4 — Regional 1 Sumatera');
        $response->assertSee('Pusat Kendali Operasi: Aktif 24/7 (WIB)');

        // Navigasi Utama Footer
        $response->assertSee('Beranda Platform');
        $response->assertSee('Wilayah Kerja &amp; Lapangan', false);
        $response->assertSee('Pusat Bantuan &amp; FAQ', false);

        // Wilayah Lapangan Operasi
        $response->assertSee('Lapangan Prabumulih');
        $response->assertSee('Lapangan Limau');
        $response->assertSee('Lapangan Pendopo');
        $response->assertSee('Lapangan Adera');
        $response->assertSee('Lapangan Ramba &amp; Ogan Komering', false);

        // Kontak Helpdesk & Disclaimer
        $response->assertSee('Komplek Pertamina EP Asset 2, Kota Prabumulih');
        $response->assertSee('helpdesk.rig@pertamina.com');
        $response->assertSee('Dukungan Operasi: 24 Jam Non-Stop');
        $response->assertSee('&copy; 2026 PT Pertamina Hulu Rokan Zona 4', false);
        $response->assertSee('Dokumen &amp; Data Rahasia Perusahaan', false);
    }

    public function test_about_page_also_inherits_new_navbar_and_corporate_footer(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('PT Pertamina Hulu Rokan');
        $response->assertSee('mobile-menu-btn');
        $response->assertSee('btn-theme-guest');
        $response->assertSee('helpdesk.rig@pertamina.com');
    }
}
