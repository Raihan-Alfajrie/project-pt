<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\ImportExportLog;
use App\Models\Lokasi;
use App\Models\User;
use App\Services\ImportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Tests\TestCase;

class ImportPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Helper to create a temporary XLSX UploadedFile.
     */
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

    public function test_guest_and_worker_cannot_access_import_endpoints(): void
    {
        // Guest
        $resGuest = $this->get('/import-export/template?modul=alat');
        $resGuest->assertRedirect('/login');

        $resGuestUpload = $this->post('/import-export/upload-preview', ['modul' => 'alat']);
        $resGuestUpload->assertRedirect('/login');

        $resGuestErr = $this->post('/import-export/error-report', ['modul' => 'alat']);
        $resGuestErr->assertRedirect('/login');

        // Worker
        $worker = User::where('jabatan', 'pekerja')->first();

        $resWorkerTmpl = $this->actingAs($worker)->get('/import-export/template?modul=alat');
        $resWorkerTmpl->assertStatus(403);

        $resWorkerUpload = $this->actingAs($worker)->postJson('/import-export/upload-preview', ['modul' => 'alat']);
        $resWorkerUpload->assertStatus(403);

        $resWorkerErr = $this->actingAs($worker)->post('/import-export/error-report', ['modul' => 'alat']);
        $resWorkerErr->assertStatus(403);
    }

    public function test_admin_can_download_templates_for_all_importable_modules(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        foreach (['alat', 'lokasi', 'jadwal'] as $modul) {
            $response = $this->actingAs($admin)->get("/import-export/template?modul={$modul}");
            $response->assertOk();
            $response->assertHeader('Content-Disposition', "attachment; filename=\"template_{$modul}.xlsx\"");

            // Validate that the returned content has 2 sheets: Template & Petunjuk
            $tempFile = tempnam(sys_get_temp_dir(), 'verify_tmpl_') . '.xlsx';
            file_put_contents($tempFile, $response->getContent());

            $spreadsheet = IOFactory::load($tempFile);
            $sheetNames = $spreadsheet->getSheetNames();
            $this->assertContains('Template', $sheetNames);
            $this->assertContains('Petunjuk', $sheetNames);

            $spreadsheet->disconnectWorksheets();
            @unlink($tempFile);
        }
    }

    public function test_upload_fails_if_file_extension_is_invalid(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $invalidFile,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('File tidak bisa dibaca sebagai Excel/CSV', $response->json('message'));
    }

    public function test_upload_fails_if_required_columns_are_missing(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Only "Nama" and "Kategori" provided, "Merek" and "No. Seri" missing
        $file = $this->createExcelFile([
            ['Nama', 'Kategori'],
            ['Rig Test', 'Rig'],
        ]);

        $response = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Kolom wajib tidak ditemukan', $response->json('message'));
        $this->assertStringContainsString('Merek', $response->json('message'));
        $this->assertStringContainsString('No. Seri', $response->json('message'));
    }

    public function test_upload_fails_if_file_has_no_data_rows(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Only header row
        $file = $this->createExcelFile([
            ['Nama', 'Kategori', 'Merek', 'Model / Tipe', 'Tahun Perolehan', 'No. Seri', 'Status', 'Keterangan'],
        ]);

        $response = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'File kosong atau tidak ada baris data di bawah baris judul kolom.',
        ]);
    }

    public function test_upload_preview_successfully_validates_and_classifies_alat(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Existing alat in seeder has No. Seri "RIG-3610-2016"
        $file = $this->createExcelFile([
            ['Nama', 'Kategori', 'Merek', 'Model / Tipe', 'Tahun Perolehan', 'No. Seri', 'Status', 'Keterangan'],
            // Row 2: Valid new item
            ['Rig Baru 101', 'Rig', 'PDSI', 'SKY-100', 2022, 'RIG-NEW-101', 'Aktif', 'Rig baru'],
            // Row 3: Existing item in DB
            ['PDSI #36.1/SKYTOP650-M', 'Rig', 'PDSI', 'SKYTOP650-M', 2016, 'RIG-3610-2016', 'Aktif', 'Rig sudah ada'],
            // Row 4: Duplicate within file (same serial number as row 2)
            ['Rig Duplikat', 'Rig', 'PDSI', 'SKY-100', 2022, 'RIG-NEW-101', 'Aktif', 'Duplikat no seri'],
            // Row 5: Validation error (invalid status & invalid tahun)
            ['Rig Error', 'Rig', 'PDSI', '', 1950, 'RIG-ERR-001', 'StatusPalsu', 'Error data'],
        ]);

        $response = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'modul' => 'alat', 'total' => 4]);

        $data = $response->json();
        $this->assertNotEmpty($data['token']);
        $this->assertCount(4, $data['rows']);

        // Check Row 2 (New)
        $row2 = $data['rows'][0];
        $this->assertEquals(2, $row2['rowNo']);
        $this->assertEmpty($row2['errs']);
        $this->assertFalse($row2['existsInDb']);

        // Check Row 3 (Existing in DB)
        $row3 = $data['rows'][1];
        $this->assertEquals(3, $row3['rowNo']);
        $this->assertEmpty($row3['errs']);
        $this->assertTrue($row3['existsInDb']);

        // Check Row 4 (Duplicate in file)
        $row4 = $data['rows'][2];
        $this->assertEquals(4, $row4['rowNo']);
        $this->assertNotEmpty($row4['errs']);
        $this->assertStringContainsString('Duplikat di dalam file (sama dengan baris 2)', $row4['errs'][0]);

        // Check Row 5 (Domain errors)
        $row5 = $data['rows'][3];
        $this->assertEquals(5, $row5['rowNo']);
        $this->assertNotEmpty($row5['errs']);
        $this->assertTrue(collect($row5['errs'])->contains(fn($e) => str_contains($e, 'Status harus Aktif')));
        $this->assertTrue(collect($row5['errs'])->contains(fn($e) => str_contains($e, '1980–2026')));

        // Check token cached in memory
        $this->assertTrue(Cache::has("impex_preview_{$data['token']}"));
    }

    public function test_upload_preview_validates_lokasi_and_jadwal(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // 1. Lokasi: invalid provinsi
        $lokasiFile = $this->createExcelFile([
            ['Kode Lapangan', 'Nama Tempat', 'Provinsi', 'Kota / Kabupaten', 'Kecamatan', 'Keterangan'],
            ['LOK-99', 'Lapangan Baru', 'Provinsi Fantasi', '', '', 'Catatan'],
        ]);

        $resLokasi = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'lokasi',
            'file' => $lokasiFile,
        ]);
        $resLokasi->assertOk();
        $lokRow = $resLokasi->json('rows.0');
        $this->assertTrue(collect($lokRow['errs'])->contains(fn($e) => str_contains($e, 'Provinsi tidak dikenal')));

        // 2. Jadwal: Rig not in Alat, Bulan invalid
        $jadwalFile = $this->createExcelFile([
            ['Rig', 'Nama Sumur / Pekerjaan', 'Kategori', 'Status', 'Bulan Mulai', 'Durasi (bulan)', 'Catatan'],
            ['Rig Antah Berantah', 'Sumur X', 'Konstruksi', 'Menunggu / Progres', '2025-01', 1, 'Catatan'],
        ]);

        $resJadwal = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'jadwal',
            'file' => $jadwalFile,
        ]);
        $resJadwal->assertOk();
        $jadRow = $resJadwal->json('rows.0');
        $this->assertTrue(collect($jadRow['errs'])->contains(fn($e) => str_contains($e, 'Rig tidak ditemukan di data Alat')));
        $this->assertTrue(collect($jadRow['errs'])->contains(fn($e) => str_contains($e, 'Bulan Mulai di luar rentang Jun-26 s/d Des-27')));
    }

    public function test_admin_can_download_error_report(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // Create a preview with errors
        $file = $this->createExcelFile([
            ['Nama', 'Kategori', 'Merek', 'Model / Tipe', 'Tahun Perolehan', 'No. Seri', 'Status', 'Keterangan'],
            ['Rig Error', 'Rig', 'PDSI', '', 1950, 'RIG-ERR-001', 'StatusSalah', 'Catatan'],
        ]);

        $previewRes = $this->actingAs($admin)->postJson('/import-export/upload-preview', [
            'modul' => 'alat',
            'file' => $file,
        ]);
        $previewRes->assertOk();
        $token = $previewRes->json('token');

        // Request error report using the token
        $response = $this->actingAs($admin)->post('/import-export/error-report', [
            'token' => $token,
        ]);

        $response->assertOk();
        $today = now()->format('Y-m-d');
        $response->assertHeader('Content-Disposition', "attachment; filename=\"laporan_error_alat_{$today}.xlsx\"");

        // Verify the file content contains the Error sheet
        $tempFile = tempnam(sys_get_temp_dir(), 'verify_err_') . '.xlsx';
        file_put_contents($tempFile, $response->getContent());

        $spreadsheet = IOFactory::load($tempFile);
        $this->assertContains('Error', $spreadsheet->getSheetNames());

        $sheet = $spreadsheet->getSheetByName('Error');
        // Row 1 should have "Baris Excel" and "Pesan Error"
        $this->assertEquals('Baris Excel', $sheet->getCell('A1')->getValue());
        $this->assertEquals('Pesan Error', $sheet->getCell('B1')->getValue());
        $this->assertEquals(2, $sheet->getCell('A2')->getValue());

        $spreadsheet->disconnectWorksheets();
        @unlink($tempFile);
    }
}
