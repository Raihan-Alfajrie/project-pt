<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Lokasi;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->hasModuleAccess('alat');

        // KPI counts
        $totalAlat = Alat::count();
        $alatAktif = Alat::where('status', 'aktif')->count();
        $alatStandby = Alat::where('status', 'standby')->count();
        $alatBermasalah = Alat::where('status', 'bermasalah')->count();

        // Get all tools with assigned location
        $alats = Alat::with('lokasi')->orderBy('id')->get();
        $lokasis = Lokasi::orderBy('kode')->get();

        return view('alat.index', [
            'alats' => $alats,
            'lokasis' => $lokasis,
            'totalAlat' => $totalAlat,
            'alatAktif' => $alatAktif,
            'alatStandby' => $alatStandby,
            'alatBermasalah' => $alatBermasalah,
            'canManage' => $canManage,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasModuleAccess('alat')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambah data alat.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:rig,alat_pendukung'],
            'status' => ['required', 'in:aktif,standby,bermasalah'],
            'merek' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'tahun_perolehan' => ['nullable', 'integer', 'min:1980', 'max:2030'],
            'no_seri' => ['required', 'string', 'max:50', 'unique:alat,no_seri'],
            'keterangan' => ['nullable', 'string'],
            'lokasi_id' => ['nullable', 'exists:lokasi,id'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('alat_photos', 'public');
        }

        $alat = Alat::create($validated);

        AuditLogService::log('tambah', 'alat', "Menambahkan alat: {$alat->nama} ({$alat->no_seri})");

        return redirect()->route('alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alat $alat): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasModuleAccess('alat')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit data alat.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:rig,alat_pendukung'],
            'status' => ['required', 'in:aktif,standby,bermasalah'],
            'merek' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'tahun_perolehan' => ['nullable', 'integer', 'min:1980', 'max:2030'],
            'no_seri' => ['required', 'string', 'max:50', Rule::unique('alat', 'no_seri')->ignore($alat->id)],
            'keterangan' => ['nullable', 'string'],
            'lokasi_id' => ['nullable', 'exists:lokasi,id'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($alat->foto && Storage::disk('public')->exists($alat->foto)) {
                Storage::disk('public')->delete($alat->foto);
            }
            $validated['foto'] = $request->file('foto')->store('alat_photos', 'public');
        }

        $alat->update($validated);

        AuditLogService::log('edit', 'alat', "Memperbarui data alat: {$alat->nama}");

        return redirect()->route('alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alat $alat): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasModuleAccess('alat')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus data alat.');
        }

        $namaAlat = $alat->nama;

        if ($alat->foto && Storage::disk('public')->exists($alat->foto)) {
            Storage::disk('public')->delete($alat->foto);
        }

        $alat->delete();

        AuditLogService::log('hapus', 'alat', "Menghapus data alat: {$namaAlat}");

        return redirect()->route('alat.index')->with('success', 'Data alat berhasil dihapus.');
    }
}
