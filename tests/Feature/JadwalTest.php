<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DokumentasiItem;
use App\Models\Jadwal;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_jadwal(): void
    {
        $response = $this->get('/jadwal');
        $response->assertRedirect('/login');
    }

    public function test_worker_without_permission_can_view_gantt_but_cannot_see_mutation_buttons(): void
    {
        $this->seed(DatabaseSeeder::class);

        // User id 6 (Fajar Ramadhan) has jadwal permission = 0
        $user = User::find(6);
        $this->assertFalse($user->hasModuleAccess('jadwal'));

        $response = $this->actingAs($user)->get('/jadwal');
        $response->assertStatus(200);

        // Can view Gantt, KPI status cards, and schedule labels
        $response->assertSee('Jadwal &amp; Progres Pekerjaan Sumur', false);
        $response->assertSee('Pekerjaan WI Zona 4');
        $response->assertSee('PDSI #36.1/SKYTOP650-M');

        // Cannot see add, edit, or delete buttons
        $response->assertDontSee('Tambah Jadwal');
        $response->assertDontSee('id="btnDetailEdit"', false);
        $response->assertDontSee('id="btnDetailDelete"', false);
    }

    public function test_worker_without_permission_cannot_mutate_jadwal(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::find(6); // pekerja, jadwal = 0
        $jadwal = Jadwal::first();
        $rig = Alat::where('kategori', 'rig')->first();
        $kategori = DokumentasiItem::first();

        // Attempt Create
        $response = $this->actingAs($user)->post('/jadwal', [
            'alat_id' => $rig->id,
            'label' => 'Sumur Palsu CF 2027',
            'kategori_id' => $kategori->id,
            'status' => 'progres',
            'bulan_index' => 2,
            'durasi_bulan' => 3,
        ]);
        $response->assertStatus(403);

        // Attempt Update
        $response = $this->actingAs($user)->put("/jadwal/{$jadwal->id}", [
            'alat_id' => $rig->id,
            'label' => 'Label Updated Palsu',
            'kategori_id' => $kategori->id,
            'status' => 'selesai',
            'bulan_index' => 1,
            'durasi_bulan' => 1,
        ]);
        $response->assertStatus(403);

        // Attempt Delete
        $response = $this->actingAs($user)->delete("/jadwal/{$jadwal->id}");
        $response->assertStatus(403);
    }

    public function test_worker_with_permission_can_create_edit_and_delete_jadwal(): void
    {
        $this->seed(DatabaseSeeder::class);

        // User id 4 (Agus Wijaya) has jadwal permission = 1
        $user = User::find(4);
        $this->assertTrue($user->hasModuleAccess('jadwal'));

        $response = $this->actingAs($user)->get('/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Tambah Jadwal');

        $rig = Alat::where('kategori', 'rig')->first();
        $kategori = DokumentasiItem::first();

        // 1. Create
        $createResponse = $this->actingAs($user)->post('/jadwal', [
            'alat_id' => $rig->id,
            'label' => 'MJ-TEST-01 (CF 2027)',
            'kategori_id' => $kategori->id,
            'status' => 'progres',
            'bulan_index' => 4, // Oct-26
            'durasi_bulan' => 2,
            'catatan' => 'Pekerjaan tajak baru.',
        ]);
        $createResponse->assertRedirect('/jadwal');
        $createResponse->assertSessionHas('success');

        $this->assertDatabaseHas('jadwal', [
            'label' => 'MJ-TEST-01 (CF 2027)',
            'alat_id' => $rig->id,
            'durasi_bulan' => 2,
        ]);

        $newJadwal = Jadwal::where('label', 'MJ-TEST-01 (CF 2027)')->first();

        // 2. Update
        $updateResponse = $this->actingAs($user)->put("/jadwal/{$newJadwal->id}", [
            'alat_id' => $rig->id,
            'label' => 'MJ-TEST-01 Completed',
            'kategori_id' => $kategori->id,
            'status' => 'selesai',
            'bulan_index' => 4,
            'durasi_bulan' => 2,
            'catatan' => 'Pekerjaan sudah selesai lebih cepat.',
        ]);
        $updateResponse->assertRedirect('/jadwal');
        $this->assertDatabaseHas('jadwal', [
            'id' => $newJadwal->id,
            'label' => 'MJ-TEST-01 Completed',
            'status' => 'selesai',
        ]);

        // 3. Delete
        $deleteResponse = $this->actingAs($user)->delete("/jadwal/{$newJadwal->id}");
        $deleteResponse->assertRedirect('/jadwal');
        $this->assertDatabaseMissing('jadwal', [
            'id' => $newJadwal->id,
        ]);
    }

    public function test_admin_can_manage_jadwal(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::find(1); // Administrator
        $this->assertTrue($admin->isAdmin());

        $response = $this->actingAs($admin)->get('/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Tambah Jadwal');
    }

    public function test_deep_link_with_kode_parameter(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::find(4);
        $response = $this->actingAs($user)->get('/jadwal?kode=MJ');
        $response->assertStatus(200);
        $response->assertSee('Lapangan: MJ ✕');
    }

    public function test_user_can_create_jadwal_with_month_and_year_beyond_2027_and_timeline_expands(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::find(4);
        $rig = Alat::where('kategori', 'rig')->first();
        $kategori = DokumentasiItem::first();

        // Create schedule in July 2028 for 5 months
        $response = $this->actingAs($user)->post('/jadwal', [
            'alat_id' => $rig->id,
            'label' => 'FUTURE-RIG-2028',
            'kategori_id' => $kategori->id,
            'status' => 'progres',
            'bulan' => 7,
            'tahun' => 2028,
            'durasi_bulan' => 5,
            'catatan' => 'Jadwal jangka panjang tahun 2028.',
        ]);

        $response->assertRedirect('/jadwal');
        $this->assertDatabaseHas('jadwal', [
            'label' => 'FUTURE-RIG-2028',
            'alat_id' => $rig->id,
            'durasi_bulan' => 5,
        ]);

        $created = Jadwal::where('label', 'FUTURE-RIG-2028')->first();
        $this->assertNotNull($created);
        $this->assertEquals('2028-07-01', $created->periode_mulai->format('Y-m-d'));

        // Check Jadwal page has the extended timeline
        $jadwalPage = $this->actingAs($user)->get('/jadwal');
        $jadwalPage->assertStatus(200);
        $jadwalPage->assertSee('Jul-28');
        $jadwalPage->assertSee('Nov-28');
        $jadwalPage->assertSee('Dec-28');

        // Check Dashboard page also has the extended timeline
        $dashboardPage = $this->actingAs($user)->get('/dashboard');
        $dashboardPage->assertStatus(200);
        $dashboardPage->assertSee('Jul-28');
        $dashboardPage->assertSee('Dec-28');
    }

    public function test_now_col_matches_current_month(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::find(4);
        $currentMonthLabel = \Carbon\Carbon::now()->format('M-y');

        $jadwalPage = $this->actingAs($user)->get('/jadwal');
        $jadwalPage->assertStatus(200);
        $jadwalPage->assertSee($currentMonthLabel);

        $dashboardPage = $this->actingAs($user)->get('/dashboard');
        $dashboardPage->assertStatus(200);
        $dashboardPage->assertSee($currentMonthLabel);
    }
}

