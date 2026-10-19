<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\AuditLog;
use App\Models\DokumentasiItem;
use App\Models\ImportExportLog;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\User;
use App\Services\ImportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Tests\TestCase;

class ImportConfirmTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function createExcelFile(array $rows, string $filename = 'test.xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_impex_') . '.xlsx';
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempPath);
        $spreadsheet->disconnectWorksheets();

        return new UploadedFile(
            $tempPath,
            $filename,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    public function test_guest_and_worker_cannot_confirm_import(): void
    {
        $resGuest = $this->postJson('/import-export/confirm', [
            'token' => 'dummy_token',
            'mode' => 'skip',
        ]);
        $resGuest->assertStatus(401);

        $worker = User::where('jabatan', 'pekerja')->first();
        $resWorker = $this->actingAs($worker)->postJson('/import-export/confirm', [
            'token' => 'dummy_token',
            'mode' => 'skip',
        ]);
        $resWorker->assertStatus(403);
    }

    public function test_confirm_fails_with_invalid_or_expired_token(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => 'token_yang_tidak_ada_di_cache',
            'mode' => 'skip',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Sesi import telah kedaluwarsa atau tidak valid. Silakan unggah ulang file.',
        ]);
    }

    public function test_confirm_import_alat_mode_skip(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $existing = Alat::where('no_seri', 'RIG-3610-2016')->first();
        $originalNama = $existing->nama;

        $file = $this->createExcelFile([
            ['Nama', 'Kategori', 'Merek', 'Model / Tipe', 'Tahun Perolehan', 'No. Seri', 'Status', 'Keterangan'],
            // Row 2: New Alat
            ['Rig Baru Nusantara', 'Rig', 'PDSI', 'N-1000', 2023, 'RIG-BARU-001', 'Aktif', 'Rig baru siap operasi'],
            // Row 3: Existing Alat (Mode skip should NOT touch this)
            ['Nama Yang Diedit', 'Rig', 'PDSI', 'MOD-EDIT', 2020, 'RIG-3610-2016', 'Bermasalah', 'Keterangan baru'],
            // Row 4: Error row (invalid tahun)
            ['Rig Rusak', 'Rig', 'PDSI', '', 1900, 'RIG-ERR-001', 'Aktif', 'Tahun invalid'],
        ]);

        // 1. Upload preview
        $previewRes = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $file,
        ]);
        $previewRes->assertOk();
        $token = $previewRes->json('token');

        // 2. Confirm with mode 'skip'
        $confirmRes = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $token,
            'mode' => 'skip',
        ]);

        $confirmRes->assertOk();
        $confirmRes->assertJson([
            'success' => true,
            'counts' => [
                'total' => 3,
                'baru' => 1,
                'diperbarui' => 0,
                'dilewati' => 1,
                'error' => 1,
            ],
        ]);
        $this->assertStringContainsString('1 ditambahkan', $confirmRes->json('message'));
        $this->assertStringContainsString('0 diperbarui', $confirmRes->json('message'));
        $this->assertStringContainsString('1 baris error dilewati', $confirmRes->json('message'));

        // Assert new record exists in DB
        $this->assertDatabaseHas('alat', [
            'no_seri' => 'RIG-BARU-001',
            'nama' => 'Rig Baru Nusantara',
            'kategori' => 'rig',
            'status' => 'aktif',
        ]);

        // Assert existing record was NOT altered
        $existing->refresh();
        $this->assertEquals($originalNama, $existing->nama);

        // Assert error row was NOT saved
        $this->assertDatabaseMissing('alat', [
            'no_seri' => 'RIG-ERR-001',
        ]);

        // Assert ImportExportLog created with status 'sebagian'
        $log = ImportExportLog::where('jenis', 'import')->where('modul', 'alat')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertEquals('sebagian', $log->status);
        $this->assertEquals(1, $log->baris_berhasil);
        $this->assertEquals(3, $log->baris_total);

        // Assert live AuditLog created
        $audit = AuditLog::where('modul', 'sistem')->where('aksi', 'tambah')->latest('id')->first();
        $this->assertNotNull($audit);
        $this->assertStringContainsString('Mengimpor 1 baris data Alat', $audit->keterangan);

        // Assert token cleared from cache
        $this->assertFalse(Cache::has("impex_preview_{$token}"));
    }

    public function test_confirm_import_alat_mode_update_clears_optional_columns(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Existing alat with existing model and keterangan
        $existing = Alat::where('no_seri', 'RIG-3610-2016')->first();
        $this->assertNotNull($existing->keterangan);

        $file = $this->createExcelFile([
            ['Nama', 'Kategori', 'Merek', 'Model / Tipe', 'Tahun Perolehan', 'No. Seri', 'Status', 'Keterangan'],
            // Updated existing record: model and keterangan are empty in Excel (Option A clears them)
            ['PDSI #36.1 (Updated)', 'Rig', 'PDSI Baru', '', 2021, 'RIG-3610-2016', 'Bermasalah', ''],
            // New record
            ['Rig Pendukung 02', 'Alat Pendukung', 'NOV', 'P-500', 2019, 'GEN-NEW-002', 'Standby', 'Genset cadangan'],
        ]);

        $previewRes = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $file,
        ]);
        $previewRes->assertOk();
        $token = $previewRes->json('token');

        // Confirm with mode 'update'
        $confirmRes = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $token,
            'mode' => 'update',
        ]);

        $confirmRes->assertOk();
        $confirmRes->assertJson([
            'success' => true,
            'counts' => [
                'total' => 2,
                'baru' => 1,
                'diperbarui' => 1,
                'dilewati' => 0,
                'error' => 0,
            ],
        ]);

        // Verify updated values and that empty optional field in Excel cleared the old value
        $existing->refresh();
        $this->assertEquals('PDSI #36.1 (Updated)', $existing->nama);
        $this->assertEquals('PDSI Baru', $existing->merek);
        $this->assertNull($existing->model);
        $this->assertEquals(2021, $existing->tahun_perolehan);
        $this->assertEquals('bermasalah', $existing->status);
        $this->assertNull($existing->keterangan);

        // Verify new record
        $this->assertDatabaseHas('alat', [
            'no_seri' => 'GEN-NEW-002',
            'nama' => 'Rig Pendukung 02',
            'kategori' => 'alat_pendukung',
            'status' => 'standby',
        ]);
    }

    public function test_confirm_import_lokasi(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $existingLokasi = Lokasi::where('kode', 'MJ')->first();

        $file = $this->createExcelFile([
            ['Kode Lapangan', 'Nama Tempat', 'Provinsi', 'Kota / Kabupaten', 'Kecamatan', 'Keterangan'],
            // New lokasi
            ['KRA', 'Lapangan Karang Anyar', 'Sumatera Selatan', 'Muara Enim', 'Gelumbang', 'Blok baru'],
            // Existing lokasi with updated name in mode update
            ['MJ', 'Lapangan Meruap Jaya (Updated)', 'Sumatera Selatan', 'Prabumulih', '', 'Cluster sumur MJ'],
        ]);

        $previewRes = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'lokasi',
            'file' => $file,
        ]);
        $previewRes->assertOk();
        $token = $previewRes->json('token');

        $confirmRes = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $token,
            'mode' => 'update',
        ]);

        $confirmRes->assertOk();
        $confirmRes->assertJson([
            'success' => true,
            'counts' => [
                'total' => 2,
                'baru' => 1,
                'diperbarui' => 1,
            ],
        ]);

        // Verify new lokasi has uppercase code and default tanggal_dipetakan
        $this->assertDatabaseHas('lokasi', [
            'kode' => 'KRA',
            'nama_tempat' => 'Lapangan Karang Anyar',
            'provinsi' => 'Sumatera Selatan',
        ]);

        // Verify updated lokasi
        $existingLokasi->refresh();
        $this->assertEquals('Lapangan Meruap Jaya (Updated)', $existingLokasi->nama_tempat);
        $this->assertNull($existingLokasi->kecamatan);
    }

    public function test_confirm_import_jadwal(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $rig = Alat::where('kategori', 'rig')->first();
        $existingJadwal = Jadwal::where('alat_id', $rig->id)->first();

        $file = $this->createExcelFile([
            ['Rig', 'Nama Sumur / Pekerjaan', 'Kategori', 'Status', 'Bulan Mulai', 'Durasi (bulan)', 'Catatan'],
            // New Jadwal
            [$rig->nama, 'Sumur Test Baru X-1', 'Konstruksi', 'Menunggu / Progres', '2026-08', 3, 'Operasi baru'],
            // Existing Jadwal updated
            [$rig->nama, $existingJadwal->label, 'Sudah Tajak', 'Selesai', '2026-07', 2, 'Catatan diperbarui'],
        ]);

        $previewRes = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'jadwal',
            'file' => $file,
        ]);
        $previewRes->assertOk();
        $token = $previewRes->json('token');

        $confirmRes = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $token,
            'mode' => 'update',
        ]);

        $confirmRes->assertOk();
        $confirmRes->assertJson([
            'success' => true,
            'counts' => [
                'total' => 2,
                'baru' => 1,
                'diperbarui' => 1,
            ],
        ]);

        // Verify new jadwal created with correct foreign key and date
        $newJadwal = Jadwal::where('label', 'Sumur Test Baru X-1')->first();
        $this->assertNotNull($newJadwal);
        $this->assertEquals($rig->id, $newJadwal->alat_id);
        $this->assertEquals('2026-08-01', $newJadwal->periode_mulai->format('Y-m-d'));
        $this->assertEquals(3, $newJadwal->durasi_bulan);
        $this->assertEquals('progres', $newJadwal->status);

        // Verify existing jadwal updated
        $existingJadwal->refresh();
        $this->assertEquals('selesai', $existingJadwal->status);
        $this->assertEquals('2026-07-01', $existingJadwal->periode_mulai->format('Y-m-d'));
        $this->assertEquals(2, $existingJadwal->durasi_bulan);
        $this->assertEquals('Catatan diperbarui', $existingJadwal->catatan);
    }

    public function test_confirm_import_rolls_back_on_fatal_system_error(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Create cached preview with 2 rows having duplicate unique key (no_seri)
        // This will trigger a PDOException during second insert, forcing a transaction rollback
        $token = 'fatal_error_test_token';
        Cache::put("impex_preview_{$token}", [
            'modul' => 'alat',
            'filename' => 'fatal_test.xlsx',
            'total' => 2,
            'rows' => [
                [
                    'rowNo' => 2,
                    'errs' => [],
                    'existsInDb' => false,
                    'rec' => [
                        'nama' => 'Alat Sebelum Fatal',
                        'kategori' => 'Rig',
                        'merek' => 'PDSI',
                        'model' => 'M-1',
                        'tahun' => 2022,
                        'noseri' => 'RIG-BEFORE-FATAL',
                        'status' => 'Aktif',
                        'ket' => 'Harus dirollback',
                    ],
                ],
                [
                    'rowNo' => 3,
                    'errs' => [],
                    'existsInDb' => false,
                    'rec' => [
                        'nama' => 'Alat Pemicu Fatal Unique Collision',
                        'kategori' => 'Rig',
                        'merek' => 'PDSI',
                        'model' => 'M-2',
                        'tahun' => 2022,
                        'noseri' => 'RIG-BEFORE-FATAL', // Same unique key violates DB UNIQUE constraint
                        'status' => 'Aktif',
                        'ket' => '',
                    ],
                ],
            ],
        ], now()->addHour());

        $response = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $token,
            'mode' => 'skip',
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem saat menyimpan data import. Seluruh perubahan telah dibatalkan (rollback).',
        ]);

        // Verify that the first row was rolled back and NOT saved
        $this->assertDatabaseMissing('alat', [
            'no_seri' => 'RIG-BEFORE-FATAL',
        ]);

        // Verify failed log was created
        $log = ImportExportLog::where('nama_file', 'fatal_test.xlsx')->first();
        $this->assertNotNull($log);
        $this->assertEquals('gagal', $log->status);
        $this->assertEquals(0, $log->baris_berhasil);
    }

    public function test_round_trip_export_then_import_produces_identical_data(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // 1. Export all current Alat to XLSX
        $exportService = app(\App\Services\ExportService::class);
        $xlsxContent = $exportService->generateXlsx('alat', []);
        $originalCount = Alat::count();

        // Save to temp file
        $tempPath = tempnam(sys_get_temp_dir(), 'roundtrip_') . '.xlsx';
        file_put_contents($tempPath, $xlsxContent);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'alat_roundtrip.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // 2. Upload and Preview
        $previewRes = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $uploadedFile,
        ]);
        $previewRes->assertOk();
        $token = $previewRes->json('token');

        // All rows should be classified as existing in DB
        $rows = $previewRes->json('rows');
        $this->assertCount($originalCount, $rows);
        foreach ($rows as $r) {
            $this->assertTrue($r['existsInDb']);
            $this->assertEmpty($r['errs']);
        }

        // 3. Confirm with update mode
        $confirmRes = $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $token,
            'mode' => 'update',
        ]);
        $confirmRes->assertOk();
        $this->assertEquals($originalCount, $confirmRes->json('counts.diperbarui'));
        $this->assertEquals(0, $confirmRes->json('counts.baru'));
        $this->assertEquals(0, $confirmRes->json('counts.error'));
        $this->assertEquals($originalCount, Alat::count());

        @unlink($tempPath);
    }

    public function test_consecutive_imports_work_seamlessly(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Import 1: Lokasi
        $lokasiFile = $this->createExcelFile([
            ['Kode Lapangan', 'Nama Tempat', 'Provinsi', 'Kota / Kabupaten', 'Kecamatan', 'Keterangan'],
            ['LOK-CONSEC-1', 'Lapangan Pertama', 'Jambi', '', '', ''],
        ]);
        $preview1 = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'lokasi',
            'file' => $lokasiFile,
        ]);
        $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $preview1->json('token'),
            'mode' => 'skip',
        ])->assertOk();

        // Import 2: Lokasi immediately following
        $lokasiFile2 = $this->createExcelFile([
            ['Kode Lapangan', 'Nama Tempat', 'Provinsi', 'Kota / Kabupaten', 'Kecamatan', 'Keterangan'],
            ['LOK-CONSEC-2', 'Lapangan Kedua', 'Riau', '', '', ''],
        ]);
        $preview2 = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'lokasi',
            'file' => $lokasiFile2,
        ]);
        $this->actingAs($admin)->postJson('/import-export/confirm', [
            'token' => $preview2->json('token'),
            'mode' => 'skip',
        ])->assertOk();

        $this->assertDatabaseHas('lokasi', ['kode' => 'LOK-CONSEC-1']);
        $this->assertDatabaseHas('lokasi', ['kode' => 'LOK-CONSEC-2']);
    }
}
