<?php

namespace Tests\Feature;

use App\Models\Lokasi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_lokasi(): void
    {
        $response = $this->get('/lokasi');
        $response->assertRedirect('/login');
    }

    public function test_worker_without_permission_can_view_table_but_cannot_see_mutation_buttons(): void
    {
        $this->seed(DatabaseSeeder::class);

        // User id 4 (Agus Wijaya) has lokasi permission = 0
        $user = User::find(4);
        $this->assertFalse($user->hasModuleAccess('lokasi'));

        $response = $this->actingAs($user)->get('/lokasi');
        $response->assertStatus(200);

        // Can view table, map container and field codes
        $response->assertSee('Daftar Kode Lapangan');
        $response->assertSee('Peta Lapangan Operasional');
        $response->assertSee('KRG');
        $response->assertSee('Lapangan KRG');

        // Cannot see add, edit, or delete buttons
        $response->assertDontSee('Tambah Lokasi');
        $response->assertDontSee('title="Edit"', false);
        $response->assertDontSee('title="Hapus"', false);
    }

    public function test_worker_without_permission_cannot_mutate_lokasi(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::find(4); // pekerja, lokasi = 0
        $lokasi = Lokasi::first();

        // Attempt Create
        $response = $this->actingAs($user)->post('/lokasi', [
            'kode' => 'XYZ',
            'nama_tempat' => 'Lapangan Palsu',
            'provinsi' => 'Sumatera Selatan',
        ]);
        $response->assertStatus(403);

        // Attempt Update
        $response = $this->actingAs($user)->put("/lokasi/{$lokasi->id}", [
            'kode' => $lokasi->kode,
            'nama_tempat' => 'Nama Baru',
            'provinsi' => 'Sumatera Selatan',
        ]);
        $response->assertStatus(403);

        // Attempt Delete
        $response = $this->actingAs($user)->delete("/lokasi/{$lokasi->id}");
        $response->assertStatus(403);
    }

    public function test_worker_with_permission_can_create_edit_and_delete_lokasi(): void
    {
        $this->seed(DatabaseSeeder::class);

        // User id 5 (Dewi Lestari) has lokasi permission = 1
        $user = User::find(5);
        $this->assertTrue($user->hasModuleAccess('lokasi'));

        $response = $this->actingAs($user)->get('/lokasi');
        $response->assertStatus(200);
        $response->assertSee('Tambah Lokasi');

        // 1. Create
        $createResponse = $this->actingAs($user)->post('/lokasi', [
            'kode' => 'NEWL',
            'nama_tempat' => 'Lapangan Baru Prabumulih',
            'provinsi' => 'Sumatera Selatan',
            'kota' => 'Kota Prabumulih',
            'kecamatan' => 'Prabumulih Timur',
            'keterangan' => 'Lokasi pengeboran baru 2027.',
        ]);
        $createResponse->assertRedirect('/lokasi');
        $createResponse->assertSessionHas('success');

        $this->assertDatabaseHas('lokasi', [
            'kode' => 'NEWL',
            'nama_tempat' => 'Lapangan Baru Prabumulih',
        ]);

        $newL = Lokasi::where('kode', 'NEWL')->first();

        // 2. Update
        $updateResponse = $this->actingAs($user)->put("/lokasi/{$newL->id}", [
            'kode' => 'NEWL',
            'nama_tempat' => 'Lapangan Baru Prabumulih Updated',
            'provinsi' => 'Sumatera Selatan',
            'kota' => 'Kota Prabumulih',
            'kecamatan' => 'Prabumulih Barat',
            'keterangan' => 'Keterangan diperbarui.',
        ]);
        $updateResponse->assertRedirect('/lokasi');
        $this->assertDatabaseHas('lokasi', [
            'id' => $newL->id,
            'nama_tempat' => 'Lapangan Baru Prabumulih Updated',
            'kecamatan' => 'Prabumulih Barat',
        ]);

        // 3. Delete
        $deleteResponse = $this->actingAs($user)->delete("/lokasi/{$newL->id}");
        $deleteResponse->assertRedirect('/lokasi');
        $this->assertDatabaseMissing('lokasi', [
            'id' => $newL->id,
        ]);
    }

    public function test_admin_can_manage_lokasi(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1); // Administrator
        $this->assertTrue($admin->isAdmin());

        $response = $this->actingAs($admin)->get('/lokasi');
        $response->assertStatus(200);
        $response->assertSee('Tambah Lokasi');
        $response->assertSee('title="Edit"', false);
        $response->assertSee('title="Hapus"', false);
    }

    public function test_validation_rejects_duplicate_kode(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1);

        $response = $this->actingAs($admin)->post('/lokasi', [
            'kode' => 'MJ', // already exists in seeds
            'nama_tempat' => 'Lapangan Duplikat',
            'provinsi' => 'Sumatera Selatan',
        ]);

        $response->assertSessionHasErrors(['kode']);
    }
}
