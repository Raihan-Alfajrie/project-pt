<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\AuditLog;
use App\Models\DokumentasiItem;
use App\Models\ImportExportLog;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Services\AuditLogService;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImportExportController extends Controller
{
    /**
     * Display the Import / Export dashboard page (Administrator exclusive).
     */
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat mengakses menu Import / Export.');

        // Live row counts per module for Export cards
        $counts = [
            'alat' => Alat::count(),
            'lokasi' => Lokasi::count(),
            'jadwal' => Jadwal::count(),
            'audit' => AuditLog::count(),
        ];

        // Dynamic filters definition
        $provinsis = Lokasi::distinct()->orderBy('provinsi')->pluck('provinsi')->toArray();
        if (empty($provinsis)) {
            $provinsis = ['Sumatera Selatan'];
        }

        $jadwalKategoris = DokumentasiItem::orderBy('urutan')->pluck('label')->toArray();
        if (empty($jadwalKategoris)) {
            $jadwalKategoris = [
                'Sudah Tajak', 'MIRU / RFD', 'Konstruksi', 'Lahan Bebas',
                'Pembayaran / Pengajuan', 'Finalisasi KJPP', 'ARS / Survei Sosialisasi',
                'Proses Pengadaan Rig', 'Rig Acceptance', 'Lain-lain',
                'Eksplorasi / Kompleks', 'Kontrak Habis'
            ];
        }

        $filters = [
            'alat' => [
                ['id' => 'kategori', 'label' => 'Kategori', 'opts' => ['Rig', 'Alat Pendukung']],
                ['id' => 'status', 'label' => 'Status', 'opts' => ['Aktif', 'Tidak Aktif', 'Bermasalah']],
            ],
            'lokasi' => [
                ['id' => 'provinsi', 'label' => 'Provinsi', 'opts' => $provinsis],
            ],
            'jadwal' => [
                ['id' => 'status', 'label' => 'Status', 'opts' => ['Selesai', 'Menunggu / Progres', 'Kendala']],
                ['id' => 'kat', 'label' => 'Kategori', 'opts' => $jadwalKategoris],
            ],
            'audit' => [
                ['id' => 'aksi', 'label' => 'Aksi', 'opts' => ['Login', 'Logout', 'Tambah', 'Edit', 'Hapus']],
                ['id' => 'modul', 'label' => 'Modul', 'opts' => ['Alat', 'Lokasi', 'Jadwal', 'User', 'Sistem']],
            ],
        ];

        // 50 latest history entries
        $riwayats = ImportExportLog::with('user')
            ->orderByDesc('id')
            ->take(50)
            ->get();

        return view('import-export.index', compact('counts', 'filters', 'riwayats'));
    }

    /**
     * Get real-time row count for a module given dynamic filters.
     */
    public function count(Request $request, ExportService $exportService): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'modul' => ['required', 'string', 'in:alat,lokasi,jadwal,audit,audit_log'],
            'filters' => ['nullable', 'array'],
        ]);

        $modul = $validated['modul'];
        $filters = $validated['filters'] ?? [];

        $count = $exportService->countRows($modul, $filters);

        return response()->json([
            'success' => true,
            'modul' => $modul,
            'count' => $count,
        ]);
    }

    /**
     * Download exported data in .xlsx or .csv format.
     */
    public function export(Request $request, ExportService $exportService): Response
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat mengekspor data.');

        $validated = $request->validate([
            'modul' => ['required', 'string', 'in:alat,lokasi,jadwal,audit,audit_log'],
            'format' => ['required', 'string', 'in:xlsx,csv'],
            'filters' => ['nullable', 'array'],
        ]);

        $modul = $validated['modul'];
        $format = $validated['format'];
        $filters = $validated['filters'] ?? [];

        $count = $exportService->countRows($modul, $filters);
        abort_if($count === 0, 422, 'Tidak ada baris yang sesuai dengan filter untuk diekspor.');

        $filePrefix = match ($modul) {
            'alat' => 'alat',
            'lokasi' => 'lokasi',
            'jadwal' => 'jadwal',
            default => 'audit_log',
        };

        $today = now()->format('Y-m-d');
        $filename = "{$filePrefix}_{$today}.{$format}";

        if ($format === 'xlsx') {
            $content = $exportService->generateXlsx($modul, $filters);
            $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        } else {
            $content = $exportService->generateCsv($modul, $filters);
            $mimeType = 'text/csv; charset=UTF-8';
        }

        // Record history entry into import_export_logs
        $logModul = ($modul === 'audit' || $modul === 'audit_log') ? 'audit_log' : $modul;
        ImportExportLog::create([
            'user_id' => auth()->id(),
            'jenis' => 'export',
            'modul' => $logModul,
            'nama_file' => $filename,
            'baris_berhasil' => $count,
            'baris_total' => $count,
            'status' => 'sukses',
        ]);

        // Record entry into live audit_log
        AuditLogService::log(
            'tambah',
            'sistem',
            "Mengekspor {$count} baris data " . ucfirst($logModul) . " ke file {$filename}"
        );

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    /**
     * Download Excel template with Template and Petunjuk sheets.
     */
    public function template(Request $request, ImportService $importService): Response
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat mengunduh template.');

        $validated = $request->validate([
            'modul' => ['required', 'string', 'in:alat,lokasi,jadwal'],
        ]);

        $modul = $validated['modul'];
        $content = $importService->generateTemplate($modul);
        $filename = "template_{$modul}.xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    /**
     * Parse uploaded file and return validation preview.
     */
    public function uploadPreview(Request $request, ImportService $importService): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat mengunggah file.');

        $validated = $request->validate([
            'modul' => ['required', 'string', 'in:alat,lokasi,jadwal'],
            'file' => ['required', 'file', 'max:5120'], // Max 5MB
        ]);

        $modul = $validated['modul'];
        $file = $validated['file'];
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['xlsx', 'csv', 'xls'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak bisa dibaca sebagai Excel/CSV. Pastikan formatnya .xlsx, .xls, atau .csv.',
            ], 422);
        }

        try {
            $rawRows = $importService->readFileData($file->getRealPath());
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'File tidak bisa dibaca sebagai Excel/CSV. Pastikan formatnya .xlsx, .xls, atau .csv.',
            ], 422);
        }

        $result = $importService->parseAndValidate($modul, $rawRows);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 422);
        }

        // Store validated state temporarily for confirmation in Phase L4
        $token = Str::random(40);
        Cache::put("impex_preview_{$token}", [
            'modul' => $modul,
            'filename' => $file->getClientOriginalName(),
            'rows' => $result['rows'],
            'total' => $result['total'],
        ], now()->addHours(2));

        return response()->json([
            'success' => true,
            'token' => $token,
            'filename' => $file->getClientOriginalName(),
            'modul' => $modul,
            'rows' => $result['rows'],
            'total' => $result['total'],
            'previewCols' => $result['previewCols'],
            'cols' => $result['cols'],
        ]);
    }

    /**
     * Download Error Report (.xlsx) for invalid rows.
     */
    public function errorReport(Request $request, ImportService $importService): Response
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $token = $request->input('token');
        $cached = $token ? Cache::get("impex_preview_{$token}") : null;

        $modul = $cached['modul'] ?? $request->input('modul', 'alat');
        $rows = $cached['rows'] ?? $request->input('rows', []);

        if (is_string($rows)) {
            $rows = json_decode($rows, true) ?: [];
        }

        $content = $importService->generateErrorReport($modul, $rows);
        $today = now()->format('Y-m-d');
        $filename = "laporan_error_{$modul}_{$today}.xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    /**
     * Confirm batch import and persist records to the database.
     */
    public function confirm(Request $request, ImportService $importService): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat mengonfirmasi import.');

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'mode' => ['required', 'string', 'in:skip,update'],
        ]);

        $token = $validated['token'];
        $mode = $validated['mode'];

        $cached = Cache::get("impex_preview_{$token}");
        if (!$cached || empty($cached['rows'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi import telah kedaluwarsa atau tidak valid. Silakan unggah ulang file.',
            ], 422);
        }

        try {
            $result = $importService->executeImport($cached, $mode, auth()->id());
            Cache::forget("impex_preview_{$token}");

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'counts' => $result['counts'],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan data import. Seluruh perubahan telah dibatalkan (rollback).',
            ], 500);
        }
    }
}
