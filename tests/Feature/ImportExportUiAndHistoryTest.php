<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\ImportExportLog;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportExportUiAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_worker_cannot_view_import_export_page(): void
    {
        $worker = User::where('jabatan', 'pekerja')->first();

        $response = $this->actingAs($worker)->get('/import-export');
        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/import-export');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_import_export_page_with_accurate_counts(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/import-export');
        $response->assertOk();
        $response->assertSee('Export Data');
        $response->assertSee('Import Data');
        $response->assertSee('Riwayat Import / Export');

        // Check module counts in view
        $response->assertSee(Alat::count() . ' baris');
        $response->assertSee(Lokasi::count() . ' baris');
        $response->assertSee(Jadwal::count() . ' baris');
    }

    public function test_riwayat_renders_empty_state_when_no_records_exist(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Ensure table is empty
        ImportExportLog::query()->delete();

        $response = $this->actingAs($admin)->get('/import-export');
        $response->assertOk();
        $response->assertSee('Belum ada riwayat import / export.');
    }

    public function test_riwayat_renders_records_with_correct_pills_and_labels(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        ImportExportLog::query()->delete();

        // 1. Sukses import
        ImportExportLog::create([
            'user_id' => $admin->id,
            'jenis' => 'import',
            'modul' => 'alat',
            'nama_file' => 'import_alat_nov26.xlsx',
            'baris_berhasil' => 10,
            'baris_total' => 10,
            'status' => 'sukses',
        ]);

        // 2. Sebagian import
        ImportExportLog::create([
            'user_id' => $admin->id,
            'jenis' => 'import',
            'modul' => 'lokasi',
            'nama_file' => 'import_lokasi_sebagian.xlsx',
            'baris_berhasil' => 8,
            'baris_total' => 10,
            'status' => 'sebagian',
        ]);

        // 3. Sukses export
        ImportExportLog::create([
            'user_id' => $admin->id,
            'jenis' => 'export',
            'modul' => 'jadwal',
            'nama_file' => 'jadwal_export.xlsx',
            'baris_berhasil' => 25,
            'baris_total' => 25,
            'status' => 'sukses',
        ]);

        $response = $this->actingAs($admin)->get('/import-export');
        $response->assertOk();

        $response->assertSee('import_alat_nov26.xlsx');
        $response->assertSee('import_lokasi_sebagian.xlsx');
        $response->assertSee('jadwal_export.xlsx');
        $response->assertSee('8 / 10');
        $response->assertSee('Sebagian');
        $response->assertSee('Sukses');
        $response->assertDontSee('Belum ada riwayat import / export.');
    }

    public function test_history_is_limited_to_50_latest_records(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        ImportExportLog::query()->delete();

        // Create 60 records
        for ($i = 1; $i <= 60; $i++) {
            ImportExportLog::create([
                'user_id' => $admin->id,
                'jenis' => 'export',
                'modul' => 'alat',
                'nama_file' => "export_batch_{$i}.xlsx",
                'baris_berhasil' => $i,
                'baris_total' => $i,
                'status' => 'sukses',
                'created_at' => now()->subMinutes(60 - $i),
            ]);
        }

        $response = $this->actingAs($admin)->get('/import-export');
        $response->assertOk();

        // Newest record should be visible
        $response->assertSee('export_batch_60.xlsx');
        $response->assertSee('export_batch_11.xlsx');

        // Oldest record beyond 50 limit should NOT be in the view
        $response->assertDontSee('export_batch_1.xlsx');
        $response->assertDontSee('export_batch_10.xlsx');
    }
}
