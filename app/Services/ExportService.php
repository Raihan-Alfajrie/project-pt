<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\AuditLog;
use App\Models\Jadwal;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    /**
     * Build base query for a module with dynamic filters applied.
     */
    public function getQuery(string $modul, array $filters = [])
    {
        return match ($modul) {
            'alat' => $this->getAlatQuery($filters),
            'lokasi' => $this->getLokasiQuery($filters),
            'jadwal' => $this->getJadwalQuery($filters),
            'audit', 'audit_log' => $this->getAuditQuery($filters),
            default => throw new \InvalidArgumentException("Modul {$modul} tidak didukung."),
        };
    }

    /**
     * Count rows matching the filter.
     */
    public function countRows(string $modul, array $filters = []): int
    {
        return $this->getQuery($modul, $filters)->count();
    }

    /**
     * Get formatted rows with headers ready for export.
     */
    public function getExportData(string $modul, array $filters = []): array
    {
        $headers = match ($modul) {
            'alat' => ['Nama', 'Kategori', 'Merek', 'Model / Tipe', 'Tahun Perolehan', 'No. Seri', 'Status', 'Keterangan'],
            'lokasi' => ['Kode Lapangan', 'Nama Tempat', 'Provinsi', 'Kota / Kabupaten', 'Kecamatan', 'Keterangan'],
            'jadwal' => ['Rig', 'Nama Sumur / Pekerjaan', 'Kategori', 'Status', 'Bulan Mulai', 'Durasi (bulan)', 'Catatan'],
            'audit', 'audit_log' => ['Username', 'Nama', 'Gmail', 'Jabatan', 'Tanggal / Waktu', 'Aksi', 'Modul', 'Keterangan'],
            default => throw new \InvalidArgumentException("Modul {$modul} tidak didukung."),
        };

        $records = $this->getQuery($modul, $filters)->get();
        $rows = [];

        foreach ($records as $record) {
            $row = match ($modul) {
                'alat' => [
                    $record->nama,
                    $record->kategori === 'rig' ? 'Rig' : 'Alat Pendukung',
                    $record->merek,
                    $record->model ?? '',
                    $record->tahun_perolehan ?? '',
                    $record->no_seri,
                    $record->status === 'aktif' ? 'Aktif' : ($record->status === 'standby' ? 'Tidak Aktif' : 'Bermasalah'),
                    $record->keterangan ?? '',
                ],
                'lokasi' => [
                    $record->kode,
                    $record->nama_tempat,
                    $record->provinsi,
                    $record->kota ?? '',
                    $record->kecamatan ?? '',
                    $record->keterangan ?? '',
                ],
                'jadwal' => [
                    $record->alat?->nama ?? '',
                    $record->label,
                    $record->kategori?->label ?? '',
                    $record->status === 'selesai' ? 'Selesai' : ($record->status === 'kendala' ? 'Kendala' : 'Menunggu / Progres'),
                    Carbon::parse($record->periode_mulai)->format('M-y'),
                    $record->durasi_bulan,
                    $record->catatan ?? '',
                ],
                'audit', 'audit_log' => [
                    $record->username_snapshot,
                    $record->user?->nama ?? $record->username_snapshot,
                    $record->email_snapshot,
                    ucfirst($record->jabatan_snapshot),
                    $record->waktu ? Carbon::parse($record->waktu)->translatedFormat('d M Y, H:i') : '—',
                    ucfirst($record->aksi),
                    ucfirst($record->modul),
                    $record->keterangan,
                ],
            };

            // Neutralize formula injection for each cell
            $rows[] = array_map([$this, 'sanitizeFormula'], $row);
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
            'title' => match ($modul) {
                'alat' => 'Alat',
                'lokasi' => 'Lokasi',
                'jadwal' => 'Jadwal',
                default => 'Audit Log',
            },
        ];
    }

    /**
     * Neutralize CSV / formula injection (=, +, -, @).
     */
    public function sanitizeFormula(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $trimmed = ltrim($value);
        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Generate XLSX file content as binary string.
     */
    public function generateXlsx(string $modul, array $filters = []): string
    {
        $data = $this->getExportData($modul, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($data['title']);

        // Set headers
        $sheet->fromArray([$data['headers']], null, 'A1');

        // Bold headers
        $highestCol = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestCol}1")->getFont()->setBold(true);

        // Set rows if available
        if (!empty($data['rows'])) {
            $sheet->fromArray($data['rows'], null, 'A2');
        }

        // Auto-size columns
        foreach (range('A', $highestCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'impex_xlsx_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        return $content;
    }

    /**
     * Generate CSV file content with UTF-8 BOM.
     */
    public function generateCsv(string $modul, array $filters = []): string
    {
        $data = $this->getExportData($modul, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($data['title']);

        $sheet->fromArray([$data['headers']], null, 'A1');
        if (!empty($data['rows'])) {
            $sheet->fromArray($data['rows'], null, 'A2');
        }

        $writer = new Csv($spreadsheet);
        $writer->setUseBOM(true);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");

        $tempFile = tempnam(sys_get_temp_dir(), 'impex_csv_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        return $content;
    }

    protected function getAlatQuery(array $filters)
    {
        $q = Alat::query()->orderBy('id');

        if (!empty($filters['kategori'])) {
            $kat = $filters['kategori'] === 'Rig' ? 'rig' : 'alat_pendukung';
            $q->where('kategori', $kat);
        }

        if (!empty($filters['status'])) {
            $st = match ($filters['status']) {
                'Aktif' => 'aktif',
                'Tidak Aktif' => 'standby',
                'Bermasalah' => 'bermasalah',
                default => null,
            };
            if ($st) {
                $q->where('status', $st);
            }
        }

        return $q;
    }

    protected function getLokasiQuery(array $filters)
    {
        $q = Lokasi::query()->orderBy('kode');

        if (!empty($filters['provinsi'])) {
            $q->where('provinsi', $filters['provinsi']);
        }

        return $q;
    }

    protected function getJadwalQuery(array $filters)
    {
        $q = Jadwal::with(['alat', 'kategori'])->orderBy('periode_mulai');

        if (!empty($filters['status'])) {
            $st = match ($filters['status']) {
                'Selesai' => 'selesai',
                'Kendala' => 'kendala',
                'Menunggu / Progres', 'Menunggu', 'Progres' => 'progres',
                default => null,
            };
            if ($st) {
                $q->where('status', $st);
            }
        }

        if (!empty($filters['kategori'])) {
            $q->whereHas('kategori', fn($query) => $query->where('label', $filters['kategori']));
        }

        return $q;
    }

    protected function getAuditQuery(array $filters)
    {
        $q = AuditLog::with('user')->orderBy('waktu', 'desc');

        if (!empty($filters['aksi'])) {
            $q->where('aksi', strtolower($filters['aksi']));
        }

        if (!empty($filters['modul'])) {
            $q->where('modul', strtolower($filters['modul']));
        }

        return $q;
    }
}
