<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\UserPermission;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlatTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_alat(): void
    {
        $response = $this->get('/alat');
        $response->assertRedirect('/login');
    }

    public function test_worker_without_permission_can_view_table_but_cannot_see_mutation_buttons(): void
    {
        $this->seed(DatabaseSeeder::class);

        // User id 5 (Dewi Lestari) has alat permission = 0
        $user = User::find(5);
        $this->assertFalse($user->hasModuleAccess('alat'));

        $response = $this->actingAs($user)->get('/alat');
        $response->assertStatus(200);

        // Can view table and items
        $response->assertSee('Daftar Alat');
        $response->assertSee('PDSI #36.1/SKYTOP650-M');
        $response->assertSee('Lihat detail');

        // Cannot see add, edit, or delete buttons
        $response->assertDontSee('Tambah Alat');
        $response->assertDontSee('title="Edit"', false);
        $response->assertDontSee('title="Hapus"', false);
    }

    public function test_worker_without_permission_cannot_mutate_alat(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::find(5); // pekerja, alat = 0
        $alat = Alat::first();

        // Attempt Create
        $response = $this->actingAs($user)->post('/alat', [
            'nama' => 'Rig Palsu #99',
            'kategori' => 'rig',
            'merek' => 'Palsu',
            'no_seri' => 'RIG-9999',
            'status' => 'aktif',
        ]);
        $response->assertStatus(403);

        // Attempt Update
        $response = $this->actingAs($user)->put("/alat/{$alat->id}", [
            'nama' => 'Rig Diubah',
            'kategori' => 'rig',
            'merek' => $alat->merek,
            'no_seri' => $alat->no_seri,
            'status' => 'aktif',
        ]);
        $response->assertStatus(403);

        // Attempt Delete
        $response = $this->actingAs($user)->delete("/alat/{$alat->id}");
        $response->assertStatus(403);
    }

    public function test_worker_with_permission_can_create_update_and_delete_alat(): void
    {
        $this->seed(DatabaseSeeder::class);

        // User id 4 (Agus Wijaya) has alat permission = 1
        $user = User::find(4);
        $this->assertTrue($user->hasModuleAccess('alat'));

        // 1. Can see add button
        $response = $this->actingAs($user)->get('/alat');
        $response->assertStatus(200);
        $response->assertSee('Tambah Alat');

        // 2. Can create new alat
        $response = $this->actingAs($user)->post('/alat', [
            'nama' => 'Genset Baru 250kVA',
            'kategori' => 'alat_pendukung',
            'merek' => 'Yanmar',
            'model' => 'YM-250',
            'tahun_perolehan' => 2024,
            'no_seri' => 'GEN-0250-2024',
            'status' => 'aktif',
            'keterangan' => 'Unit genset baru diterima.',
        ]);

        $response->assertRedirect('/alat');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('alat', [
            'no_seri' => 'GEN-0250-2024',
            'nama' => 'Genset Baru 250kVA',
        ]);

        $newAlat = Alat::where('no_seri', 'GEN-0250-2024')->first();

        // 3. Can update alat
        $response = $this->actingAs($user)->put("/alat/{$newAlat->id}", [
            'nama' => 'Genset Baru 250kVA (Revisi)',
            'kategori' => 'alat_pendukung',
            'merek' => 'Yanmar',
            'model' => 'YM-250-REV',
            'tahun_perolehan' => 2024,
            'no_seri' => 'GEN-0250-2024',
            'status' => 'standby',
            'keterangan' => 'Unit standby.',
        ]);

        $response->assertRedirect('/alat');
        $this->assertDatabaseHas('alat', [
            'id' => $newAlat->id,
            'nama' => 'Genset Baru 250kVA (Revisi)',
            'status' => 'standby',
        ]);

        // 4. Can delete alat
        $response = $this->actingAs($user)->delete("/alat/{$newAlat->id}");
        $response->assertRedirect('/alat');
        $this->assertDatabaseMissing('alat', [
            'id' => $newAlat->id,
        ]);
    }

    public function test_administrator_has_full_mutation_access(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1); // Administrator
        $this->assertTrue($admin->isAdmin());

        $response = $this->actingAs($admin)->get('/alat');
        $response->assertStatus(200);
        $response->assertSee('Tambah Alat');
        $response->assertSee('title="Edit"', false);
        $response->assertSee('title="Hapus"', false);
    }
}
