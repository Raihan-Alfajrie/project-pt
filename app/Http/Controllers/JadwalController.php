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
     * Timeline months from Jun 2026 to Dec 2027 (19 months).
     */
    protected array $months = [
        'Jun-26', 'Jul-26', 'Aug-26', 'Sep-26', 'Oct-26', 'Nov-26', 'Dec-26',
        'Jan-27', 'Feb-27', 'Mar-27', 'Apr-27', 'May-27', 'Jun-27', 'Jul-27',
        'Aug-27', 'Sep-27', 'Oct-27', 'Nov-27', 'Dec-27'
    ];

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

        $months = $this->months;
        $activeKode = $request->query('kode');

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
            'catatan' => $j->catatan ?? ''
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
            'jadwalsData'
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
            'bulan_index' => ['required', 'integer', 'min:0', 'max:18'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:19'],
            'lokasi_id' => ['nullable', 'exists:lokasi,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        // Convert month index (0 = Jun 2026) to Y-m-01 date
        $idx = intval($validated['bulan_index']);
        $year = 2026 + intdiv($idx + 5, 12);
        $month = (($idx + 5) % 12) + 1;
        $periodeMulai = sprintf('%04d-%02d-01', $year, $month);

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
            'bulan_index' => ['required', 'integer', 'min:0', 'max:18'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:19'],
            'lokasi_id' => ['nullable', 'exists:lokasi,id'],
            'catatan' => ['nullable', 'string'],
        ]);

        $idx = intval($validated['bulan_index']);
        $year = 2026 + intdiv($idx + 5, 12);
        $month = (($idx + 5) % 12) + 1;
        $periodeMulai = sprintf('%04d-%02d-01', $year, $month);

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
