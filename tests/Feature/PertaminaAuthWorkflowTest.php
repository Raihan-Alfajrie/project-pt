<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PertaminaAuthWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_with_correct_content(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Satu Dashboard untuk');
        $response->assertSee('Rig Zona 4');
        $response->assertSee('Rig Termonitor');
        $response->assertSee('Kode Lapangan');
    }

    public function test_about_page_loads_with_coming_soon(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Coming Soon');
        $response->assertSee('Halaman About sedang disiapkan');
    }

    public function test_user_can_login_with_email_and_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'budi.santoso@gmail.com',
            'username' => 'budi.santoso',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => 'budi.santoso@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');

        $user->refresh();
        $this->assertSame('online', $user->status);
    }

    public function test_user_can_login_with_username_and_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'budi.santoso@gmail.com',
            'username' => 'budi.santoso',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => 'budi.santoso',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_new_user_can_register_with_username_and_gets_default_permissions(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Joko Susilo',
            'username' => 'joko.susilo',
            'email' => 'joko.susilo@gmail.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'joko.susilo@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Joko Susilo', $user->nama);
        $this->assertSame('joko.susilo', $user->username);
        $this->assertSame('pekerja', $user->jabatan);
        $this->assertSame('online', $user->status);
        $this->assertCount(3, $user->permissions);
    }

    public function test_user_can_logout_and_status_becomes_offline(): void
    {
        $user = User::factory()->create(['status' => 'online']);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');

        $user->refresh();
        $this->assertSame('offline', $user->status);
    }
}
