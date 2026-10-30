<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_pages_render_theme_toggle_button(): void
    {
        $responseHome = $this->get('/');
        $responseHome->assertStatus(200);
        $responseHome->assertSee('toggleTheme()');
        $responseHome->assertSee('theme-icon-sun');
        $responseHome->assertSee('theme-icon-moon');

        $responseAbout = $this->get('/about');
        $responseAbout->assertStatus(200);
        $responseAbout->assertSee('toggleTheme()');
        $responseAbout->assertSee('theme-icon-sun');
        $responseAbout->assertSee('theme-icon-moon');
    }

    public function test_auth_pages_render_theme_toggle_button(): void
    {
        $responseLogin = $this->get('/login');
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('toggleTheme()');
        $responseLogin->assertSee('auth-theme-btn');
        $responseLogin->assertSee('theme-icon-sun');
        $responseLogin->assertSee('theme-icon-moon');

        $responseRegister = $this->get('/register');
        $responseRegister->assertStatus(200);
        $responseRegister->assertSee('toggleTheme()');
        $responseRegister->assertSee('auth-theme-btn');
        $responseRegister->assertSee('theme-icon-sun');
        $responseRegister->assertSee('theme-icon-moon');
    }

    public function test_authenticated_dashboard_renders_theme_toggle_in_navbar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('theme-toggle-btn');
        $response->assertSee('toggleTheme()');
        $response->assertSee('theme-icon-sun');
        $response->assertSee('theme-icon-moon');
    }

    public function test_lokasi_page_has_theme_changed_listener(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/lokasi');
        $response->assertStatus(200);
        $response->assertSee('theme-changed');
        $response->assertSee('getMapThemeUrls');
    }
}

