<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('logo_pertamina_2.png');
        $response->assertSee('Buat Akun Baru');
        $response->assertSee('Lengkapi data diri Anda untuk mendaftar');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('Username');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Daftar');
        $response->assertSee('Sudah punya akun?');
        $response->assertSee('Masuk');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
