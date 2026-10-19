<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\AuditLog;
use App\Models\ImportExportLog;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\User;
use App\Services\ExportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_and_worker_cannot_access_export_endpoints(): void
    {
        // Guest
        $resGuest = $this->post('/import-export/count', ['modul' => 'alat']);
        $resGuest->assertRedirect('/login');

        $resGuestExport = $this->post('/import-export/export', ['modul' => 'alat', 'format' => 'xlsx']);
        $resGuestExport->assertRedirect('/login');

        // Worker
        $worker = User::where('jabatan', 'pekerja')->first();

        $resWorkerCount = $this->actingAs($worker)->postJson('/import-export/count', ['modul' => 'alat']);
        $resWorkerCount->assertStatus(403);

        $resWorkerExport = $this->actingAs($worker)->post('/import-export/export', ['modul' => 'alat', 'format' => 'xlsx']);
        $resWorkerExport->assertStatus(403);
    }

    public function test_admin_can_get_live_count_with_filters(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // 1. Total Alat
        $resTotal = $this->actingAs($admin)->postJson('/import-export/count', [
            'modul' => 'alat',
        ]);
        $resTotal->assertOk();
        $resTotal->assertJson(['success' => true, 'count' => Alat::count()]);

        // 2. Filter Alat: Kategori Rig
        $rigCount = Alat::where('kategori', 'rig')->count();
        $resFilter = $this->actingAs($admin)->postJson('/import-export/count', [
            'modul' => 'alat',
            'filters' => ['kategori' => 'Rig'],
        ]);
        $resFilter->assertOk();
        $resFilter->assertJson(['success' => true, 'count' => $rigCount]);

        // 3. Lokasi count
        $resLokasi = $this->actingAs($admin)->postJson('/import-export/count', [
            'modul' => 'lokasi',
        ]);
        $resLokasi->assertOk();
        $resLokasi->assertJson(['success' => true, 'count' => Lokasi::count()]);

        // 4. Jadwal count
        $resJadwal = $this->actingAs($admin)->postJson('/import-export/count', [
            'modul' => 'jadwal',
        ]);
        $resJadwal->assertOk();
        $resJadwal->assertJson(['success' => true, 'count' => Jadwal::count()]);

        // 5. Audit Log count
        $resAudit = $this->actingAs($admin)->postJson('/import-export/count', [
            'modul' => 'audit',
        ]);
        $resAudit->assertOk();
        $resAudit->assertJson(['success' => true, 'count' => AuditLog::count()]);
    }

    public function test_export_alat_as_xlsx_and_csv(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();
        $totalAlat = Alat::count();

        // Export XLSX
        $resXlsx = $this->actingAs($admin)->post('/import-export/export', [
            'modul' => 'alat',
            'format' => 'xlsx',
        ]);

        $resXlsx->assertOk();
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $resXlsx->headers->get('content-type')
        );
        $today = now()->format('Y-m-d');
        $this->assertStringContainsString("alat_{$today}.xlsx", (string) $resXlsx->headers->get('content-disposition'));

        // Riwayat tercipta
        $this->assertDatabaseHas('import_export_logs', [
            'user_id' => $admin->id,
            'jenis' => 'export',
            'modul' => 'alat',
            'nama_file' => "alat_{$today}.xlsx",
            'baris_berhasil' => $totalAlat,
            'status' => 'sukses',
        ]);

        // Audit log live tercatat
        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'tambah',
            'modul' => 'sistem',
            'user_id' => $admin->id,
        ]);

        // Export CSV
        $resCsv = $this->actingAs($admin)->post('/import-export/export', [
            'modul' => 'alat',
            'format' => 'csv',
        ]);

        $resCsv->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $resCsv->headers->get('content-type'));
        $content = $resCsv->getContent();

        // Verify UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        // Verify exact headers as specified in PRD §6
        $this->assertStringContainsString('"Nama","Kategori","Merek","Model / Tipe","Tahun Perolehan","No. Seri","Status","Keterangan"', $content);
    }

    public function test_export_lokasi_and_jadwal_and_audit_log_headers(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Lokasi CSV
        $resLokasi = $this->actingAs($admin)->post('/import-export/export', [
            'modul' => 'lokasi',
            'format' => 'csv',
        ]);
        $resLokasi->assertOk();
        $this->assertStringContainsString('"Kode Lapangan","Nama Tempat","Provinsi","Kota / Kabupaten","Kecamatan","Keterangan"', $resLokasi->getContent());

        // Jadwal CSV
        $resJadwal = $this->actingAs($admin)->post('/import-export/export', [
            'modul' => 'jadwal',
            'format' => 'csv',
        ]);
        $resJadwal->assertOk();
        $this->assertStringContainsString('"Rig","Nama Sumur / Pekerjaan","Kategori","Status","Bulan Mulai","Durasi (bulan)","Catatan"', $resJadwal->getContent());

        // Audit Log CSV
        $resAudit = $this->actingAs($admin)->post('/import-export/export', [
            'modul' => 'audit_log',
            'format' => 'csv',
        ]);
        $resAudit->assertOk();
        $this->assertStringContainsString('"Username","Nama","Gmail","Jabatan","Tanggal / Waktu","Aksi","Modul","Keterangan"', $resAudit->getContent());
    }

    public function test_formula_injection_is_neutralized_in_export(): void
    {
        $service = app(ExportService::class);

        // Characters =, +, -, @ must be prefixed with single quote
        $this->assertEquals("'=1+1", $service->sanitizeFormula('=1+1'));
        $this->assertEquals("'+12345", $service->sanitizeFormula('+12345'));
        $this->assertEquals("'-calc()", $service->sanitizeFormula('-calc()'));
        $this->assertEquals("'@SUM(A1:A5)", $service->sanitizeFormula('@SUM(A1:A5)'));

        // Normal strings and non-strings untouched
        $this->assertEquals("Normal String", $service->sanitizeFormula("Normal String"));
        $this->assertEquals(2026, $service->sanitizeFormula(2026));
    }

    public function test_export_aborts_when_count_is_zero(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Filter lokasi yang tidak ada datanya
        $res = $this->actingAs($admin)->post('/import-export/export', [
            'modul' => 'lokasi',
            'format' => 'csv',
            'filters' => ['provinsi' => 'Provinsi Antah Berantah'],
        ]);

        $res->assertStatus(422);
    }
}
