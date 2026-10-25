<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profil_route_displays_pertamina_profile_page(): void
    {
        $user = User::factory()->create([
            'nama' => 'Budi Sudarsono',
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profil');

        $response->assertOk();
        $response->assertSee('Profil Akun');
        $response->assertSee('Detail Akun');
        $response->assertSee('Sesi Aktif');
        $response->assertSee('Perangkat &amp; Aplikasi Terhubung', false);
        $response->assertSee('Budi Sudarsono');
        $response->assertSee('BS');
        $response->assertSee('margin: 0 auto', false);
        $response->assertSee('Pengaturan akun &amp; sesi aktif', false);
        $response->assertSee('id="btnLogout"', false);
        $response->assertSee('Logout');
    }

    public function test_profile_information_can_be_updated_with_nama(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'nama' => 'Nama Baru Pertamina',
                'email' => 'baru@pertamina.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Nama Baru Pertamina', $user->nama);
        $this->assertSame('baru@pertamina.com', $user->email);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
