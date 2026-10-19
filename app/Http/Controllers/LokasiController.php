<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiController extends Controller
{
    /**
     * Display a listing of locations and the interactive Leaflet map.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $canManage = $user ? ($user->isAdmin() || $user->hasModuleAccess('lokasi')) : false;

        $lokasis = Lokasi::with(['alats', 'jadwals'])
            ->orderBy('kode')
            ->get();

        $provinsiList = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
            'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung',
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
            'Banten', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
            'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        ];

        return view('lokasi.index', compact('lokasis', 'canManage', 'provinsiList'));
    }

    /**
     * Store a newly created location in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasModuleAccess('lokasi'))) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:lokasi,kode'],
            'nama_tempat' => ['required', 'string', 'max:150'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'tanggal_dipetakan' => ['nullable', 'date'],
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['tanggal_dipetakan'] = $validated['tanggal_dipetakan'] ?? now()->toDateString();

        $lokasi = Lokasi::create($validated);

        AuditLogService::log('tambah', 'lokasi', "Menambahkan lokasi: {$lokasi->nama_tempat} ({$lokasi->kode})");

        return redirect()->route('lokasi.index')->with('success', "Lokasi {$validated['kode']} berhasil ditambahkan.");
    }

    /**
     * Update the specified location in database.
     */
    public function update(Request $request, Lokasi $lokasi): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasModuleAccess('lokasi'))) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:lokasi,kode,' . $lokasi->id],
            'nama_tempat' => ['required', 'string', 'max:150'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));

        $lokasi->update($validated);

        AuditLogService::log('edit', 'lokasi', "Memperbarui data lokasi: {$lokasi->nama_tempat} ({$lokasi->kode})");

        return redirect()->route('lokasi.index')->with('success', "Lokasi {$lokasi->kode} berhasil diperbarui.");
    }

    /**
     * Remove the specified location from database.
     */
    public function destroy(Lokasi $lokasi): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasModuleAccess('lokasi'))) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $kode = $lokasi->kode;
        $namaTempat = $lokasi->nama_tempat;
        $lokasi->delete();

        AuditLogService::log('hapus', 'lokasi', "Menghapus data lokasi: {$namaTempat} ({$kode})");

        return redirect()->route('lokasi.index')->with('success', "Lokasi {$kode} berhasil dihapus.");
    }
}
