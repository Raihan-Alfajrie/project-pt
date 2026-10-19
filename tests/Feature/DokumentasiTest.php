<?php

namespace Tests\Feature;

use App\Models\DokumentasiItem;
use App\Models\DokumentasiPanel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokumentasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dokumentasi');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dokumentasi(): void
    {
        $pekerja = User::where('jabatan', 'pekerja')->first();

        $response = $this->actingAs($pekerja)->get('/dokumentasi');

        $response->assertOk();
        $response->assertSee('Legenda Dashboard');
        $response->assertSee('Kesiapan Operasional');
        $response->assertSee('Sudah Tajak');
        // Worker does not see the Add Panel button
        $response->assertDontSee('id="btnAddPanel"', false);
    }

    public function test_admin_sees_management_buttons(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/dokumentasi');

        $response->assertOk();
        $response->assertSee('id="btnAddPanel"', false);
    }

    public function test_pekerja_cannot_manage_panels_or_items(): void
    {
        $pekerja = User::where('jabatan', 'pekerja')->first();
        $panel = DokumentasiPanel::first();
        $item = DokumentasiItem::first();

        // Panel create
        $this->actingAs($pekerja)
            ->post('/dokumentasi/panels', ['judul' => 'Panel Ilegal'])
            ->assertForbidden();

        // Panel update
        $this->actingAs($pekerja)
            ->put('/dokumentasi/panels/' . $panel->id, ['judul' => 'Update Ilegal'])
            ->assertForbidden();

        // Panel delete
        $this->actingAs($pekerja)
            ->delete('/dokumentasi/panels/' . $panel->id)
            ->assertForbidden();

        // Item create
        $this->actingAs($pekerja)
            ->post('/dokumentasi/panels/' . $panel->id . '/items', [
                'label' => 'Item Ilegal',
                'gaya' => 'solid',
                'warna' => '#123456',
            ])
            ->assertForbidden();

        // Item update
        $this->actingAs($pekerja)
            ->put('/dokumentasi/items/' . $item->id, [
                'label' => 'Item Update Ilegal',
                'gaya' => 'solid',
                'warna' => '#123456',
            ])
            ->assertForbidden();

        // Item delete
        $this->actingAs($pekerja)
            ->delete('/dokumentasi/items/' . $item->id)
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_panel(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Create
        $response = $this->actingAs($admin)->post('/dokumentasi/panels', [
            'judul' => 'Legend Baru Test',
            'deskripsi' => 'Deskripsi panel baru untuk pengujian',
        ]);
        $response->assertRedirect('/dokumentasi');
        $this->assertDatabaseHas('dokumentasi_panel', ['judul' => 'Legend Baru Test']);

        $panel = DokumentasiPanel::where('judul', 'Legend Baru Test')->first();

        // Update
        $response = $this->actingAs($admin)->put('/dokumentasi/panels/' . $panel->id, [
            'judul' => 'Legend Baru Updated',
            'deskripsi' => 'Deskripsi updated',
        ]);
        $response->assertRedirect('/dokumentasi');
        $this->assertDatabaseHas('dokumentasi_panel', ['judul' => 'Legend Baru Updated']);

        // Delete
        $response = $this->actingAs($admin)->delete('/dokumentasi/panels/' . $panel->id);
        $response->assertRedirect('/dokumentasi');
        $this->assertDatabaseMissing('dokumentasi_panel', ['id' => $panel->id]);
    }

    public function test_admin_can_create_update_and_delete_item(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();
        $panel = DokumentasiPanel::first();

        // Create Item
        $response = $this->actingAs($admin)->post('/dokumentasi/panels/' . $panel->id . '/items', [
            'label' => 'Status Operasi Baru',
            'gaya' => 'dashed',
            'warna' => '#11aacc',
        ]);
        $response->assertRedirect('/dokumentasi');
        $this->assertDatabaseHas('dokumentasi_item', [
            'panel_id' => $panel->id,
            'label' => 'Status Operasi Baru',
            'gaya' => 'dashed',
            'warna' => '#11aacc',
        ]);

        $item = DokumentasiItem::where('label', 'Status Operasi Baru')->first();

        // Update Item
        $response = $this->actingAs($admin)->put('/dokumentasi/items/' . $item->id, [
            'label' => 'Status Operasi Diperbarui',
            'gaya' => 'border',
            'warna' => '#22bbdd',
        ]);
        $response->assertRedirect('/dokumentasi');
        $this->assertDatabaseHas('dokumentasi_item', [
            'id' => $item->id,
            'label' => 'Status Operasi Diperbarui',
            'gaya' => 'border',
            'warna' => '#22bbdd',
        ]);

        // Delete Item
        $response = $this->actingAs($admin)->delete('/dokumentasi/items/' . $item->id);
        $response->assertRedirect('/dokumentasi');
        $this->assertDatabaseMissing('dokumentasi_item', ['id' => $item->id]);
    }

    public function test_changes_in_dokumentasi_reflect_in_jadwal(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();
        $panel = DokumentasiPanel::first();

        // Create a new documentation item
        $this->actingAs($admin)->post('/dokumentasi/panels/' . $panel->id . '/items', [
            'label' => 'Fase Khusus Baru',
            'gaya' => 'solid',
            'warna' => '#ff00aa',
        ]);

        // View Jadwal page, it should include 'Fase Khusus Baru' in the kategori select options
        $response = $this->actingAs($admin)->get('/jadwal');
        $response->assertOk();
        $response->assertSee('Fase Khusus Baru');
    }
}
