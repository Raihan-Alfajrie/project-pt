<?php

namespace Tests\Feature;

use App\Models\ImportExportLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportExportFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/import-export');

        $response->assertRedirect('/login');
    }

    public function test_worker_is_forbidden_from_import_export_page(): void
    {
        $worker = User::where('jabatan', 'pekerja')->first();

        $response = $this->actingAs($worker)->get('/import-export');

        $response->assertStatus(403);
    }

    public function test_worker_sidebar_does_not_show_import_export_link(): void
    {
        $worker = User::where('jabatan', 'pekerja')->first();

        $response = $this->actingAs($worker)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee(route('import-export.index'));
        $response->assertDontSee('Import / Export');
    }

    public function test_admin_can_access_import_export_page(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/import-export');

        $response->assertOk();
        $response->assertSee('Export Data');
        $response->assertSee('Import Data');
        $response->assertSee('Riwayat Import / Export');
        $response->assertSee('Pilih modul &amp; template', false);
    }

    public function test_admin_sidebar_shows_import_export_link(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee(route('import-export.index'));
        $response->assertSee('Import / Export');
    }

    public function test_import_export_log_model_and_factory_works_correctly(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $log = ImportExportLog::factory()->create([
            'user_id' => $admin->id,
            'jenis' => 'export',
            'modul' => 'jadwal',
            'nama_file' => 'jadwal_2026-10-11.xlsx',
            'baris_berhasil' => 32,
            'baris_total' => 32,
            'status' => 'sukses',
        ]);

        $this->assertDatabaseHas('import_export_logs', [
            'id' => $log->id,
            'nama_file' => 'jadwal_2026-10-11.xlsx',
            'modul' => 'jadwal',
            'status' => 'sukses',
        ]);

        $this->assertEquals('Jadwal', $log->modul_label);
        $this->assertEquals('Export', $log->jenis_label);
        $this->assertEquals('Sukses', $log->status_label);
        $this->assertEquals('32', $log->baris_display);

        // Uji partial row count display
        $logPartial = ImportExportLog::factory()->create([
            'user_id' => $admin->id,
            'baris_berhasil' => 5,
            'baris_total' => 6,
            'status' => 'sebagian',
        ]);

        $this->assertEquals('5 / 6', $logPartial->baris_display);
    }
}
