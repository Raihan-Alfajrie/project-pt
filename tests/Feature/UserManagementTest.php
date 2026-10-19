<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPermission;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');
    }

    public function test_worker_is_forbidden_from_accessing_user_management(): void
    {
        $this->seed(DatabaseSeeder::class);

        $worker = User::where('jabatan', 'pekerja')->first();

        // GET /users forbidden
        $response = $this->actingAs($worker)->get('/users');
        $response->assertStatus(403);

        // POST /users forbidden
        $postResponse = $this->actingAs($worker)->post('/users', [
            'nama' => 'Palsu',
            'username' => 'palsu',
            'email' => 'palsu@gmail.com',
            'password' => 'secret123',
            'jabatan' => 'pekerja',
            'status' => 'offline',
        ]);
        $postResponse->assertStatus(403);

        // Permissions update forbidden
        $permResponse = $this->actingAs($worker)->put("/users/{$worker->id}/permissions", [
            'alat' => 1,
            'lokasi' => 1,
            'jadwal' => 1,
        ]);
        $permResponse->assertStatus(403);
    }

    public function test_admin_can_view_user_management_and_kpis(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1); // Budi Santoso
        $this->assertTrue($admin->isAdmin());

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);

        // Verify elements
        $response->assertSee('Daftar User');
        $response->assertSee('Total Admin');
        $response->assertSee('Total User (Pekerja)');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Agus Wijaya');
    }

    public function test_admin_can_create_new_user_with_manual_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1);

        $response = $this->actingAs($admin)->post('/users', [
            'nama' => 'Rahmat Hidayat',
            'username' => 'rahmat.hidayat',
            'email' => 'rahmat.hidayat@gmail.com',
            'password' => 'PertaminaSecure2026',
            'jabatan' => 'pekerja',
            'status' => 'offline',
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'nama' => 'Rahmat Hidayat',
            'username' => 'rahmat.hidayat',
            'email' => 'rahmat.hidayat@gmail.com',
            'jabatan' => 'pekerja',
        ]);

        $newUser = User::where('username', 'rahmat.hidayat')->first();
        $this->assertTrue(Hash::check('PertaminaSecure2026', $newUser->password));

        // Default permissions created
        $this->assertDatabaseHas('user_permissions', [
            'user_id' => $newUser->id,
            'modul' => 'alat',
            'diizinkan' => 0,
        ]);
    }

    public function test_admin_can_update_user_and_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1);
        $worker = User::find(4); // Agus Wijaya

        // 1. Update user info
        $updateResponse = $this->actingAs($admin)->put("/users/{$worker->id}", [
            'nama' => 'Agus Wijaya Updated',
            'username' => 'agus.wijaya',
            'email' => 'agus.wijaya@gmail.com',
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);
        $updateResponse->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $worker->id,
            'nama' => 'Agus Wijaya Updated',
        ]);

        // 2. Update permissions (giving access to lokasi)
        $this->assertFalse($worker->hasModuleAccess('lokasi'));

        $permResponse = $this->actingAs($admin)->put("/users/{$worker->id}/permissions", [
            'alat' => 1,
            'lokasi' => 1,
            'jadwal' => 1,
        ]);
        $permResponse->assertRedirect('/users');

        $worker->refresh();
        $this->assertTrue($worker->hasModuleAccess('lokasi'));
    }

    public function test_admin_cannot_delete_self_but_can_delete_other_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1);
        $otherUser = User::find(10); // Maya Anggraini

        // 1. Cannot delete self
        $deleteSelfResponse = $this->actingAs($admin)->delete("/users/{$admin->id}");
        $deleteSelfResponse->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // 2. Can delete other user
        $deleteResponse = $this->actingAs($admin)->delete("/users/{$otherUser->id}");
        $deleteResponse->assertRedirect('/users');
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);
    }
}
