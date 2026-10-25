<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DokumentasiItem;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    /**
     * Build dynamic timeline months array from Jun 2026 up to max schedule end date / current date.
     */
    protected function buildTimelineMonths($jadwals = null): array
    {
        $startDate = \Carbon\Carbon::create(2026, 6, 1);
        $minMonths = 19; // baseline 19 months: Jun-26 s/d Dec-27

        // Check current date
        $now = \Carbon\Carbon::now();
        $diffNow = ($now->year - $startDate->year) * 12 + ($now->month - $startDate->month) + 1;
        if ($diffNow > $minMonths) {
            $minMonths = $diffNow;
        }

        // Check max schedule date
        if ($jadwals) {
            foreach ($jadwals as $j) {
                if ($j->periode_mulai) {
                    $itemEnd = $j->periode_mulai->copy()->addMonths(max(1, $j->durasi_bulan) - 1);
                    $diffEnd = ($itemEnd->year - $startDate->year) * 12 + ($itemEnd->month - $startDate->month) + 1;
                    if ($diffEnd > $minMonths) {
                        $minMonths = $diffEnd;
                    }
                }
            }
        }

        // Round up to end of year (December)
        $lastDate = $startDate->copy()->addMonths($minMonths - 1);
        if ($lastDate->month < 12) {
            $minMonths += (12 - $lastDate->month);
        }

        $months = [];
        for ($i = 0; $i < $minMonths; $i++) {
            $months[] = $startDate->copy()->addMonths($i)->format('M-y');
        }

        return $months;
    }

    /**
     * Display the Jadwal Gantt view and status KPIs.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $canManage = $user ? ($user->isAdmin() || $user->hasModuleAccess('jadwal')) : false;

        $rigs = Alat::where('kategori', 'rig')
            ->with('lokasi')
            ->orderBy('id')
            ->get();

        $jadwals = Jadwal::with(['alat.lokasi', 'lokasi', 'kategori'])
            ->orderBy('periode_mulai')
            ->get();

        $countSelesai = $jadwals->where('status', 'selesai')->count();
        $countProgres = $jadwals->where('status', 'progres')->count();
        $countKendala = $jadwals->where('status', 'kendala')->count();

        // Kategori diambil dari dokumentasi_item
        $kategoris = DokumentasiItem::orderBy('urutan')->get();
        $lokasis = Lokasi::orderBy('kode')->get();

        $months = $this->buildTimelineMonths($jadwals);
        $activeKode = $request->query('kode');

        // Dynamic current month index
        $nowLabel = \Carbon\Carbon::now()->format('M-y');
        $nowColIndex = array_search($nowLabel, $months);
        if ($nowColIndex === false) {
            $nowColIndex = -1;
        }

        $rigsData = $rigs->map(fn($r) => [
            'id' => $r->id,
            'nama' => $r->nama,
            'lokasi_kode' => $r->lokasi?->kode ?? '',
            'lokasi_nama' => $r->lokasi?->nama_tempat ?? ''
        ]);

        $jadwalsData = $jadwals->map(fn($j) => [
            'id' => $j->id,
            'alat_id' => $j->alat_id,
            'rig' => $j->alat?->nama ?? '',
            'lokasi_id' => $j->lokasi_id ?? $j->alat?->lokasi_id,
            'lokasi_kode' => $j->lokasi?->kode ?? $j->alat?->lokasi?->kode ?? '',
            'lokasi_nama' => $j->lokasi?->nama_tempat ?? $j->alat?->lokasi?->nama_tempat ?? '',
            'label' => $j->label,
            'kategori_id' => $j->kategori_id,
            'kategori_nama' => $j->kategori?->label ?? 'Lain-lain',
            'kategori_warna' => $j->kategori?->warna ?? '#e6d9bd',
            'kategori_gaya' => $j->kategori?->gaya ?? 'solid',
            's' => $j->bulan_mulai_index,
            'n' => $j->durasi_bulan,
            'status' => $j->status,
            'catatan' => $j->catatan ?? '',
            'periode_mulai' => $j->periode_mulai?->format('Y-m-d') ?? '',
            'tahun' => $j->periode_mulai?->year ?? 2026,
            'bulan' => $j->periode_mulai?->month ?? 6,
        ]);

        return view('jadwal.index', compact(
            'rigs',
            'jadwals',
            'countSelesai',
            'countProgres',
            'countKendala',
            'kategoris',
            'lokasis',
            'months',
            'canManage',
            'activeKode',
            'rigsData',
            'jadwalsData',
            'nowColIndex'
        ));
    }

    /**
     * Store a newly created schedule entry in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasModuleAccess('jadwal'))) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'alat_id' => ['required', 'exists:alat,id'],
            'label' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:dokumentasi_item,id'],
            'status' => ['required', 'in:selesai,progres,kendala'],
            'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2020', 'max:2099'],
            'periode_mulai' => ['nullable', 'string'],
            'bulan_index' => ['nullable', 'integer', 'min:0'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:60'],
            'lokasi_id' => ['nullable', 'exists:lokasi,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        // Convert to Y-m-01
        if (!empty($validated['bulan']) && !empty($validated['tahun'])) {
            $periodeMulai = sprintf('%04d-%02d-01', intval($validated['tahun']), intval($validated['bulan']));
        } elseif (!empty($validated['periode_mulai'])) {
            $periodeMulai = \Carbon\Carbon::parse($validated['periode_mulai'])->format('Y-m-01');
        } elseif (isset($validated['bulan_index'])) {
            $idx = intval($validated['bulan_index']);
            $year = 2026 + intdiv($idx + 5, 12);
            $month = (($idx + 5) % 12) + 1;
            $periodeMulai = sprintf('%04d-%02d-01', $year, $month);
        } else {
            $periodeMulai = \Carbon\Carbon::now()->format('Y-m-01');
        }

        // If lokasi_id is not given, default to the rig's current location
        $lokasiId = $validated['lokasi_id'] ?? null;
        if (!$lokasiId) {
            $alat = Alat::find($validated['alat_id']);
            $lokasiId = $alat?->lokasi_id;
        }

        $jadwal = Jadwal::create([
            'alat_id' => $validated['alat_id'],
            'lokasi_id' => $lokasiId,
            'kategori_id' => $validated['kategori_id'],
            'label' => trim($validated['label']),
            'status' => $validated['status'],
            'periode_mulai' => $periodeMulai,
            'durasi_bulan' => $validated['durasi_bulan'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        AuditLogService::log('tambah', 'jadwal', "Menambahkan jadwal operasi: {$jadwal->label}");
        NotificationService::notifyJadwalCreated($jadwal);

        return redirect()->route('jadwal.index')->with('success', 'Item jadwal berhasil ditambahkan.');
    }

    /**
     * Update the specified schedule entry in database.
     */
    public function update(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasModuleAccess('jadwal'))) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'alat_id' => ['required', 'exists:alat,id'],
            'label' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'exists:dokumentasi_item,id'],
            'status' => ['required', 'in:selesai,progres,kendala'],
            'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2020', 'max:2099'],
            'periode_mulai' => ['nullable', 'string'],
            'bulan_index' => ['nullable', 'integer', 'min:0'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:60'],
            'lokasi_id' => ['nullable', 'exists:lokasi,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        if (!empty($validated['bulan']) && !empty($validated['tahun'])) {
            $periodeMulai = sprintf('%04d-%02d-01', intval($validated['tahun']), intval($validated['bulan']));
        } elseif (!empty($validated['periode_mulai'])) {
            $periodeMulai = \Carbon\Carbon::parse($validated['periode_mulai'])->format('Y-m-01');
        } elseif (isset($validated['bulan_index'])) {
            $idx = intval($validated['bulan_index']);
            $year = 2026 + intdiv($idx + 5, 12);
            $month = (($idx + 5) % 12) + 1;
            $periodeMulai = sprintf('%04d-%02d-01', $year, $month);
        } else {
            $periodeMulai = $jadwal->periode_mulai?->format('Y-m-01') ?? \Carbon\Carbon::now()->format('Y-m-01');
        }

        $lokasiId = $validated['lokasi_id'] ?? $jadwal->lokasi_id;
        if (!$lokasiId) {
            $alat = Alat::find($validated['alat_id']);
            $lokasiId = $alat?->lokasi_id;
        }

        $oldStatus = $jadwal->status;
        $jadwal->update([
            'alat_id' => $validated['alat_id'],
            'lokasi_id' => $lokasiId,
            'kategori_id' => $validated['kategori_id'],
            'label' => trim($validated['label']),
            'status' => $validated['status'],
            'periode_mulai' => $periodeMulai,
            'durasi_bulan' => $validated['durasi_bulan'],
            'catatan' => $validated['catatan'] ?? null,
        ]);
        $newStatus = $jadwal->status;

        AuditLogService::log('edit', 'jadwal', "Memperbarui jadwal operasi: {$jadwal->label}");
        if ($oldStatus !== $newStatus) {
            NotificationService::notifyJadwalStatusChanged($jadwal, $oldStatus, $newStatus);
        }

        return redirect()->route('jadwal.index')->with('success', 'Item jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified schedule entry from database.
     */
    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasModuleAccess('jadwal'))) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $label = $jadwal->label;
        $jadwal->delete();

        AuditLogService::log('hapus', 'jadwal', "Menghapus jadwal operasi: {$label}");

        return redirect()->route('jadwal.index')->with('success', "Item jadwal '{$label}' berhasil dihapus.");
    }
}
