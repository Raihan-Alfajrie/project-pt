<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\DokumentasiItem;
use App\Models\ImportExportLog;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportService
{
    /**
     * Timeline months from Jun-26 to Dec-27 (19 months).
     */
    public const MONTHS = [
        'Jun-26', 'Jul-26', 'Aug-26', 'Sep-26', 'Oct-26', 'Nov-26', 'Dec-26',
        'Jan-27', 'Feb-27', 'Mar-27', 'Apr-27', 'May-27', 'Jun-27', 'Jul-27',
        'Aug-27', 'Sep-27', 'Oct-27', 'Nov-27', 'Dec-27'
    ];

    public const MON_ABBR = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    public const MON_ALIAS = [
        'jan' => 0, 'feb' => 1, 'mar' => 2, 'apr' => 3, 'may' => 4, 'mei' => 4,
        'jun' => 5, 'jul' => 6, 'aug' => 7, 'agu' => 7, 'agt' => 7, 'sep' => 8,
        'oct' => 9, 'okt' => 9, 'nov' => 10, 'dec' => 11, 'des' => 11
    ];

    public const PROVINSI_LIST = [
        'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
        'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung',
        'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
        'Banten', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
        'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
        'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
        'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
    ];

    /**
     * Get module specification definitions (columns, aliases, required flags, hints).
     */
    public function getModuleSpecs(string $modul): array
    {
        return match ($modul) {
            'alat' => [
                'label' => 'Alat',
                'file' => 'alat',
                'keyName' => 'No. Seri',
                'cols' => [
                    ['k' => 'nama', 'h' => 'Nama', 'req' => true, 'al' => ['nama', 'namaalat'], 'hint' => 'Nama rig / alat'],
                    ['k' => 'kategori', 'h' => 'Kategori', 'req' => true, 'al' => ['kategori'], 'hint' => 'Rig atau Alat Pendukung'],
                    ['k' => 'merek', 'h' => 'Merek', 'req' => true, 'al' => ['merek'], 'hint' => 'Merek / pabrikan'],
                    ['k' => 'model', 'h' => 'Model / Tipe', 'req' => false, 'al' => ['modeltipe', 'model', 'tipe'], 'hint' => 'Model atau tipe (boleh kosong)'],
                    ['k' => 'tahun', 'h' => 'Tahun Perolehan', 'req' => false, 'al' => ['tahunperolehan', 'tahun'], 'hint' => 'Angka 1980–2026 (boleh kosong)'],
                    ['k' => 'noseri', 'h' => 'No. Seri', 'req' => true, 'al' => ['noseri', 'nomorseri'], 'hint' => 'Harus unik, dipakai sebagai kunci pencocokan'],
                    ['k' => 'status', 'h' => 'Status', 'req' => false, 'al' => ['status'], 'hint' => 'Aktif / Tidak Aktif / Bermasalah (kosong = Aktif)'],
                    ['k' => 'ket', 'h' => 'Keterangan', 'req' => false, 'al' => ['keterangan', 'ket'], 'hint' => 'Catatan bebas'],
                ],
                'preview' => ['nama', 'kategori', 'merek', 'noseri', 'status'],
            ],
            'lokasi' => [
                'label' => 'Lokasi',
                'file' => 'lokasi',
                'keyName' => 'Kode Lapangan',
                'cols' => [
                    ['k' => 'kode', 'h' => 'Kode Lapangan', 'req' => true, 'al' => ['kodelapangan', 'kode'], 'hint' => 'Unik, dipakai sebagai kunci pencocokan'],
                    ['k' => 'nama', 'h' => 'Nama Tempat', 'req' => true, 'al' => ['namatempat', 'nama'], 'hint' => 'Nama lapangan / tempat'],
                    ['k' => 'provinsi', 'h' => 'Provinsi', 'req' => true, 'al' => ['provinsi'], 'hint' => 'Nama provinsi resmi, mis. Sumatera Selatan'],
                    ['k' => 'kota', 'h' => 'Kota / Kabupaten', 'req' => false, 'al' => ['kotakabupaten', 'kota', 'kabupaten'], 'hint' => 'Boleh kosong'],
                    ['k' => 'kec', 'h' => 'Kecamatan', 'req' => false, 'al' => ['kecamatan'], 'hint' => 'Boleh kosong'],
                    ['k' => 'ket', 'h' => 'Keterangan', 'req' => false, 'al' => ['keterangan', 'ket'], 'hint' => 'Catatan bebas'],
                ],
                'preview' => ['kode', 'nama', 'provinsi', 'kota'],
            ],
            'jadwal' => [
                'label' => 'Jadwal',
                'file' => 'jadwal',
                'keyName' => 'Rig + Nama Sumur / Pekerjaan',
                'cols' => [
                    ['k' => 'rig', 'h' => 'Rig', 'req' => true, 'al' => ['rig', 'namarig'], 'hint' => 'Harus sama dengan nama rig di data Alat'],
                    ['k' => 'label', 'h' => 'Nama Sumur / Pekerjaan', 'req' => true, 'al' => ['namasumurpekerjaan', 'namasumur', 'sumur', 'pekerjaan', 'label'], 'hint' => 'Dipakai bersama Rig sebagai kunci pencocokan'],
                    ['k' => 'kat', 'h' => 'Kategori', 'req' => true, 'al' => ['kategori'], 'hint' => 'Salah satu fase kerja di menu Dokumentasi'],
                    ['k' => 'status', 'h' => 'Status', 'req' => false, 'al' => ['status'], 'hint' => 'Selesai / Menunggu / Progres / Kendala (kosong = Menunggu / Progres)'],
                    ['k' => 'bulan', 'h' => 'Bulan Mulai', 'req' => true, 'al' => ['bulanmulai', 'bulan', 'mulai'], 'hint' => 'Format 2026-06 (disarankan) atau Jun-26; rentang Jun-26 s/d Des-27'],
                    ['k' => 'durasi', 'h' => 'Durasi (bulan)', 'req' => false, 'al' => ['durasibulan', 'durasi'], 'hint' => 'Angka bulat 1–19 (kosong = 1)'],
                    ['k' => 'catatan', 'h' => 'Catatan', 'req' => false, 'al' => ['catatan', 'keterangan'], 'hint' => 'Catatan bebas'],
                ],
                'preview' => ['rig', 'label', 'kat', 'bulan', 'durasi', 'status'],
            ],
            default => throw new \InvalidArgumentException("Modul {$modul} tidak valid untuk import."),
        };
    }

    /**
     * Generate template XLSX file with 2 sheets: Template & Petunjuk.
     */
    public function generateTemplate(string $modul): string
    {
        $spec = $this->getModuleSpecs($modul);
        $spreadsheet = new Spreadsheet();

        // 1. Sheet Template (headers only)
        $sheetTemplate = $spreadsheet->getActiveSheet();
        $sheetTemplate->setTitle('Template');

        $headers = array_map(fn($c) => $c['h'], $spec['cols']);
        $sheetTemplate->fromArray([$headers], null, 'A1');

        $highestCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheetTemplate->getStyle("A1:{$highestCol}1")->getFont()->setBold(true);

        foreach (range(1, count($headers)) as $colIndex) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheetTemplate->getColumnDimension($colLetter)->setWidth(max(15, strlen($headers[$colIndex - 1]) + 5));
        }

        // 2. Sheet Petunjuk
        $sheetPetunjuk = $spreadsheet->createSheet();
        $sheetPetunjuk->setTitle('Petunjuk');

        $petunjukHeaders = ['Kolom', 'Wajib', 'Keterangan'];
        $petunjukRows = [];
        foreach ($spec['cols'] as $c) {
            $petunjukRows[] = [
                $c['h'],
                $c['req'] ? 'Ya' : 'Tidak',
                $c['hint'],
            ];
        }

        $sheetPetunjuk->fromArray([$petunjukHeaders], null, 'A1');
        $sheetPetunjuk->fromArray($petunjukRows, null, 'A2');

        $sheetPetunjuk->getStyle('A1:C1')->getFont()->setBold(true);
        $sheetPetunjuk->getColumnDimension('A')->setWidth(26);
        $sheetPetunjuk->getColumnDimension('B')->setWidth(10);
        $sheetPetunjuk->getColumnDimension('C')->setWidth(65);

        // Set active sheet back to Template
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'tmpl_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        return $content;
    }

    /**
     * Normalize string for column/value matching: lowercase and alphanumeric only.
     */
    public static function norm(?string $s): string
    {
        if ($s === null) {
            return '';
        }
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $s));
    }

    /**
     * Parse uploaded file into raw data array.
     */
    public function readFileData(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = [];

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = [];
            foreach ($cellIterator as $cell) {
                // Read calculated value safely (do not evaluate formulas or macros)
                $val = null;
                try {
                    if (ExcelDate::isDateTime($cell)) {
                        $val = ExcelDate::excelToDateTimeObject($cell->getValue())->format('Y-m');
                    } else {
                        $val = $cell->getCalculatedValue();
                    }
                } catch (\Throwable) {
                    $val = $cell->getValue();
                }
                $rowData[] = $val !== null ? trim((string) $val) : '';
            }

            // Check if entire row is empty
            if (array_filter($rowData, fn($v) => $v !== '') !== []) {
                $rows[] = $rowData;
            }
        }

        $spreadsheet->disconnectWorksheets();
        return $rows;
    }

    /**
     * Parse rows and produce validation preview.
     */
    public function parseAndValidate(string $modul, array $rawRows): array
    {
        $spec = $this->getModuleSpecs($modul);

        if (count($rawRows) < 1) {
            return [
                'error' => 'File kosong atau tidak ada baris data di bawah baris judul kolom.',
            ];
        }

        // Header row
        $headerRow = $rawRows[0];
        $headerMap = [];
        $missing = [];

        foreach ($spec['cols'] as $col) {
            $matchedHeaderIndex = null;
            foreach ($headerRow as $idx => $headerText) {
                $normHeader = self::norm($headerText);
                if (in_array($normHeader, $col['al'], true)) {
                    $matchedHeaderIndex = $idx;
                    break;
                }
            }

            if ($matchedHeaderIndex !== null) {
                $headerMap[$col['k']] = $matchedHeaderIndex;
            } elseif ($col['req']) {
                $missing[] = $col['h'];
            }
        }

        if (!empty($missing)) {
            $missingList = implode(', ', $missing);
            return [
                'error' => "Kolom wajib tidak ditemukan: <b>{$missingList}</b>. Unduh template di Langkah 1 untuk melihat nama kolom yang benar.",
            ];
        }

        $dataRows = array_slice($rawRows, 1);
        if (empty($dataRows)) {
            return [
                'error' => 'File kosong atau tidak ada baris data di bawah baris judul kolom.',
            ];
        }

        if (count($dataRows) > 1000) {
            return [
                'error' => 'Jumlah data melebihi batas maksimum (maksimal 1.000 baris per file).',
            ];
        }

        // Load database reference data for validation and existing check
        $dbData = $this->loadDbReferences($modul);

        $parsedRows = [];
        $seenFileKeys = [];

        foreach ($dataRows as $i => $rowCells) {
            $excelRowNo = $i + 2;
            $rec = [];
            $rawObj = [];
            $errs = [];

            // Extract values by column mapping
            foreach ($spec['cols'] as $col) {
                $val = '';
                if (isset($headerMap[$col['k']]) && isset($rowCells[$headerMap[$col['k']]])) {
                    $val = trim((string) $rowCells[$headerMap[$col['k']]]);
                }
                $rec[$col['k']] = $val;
                $rawObj[$col['h']] = $val;
            }

            // Check required fields
            foreach ($spec['cols'] as $col) {
                if ($col['req'] && $rec[$col['k']] === '') {
                    $errs[] = "{$col['h']} wajib diisi";
                }
            }

            // Clean & domain validation
            $this->validateDomain($modul, $rec, $errs, $dbData);

            // Duplicate detection in the same file
            $key = null;
            if (empty($errs)) {
                $key = $this->getRecordKey($modul, $rec);
                if (isset($seenFileKeys[$key])) {
                    $errs[] = "Duplikat di dalam file (sama dengan baris {$seenFileKeys[$key]})";
                } else {
                    $seenFileKeys[$key] = $excelRowNo;
                }
            }

            // Check if exists in database
            $existsInDb = false;
            $dbId = null;
            if ($key !== null && isset($dbData['existingMap'][$key])) {
                $existsInDb = true;
                $dbId = $dbData['existingMap'][$key];
            }

            $parsedRows[] = [
                'rowNo' => $excelRowNo,
                'rec' => $rec,
                'raw' => $rawObj,
                'errs' => $errs,
                'existsInDb' => $existsInDb,
                'dbId' => $dbId,
                'key' => $key,
            ];
        }

        return [
            'success' => true,
            'modul' => $modul,
            'rows' => $parsedRows,
            'total' => count($parsedRows),
            'previewCols' => $spec['preview'],
            'cols' => $spec['cols'],
        ];
    }

    /**
     * Compute classification counts based on import mode (skip vs update).
     */
    public function classifySummary(array $rows, string $mode = 'skip'): array
    {
        $cnt = ['total' => count($rows), 'new' => 0, 'update' => 0, 'skip' => 0, 'error' => 0];

        foreach ($rows as $r) {
            if (!empty($r['errs'])) {
                $cnt['error']++;
            } elseif ($r['existsInDb']) {
                if ($mode === 'update') {
                    $cnt['update']++;
                } else {
                    $cnt['skip']++;
                }
            } else {
                $cnt['new']++;
            }
        }

        return $cnt;
    }

    /**
     * Generate Error Report Excel (.xlsx) file.
     */
    public function generateErrorReport(string $modul, array $rows): string
    {
        $spec = $this->getModuleSpecs($modul);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Error');

        $headers = array_merge(['Baris Excel', 'Pesan Error'], array_map(fn($c) => $c['h'], $spec['cols']));
        $sheet->fromArray([$headers], null, 'A1');

        $errorRows = [];
        foreach ($rows as $r) {
            if (!empty($r['errs'])) {
                $rowVals = [
                    $r['rowNo'],
                    implode('; ', $r['errs']),
                ];
                foreach ($spec['cols'] as $c) {
                    $rowVals[] = $r['raw'][$c['h']] ?? '';
                }
                $errorRows[] = $rowVals;
            }
        }

        if (!empty($errorRows)) {
            $sheet->fromArray($errorRows, null, 'A2');
        }

        $highestCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$highestCol}1")->getFont()->setBold(true);

        foreach (range(1, count($headers)) as $colIndex) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'err_report_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        return $content;
    }

    protected function getRecordKey(string $modul, array $rec): string
    {
        return match ($modul) {
            'alat' => strtolower(trim((string) $rec['noseri'])),
            'lokasi' => strtoupper(trim((string) $rec['kode'])),
            'jadwal' => strtolower(trim((string) $rec['rig']) . '|' . trim((string) $rec['label'])),
            default => '',
        };
    }

    protected function loadDbReferences(string $modul): array
    {
        $refs = ['existingMap' => []];

        if ($modul === 'alat') {
            $alats = Alat::all(['id', 'no_seri']);
            foreach ($alats as $a) {
                $refs['existingMap'][strtolower(trim($a->no_seri))] = $a->id;
            }
        } elseif ($modul === 'lokasi') {
            $lokasis = Lokasi::all(['id', 'kode']);
            foreach ($lokasis as $l) {
                $refs['existingMap'][strtoupper(trim($l->kode))] = $l->id;
            }
        } elseif ($modul === 'jadwal') {
            // Load all rig names
            $refs['rigNames'] = Alat::where('kategori', 'rig')->pluck('nama')->toArray();
            $refs['rigMap'] = [];
            foreach ($refs['rigNames'] as $nama) {
                $refs['rigMap'][self::norm($nama)] = $nama;
            }

            // Load valid categories from dokumentasi_item
            $kats = DokumentasiItem::pluck('label')->toArray();
            if (empty($kats)) {
                $kats = [
                    'Sudah Tajak', 'MIRU / RFD', 'Konstruksi', 'Lahan Bebas',
                    'Pembayaran / Pengajuan', 'Finalisasi KJPP', 'ARS / Survei Sosialisasi',
                    'Proses Pengadaan Rig', 'Rig Acceptance', 'Lain-lain',
                    'Eksplorasi / Kompleks', 'Kontrak Habis'
                ];
            }
            $refs['katMap'] = [];
            foreach ($kats as $k) {
                $refs['katMap'][self::norm($k)] = $k;
            }

            // Existing Jadwals
            $jadwals = Jadwal::with('alat')->get();
            foreach ($jadwals as $j) {
                $rName = $j->alat?->nama ?? '';
                $k = strtolower(trim($rName) . '|' . trim($j->label));
                $refs['existingMap'][$k] = $j->id;
            }
        }

        return $refs;
    }

    protected function validateDomain(string $modul, array &$rec, array &$errs, array $dbData): void
    {
        if ($modul === 'alat') {
            // Kategori
            if ($rec['kategori'] !== '') {
                $kNorm = self::norm($rec['kategori']);
                if ($kNorm === 'rig') {
                    $rec['kategori'] = 'Rig';
                } elseif ($kNorm === 'alatpendukung' || $kNorm === 'pendukung') {
                    $rec['kategori'] = 'Alat Pendukung';
                } else {
                    $errs[] = 'Kategori harus "Rig" atau "Alat Pendukung"';
                }
            }

            // Status
            $stNorm = self::norm($rec['status']);
            if ($stNorm === '' || $stNorm === 'aktif') {
                $rec['status'] = 'Aktif';
            } elseif ($stNorm === 'tidakaktif' || $stNorm === 'standby') {
                $rec['status'] = 'Tidak Aktif';
            } elseif ($stNorm === 'bermasalah') {
                $rec['status'] = 'Bermasalah';
            } else {
                $errs[] = 'Status harus Aktif / Tidak Aktif / Bermasalah';
            }

            // Tahun
            if ($rec['tahun'] !== '') {
                $th = (int) $rec['tahun'];
                if (!is_numeric($rec['tahun']) || $th < 1980 || $th > 2026) {
                    $errs[] = 'Tahun perolehan harus angka 1980–2026';
                } else {
                    $rec['tahun'] = $th;
                }
            }
        } elseif ($modul === 'lokasi') {
            $rec['kode'] = strtoupper(trim($rec['kode']));

            // Provinsi
            if ($rec['provinsi'] !== '') {
                $provNorm = self::norm($rec['provinsi']);
                $matchedProv = null;
                foreach (self::PROVINSI_LIST as $p) {
                    if (self::norm($p) === $provNorm) {
                        $matchedProv = $p;
                        break;
                    }
                }
                if ($matchedProv) {
                    $rec['provinsi'] = $matchedProv;
                } else {
                    $errs[] = 'Provinsi tidak dikenali: ' . $rec['provinsi'];
                }
            }
        } elseif ($modul === 'jadwal') {
            // Rig
            if ($rec['rig'] !== '') {
                $rNorm = self::norm($rec['rig']);
                if (isset($dbData['rigMap'][$rNorm])) {
                    $rec['rig'] = $dbData['rigMap'][$rNorm];
                } else {
                    $errs[] = 'Rig tidak ditemukan di data Alat';
                }
            }

            // Kategori
            if ($rec['kat'] !== '') {
                $kNorm = self::norm($rec['kat']);
                if (isset($dbData['katMap'][$kNorm])) {
                    $rec['kat'] = $dbData['katMap'][$kNorm];
                } else {
                    $errs[] = 'Kategori tidak dikenal: ' . $rec['kat'];
                }
            }

            // Status
            $stNorm = self::norm($rec['status']);
            if ($stNorm === '' || $stNorm === 'menunggu' || $stNorm === 'progres' || $stNorm === 'menungguprogres') {
                $rec['status'] = 'Menunggu / Progres';
            } elseif ($stNorm === 'selesai') {
                $rec['status'] = 'Selesai';
            } elseif ($stNorm === 'kendala') {
                $rec['status'] = 'Kendala';
            } else {
                $errs[] = 'Status harus Selesai / Menunggu / Progres / Kendala';
            }

            // Bulan Mulai
            $bulanParsed = $this->parseBulan($rec['bulan']);
            if (!$bulanParsed) {
                $errs[] = 'Format Bulan Mulai tidak dikenali (pakai 2026-06 atau Jun-26)';
            } elseif (!in_array($bulanParsed, self::MONTHS, true)) {
                $errs[] = 'Bulan Mulai di luar rentang Jun-26 s/d Des-27';
            } else {
                $rec['bulan'] = $bulanParsed;
            }

            // Durasi
            $durasiVal = $rec['durasi'] !== '' ? (int) $rec['durasi'] : 1;
            if ($rec['durasi'] !== '' && (!is_numeric($rec['durasi']) || $durasiVal < 1 || $durasiVal > 19)) {
                $errs[] = 'Durasi harus bilangan bulat 1–19';
            } else {
                $rec['durasi'] = $durasiVal;
            }

            // Check if start month + duration exceeds Des-27
            if ($bulanParsed && in_array($bulanParsed, self::MONTHS, true) && $durasiVal >= 1 && $durasiVal <= 19) {
                $monthIdx = array_search($bulanParsed, self::MONTHS, true);
                if ($monthIdx + $durasiVal > count(self::MONTHS)) {
                    $errs[] = 'Durasi melewati Des-27';
                }
            }
        }
    }

    /**
     * Parse date/month input into Jun-26 standard format.
     */
    public function parseBulan(mixed $v): ?string
    {
        if (empty($v)) {
            return null;
        }

        $s = trim((string) $v);

        // Pattern: 2026-06 or 2026/06
        if (preg_match('/^(\d{4})[-\/](\d{1,2})$/', $s, $m)) {
            $monthIndex = (int) $m[2] - 1;
            if ($monthIndex < 0 || $monthIndex > 11) {
                return null;
            }
            return self::MON_ABBR[$monthIndex] . '-' . substr($m[1], 2);
        }

        // Pattern: Jun-26 or Juni-2026 or Jun 26
        if (preg_match('/^([A-Za-z]{3,})[-\s\/\'](\d{2}|\d{4})$/', $s, $m)) {
            $aliasKey = strtolower(substr($m[1], 0, 3));
            if (!isset(self::MON_ALIAS[$aliasKey])) {
                return null;
            }
            $monthIndex = self::MON_ALIAS[$aliasKey];
            $yearShort = substr($m[2], -2);
            return self::MON_ABBR[$monthIndex] . '-' . $yearShort;
        }

        // Try date parse with Carbon
        try {
            $c = Carbon::parse($s);
            return $c->format('M-y');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Convert standard month representation (Jun-26, 2026-06) to Y-m-01 date string.
     */
    public function bulanToDate(?string $bulan): string
    {
        if (empty($bulan)) {
            return '2026-06-01';
        }

        // 2026-06 or 2026/06
        if (preg_match('/^(\d{4})[-\/](\d{1,2})/', $bulan, $m)) {
            return sprintf('%04d-%02d-01', (int) $m[1], (int) $m[2]);
        }

        // Jun-26 or Jun-2026
        if (preg_match('/^([A-Za-z]{3,})-(\d{2}|\d{4})$/', $bulan, $m)) {
            $abbr = ucfirst(strtolower(substr($m[1], 0, 3)));
            $monthIndex = array_search($abbr, self::MON_ABBR, true);
            $monthNum = ($monthIndex !== false) ? $monthIndex + 1 : 6;
            $year = strlen($m[2]) === 2 ? 2000 + (int) $m[2] : (int) $m[2];
            return sprintf('%04d-%02d-01', $year, $monthNum);
        }

        return '2026-06-01';
    }

    /**
     * Execute full atomic import for a verified preview batch.
     */
    public function executeImport(array $cachedData, string $mode, int $userId): array
    {
        $modul = $cachedData['modul'];
        $rows = $cachedData['rows'];
        $filename = $cachedData['filename'] ?? "import_{$modul}.xlsx";
        $total = count($rows);

        $counts = [
            'total' => $total,
            'baru' => 0,
            'diperbarui' => 0,
            'dilewati' => 0,
            'error' => 0,
        ];

        try {
            DB::beginTransaction();

            if ($modul === 'alat') {
                $this->executeImportAlat($rows, $mode, $counts);
            } elseif ($modul === 'lokasi') {
                $this->executeImportLokasi($rows, $mode, $counts);
            } elseif ($modul === 'jadwal') {
                $this->executeImportJadwal($rows, $mode, $counts);
            }

            DB::commit();

            $successCount = $counts['baru'] + $counts['diperbarui'];

            // Determine status for ImportExportLog
            if ($counts['error'] > 0 && $successCount > 0) {
                $logStatus = 'sebagian';
            } elseif ($counts['error'] > 0 && $successCount === 0) {
                $logStatus = 'gagal';
            } else {
                $logStatus = 'sukses';
            }

            // Create ImportExportLog
            ImportExportLog::create([
                'user_id' => $userId,
                'jenis' => 'import',
                'modul' => $modul,
                'nama_file' => $filename,
                'baris_berhasil' => $successCount,
                'baris_total' => $total,
                'status' => $logStatus,
            ]);

            // Create AuditLog
            AuditLogService::log(
                'tambah',
                'sistem',
                "Mengimpor {$successCount} baris data " . ucfirst($modul) . " dari file {$filename}"
            );

            // Build success message according to DESAIN.md §5.5
            if ($counts['error'] > 0) {
                $message = "Import selesai: {$counts['baru']} ditambahkan, {$counts['diperbarui']} diperbarui, {$counts['error']} baris error dilewati.";
            } else {
                $message = "Import selesai: {$counts['baru']} ditambahkan, {$counts['diperbarui']} diperbarui.";
            }

            return [
                'success' => true,
                'message' => $message,
                'counts' => $counts,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            // Record failed log on fatal system rollback
            ImportExportLog::create([
                'user_id' => $userId,
                'jenis' => 'import',
                'modul' => $modul,
                'nama_file' => $filename,
                'baris_berhasil' => 0,
                'baris_total' => $total,
                'status' => 'gagal',
            ]);

            throw $e;
        }
    }

    protected function executeImportAlat(array $rows, string $mode, array &$counts): void
    {
        foreach ($rows as $r) {
            if (!empty($r['errs'])) {
                $counts['error']++;
                continue;
            }

            $rec = $r['rec'];
            $kNorm = self::norm($rec['kategori']);
            $kategoriDb = ($kNorm === 'alatpendukung' || $kNorm === 'pendukung')
                ? 'alat_pendukung'
                : 'rig';

            $stNorm = self::norm($rec['status']);
            $statusDb = match ($stNorm) {
                'tidakaktif', 'standby' => 'standby',
                'bermasalah' => 'bermasalah',
                default => 'aktif',
            };

            $model = $rec['model'] !== '' ? $rec['model'] : null;
            $tahun = $rec['tahun'] !== '' ? (int) $rec['tahun'] : null;
            $ket = $rec['ket'] !== '' ? $rec['ket'] : null;
            $noSeri = trim((string) $rec['noseri']);
            $nama = trim((string) $rec['nama']);
            $merek = trim((string) $rec['merek']);

            $exists = $r['existsInDb'] ?? false;
            $dbId = $r['dbId'] ?? null;

            if ($exists) {
                if ($mode === 'update') {
                    $alat = $dbId ? Alat::find($dbId) : Alat::whereRaw('LOWER(no_seri) = ?', [strtolower($noSeri)])->first();
                    if ($alat) {
                        $alat->nama = $nama;
                        $alat->kategori = $kategoriDb;
                        $alat->merek = $merek;
                        $alat->model = $model;
                        $alat->tahun_perolehan = $tahun;
                        $alat->status = $statusDb;
                        $alat->keterangan = $ket;
                        $alat->save();
                        $counts['diperbarui']++;
                    } else {
                        Alat::create([
                            'nama' => $nama,
                            'kategori' => $kategoriDb,
                            'merek' => $merek,
                            'model' => $model,
                            'tahun_perolehan' => $tahun,
                            'no_seri' => $noSeri,
                            'status' => $statusDb,
                            'keterangan' => $ket,
                        ]);
                        $counts['baru']++;
                    }
                } else {
                    $counts['dilewati']++;
                }
            } else {
                Alat::create([
                    'nama' => $nama,
                    'kategori' => $kategoriDb,
                    'merek' => $merek,
                    'model' => $model,
                    'tahun_perolehan' => $tahun,
                    'no_seri' => $noSeri,
                    'status' => $statusDb,
                    'keterangan' => $ket,
                ]);
                $counts['baru']++;
            }
        }
    }

    protected function executeImportLokasi(array $rows, string $mode, array &$counts): void
    {
        foreach ($rows as $r) {
            if (!empty($r['errs'])) {
                $counts['error']++;
                continue;
            }

            $rec = $r['rec'];
            $kode = strtoupper(trim((string) $rec['kode']));
            $namaTempat = trim((string) $rec['nama']);
            $provinsi = trim((string) $rec['provinsi']);
            $kota = $rec['kota'] !== '' ? trim((string) $rec['kota']) : null;
            $kec = $rec['kec'] !== '' ? trim((string) $rec['kec']) : null;
            $ket = $rec['ket'] !== '' ? trim((string) $rec['ket']) : null;

            $exists = $r['existsInDb'] ?? false;
            $dbId = $r['dbId'] ?? null;

            if ($exists) {
                if ($mode === 'update') {
                    $lokasi = $dbId ? Lokasi::find($dbId) : Lokasi::where('kode', $kode)->first();
                    if ($lokasi) {
                        $lokasi->nama_tempat = $namaTempat;
                        $lokasi->provinsi = $provinsi;
                        $lokasi->kota = $kota;
                        $lokasi->kecamatan = $kec;
                        $lokasi->keterangan = $ket;
                        $lokasi->save();
                        $counts['diperbarui']++;
                    } else {
                        Lokasi::create([
                            'kode' => $kode,
                            'nama_tempat' => $namaTempat,
                            'provinsi' => $provinsi,
                            'kota' => $kota,
                            'kecamatan' => $kec,
                            'keterangan' => $ket,
                            'tanggal_dipetakan' => now()->toDateString(),
                        ]);
                        $counts['baru']++;
                    }
                } else {
                    $counts['dilewati']++;
                }
            } else {
                Lokasi::create([
                    'kode' => $kode,
                    'nama_tempat' => $namaTempat,
                    'provinsi' => $provinsi,
                    'kota' => $kota,
                    'kecamatan' => $kec,
                    'keterangan' => $ket,
                    'tanggal_dipetakan' => now()->toDateString(),
                ]);
                $counts['baru']++;
            }
        }
    }

    protected function executeImportJadwal(array $rows, string $mode, array &$counts): void
    {
        $rigs = Alat::where('kategori', 'rig')->get()->keyBy(fn($a) => self::norm($a->nama));
        $kategoris = DokumentasiItem::all()->keyBy(fn($d) => self::norm($d->label));

        foreach ($rows as $r) {
            if (!empty($r['errs'])) {
                $counts['error']++;
                continue;
            }

            $rec = $r['rec'];
            $rigObj = $rigs->get(self::norm($rec['rig']));
            if (!$rigObj) {
                $counts['error']++;
                continue;
            }

            $alatId = $rigObj->id;
            $lokasiId = $rigObj->lokasi_id;
            $label = trim((string) $rec['label']);

            // Kategori ID
            $katObj = $kategoris->get(self::norm($rec['kat']));
            $kategoriId = $katObj?->id;

            // Status DB enum: 'selesai', 'progres', 'kendala'
            $statusNorm = self::norm($rec['status']);
            $statusDb = match ($statusNorm) {
                'selesai' => 'selesai',
                'kendala' => 'kendala',
                default => 'progres',
            };

            $periodeMulai = $this->bulanToDate($rec['bulan']);
            $durasi = !empty($rec['durasi']) ? (int) $rec['durasi'] : 1;
            $catatan = $rec['catatan'] !== '' ? trim((string) $rec['catatan']) : null;

            $exists = $r['existsInDb'] ?? false;
            $dbId = $r['dbId'] ?? null;

            if ($exists) {
                if ($mode === 'update') {
                    $jadwal = $dbId ? Jadwal::find($dbId) : Jadwal::where('alat_id', $alatId)->where('label', $label)->first();
                    if ($jadwal) {
                        $jadwal->kategori_id = $kategoriId;
                        $jadwal->status = $statusDb;
                        $jadwal->periode_mulai = $periodeMulai;
                        $jadwal->durasi_bulan = $durasi;
                        $jadwal->catatan = $catatan;
                        $jadwal->save();
                        $counts['diperbarui']++;
                    } else {
                        Jadwal::create([
                            'alat_id' => $alatId,
                            'lokasi_id' => $lokasiId,
                            'kategori_id' => $kategoriId,
                            'label' => $label,
                            'status' => $statusDb,
                            'periode_mulai' => $periodeMulai,
                            'durasi_bulan' => $durasi,
                            'catatan' => $catatan,
                        ]);
                        $counts['baru']++;
                    }
                } else {
                    $counts['dilewati']++;
                }
            } else {
                Jadwal::create([
                    'alat_id' => $alatId,
                    'lokasi_id' => $lokasiId,
                    'kategori_id' => $kategoriId,
                    'label' => $label,
                    'status' => $statusDb,
                    'periode_mulai' => $periodeMulai,
                    'durasi_bulan' => $durasi,
                    'catatan' => $catatan,
                ]);
                $counts['baru']++;
            }
        }
    }
}
