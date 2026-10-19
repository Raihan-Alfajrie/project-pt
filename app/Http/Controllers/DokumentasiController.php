<?php

namespace App\Http\Controllers;

use App\Models\DokumentasiItem;
use App\Models\DokumentasiPanel;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DokumentasiController extends Controller
{
    /**
     * Display the Dokumentasi & Legend page.
     */
    public function index(Request $request): View
    {
        $panels = DokumentasiPanel::with(['items' => fn($q) => $q->orderBy('urutan')->orderBy('id')])
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $canManage = auth()->user()->isAdmin();

        $panelsData = $panels->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->judul,
            'desc' => $p->deskripsi ?? '',
            'items' => $p->items->map(fn($i) => [
                'id' => $i->id,
                'label' => $i->label,
                'style' => $i->gaya,
                'color' => $i->warna,
            ])->values(),
        ]);

        return view('dokumentasi.index', compact('panels', 'panelsData', 'canManage'));
    }

    /**
     * Store a newly created panel.
     */
    public function storePanel(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang dapat menambah panel.');

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
        ]);

        $maxUrutan = DokumentasiPanel::max('urutan') ?? -1;

        $panel = DokumentasiPanel::create([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'urutan' => $maxUrutan + 1,
        ]);

        AuditLogService::log('tambah', 'sistem', "Menambahkan panel legend: {$panel->judul}");

        return redirect()->route('dokumentasi.index')->with('success', 'Panel legend berhasil ditambahkan.');
    }

    /**
     * Update the specified panel.
     */
    public function updatePanel(Request $request, DokumentasiPanel $panel): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang dapat mengubah panel.');

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
        ]);

        $panel->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        AuditLogService::log('edit', 'sistem', "Memperbarui panel legend: {$panel->judul}");

        return redirect()->route('dokumentasi.index')->with('success', 'Panel legend berhasil diperbarui.');
    }

    /**
     * Remove the specified panel.
     */
    public function destroyPanel(DokumentasiPanel $panel): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang dapat menghapus panel.');

        $judul = $panel->judul;
        $panel->delete();

        AuditLogService::log('hapus', 'sistem', "Menghapus panel legend: {$judul}");

        return redirect()->route('dokumentasi.index')->with('success', 'Panel legend berhasil dihapus.');
    }

    /**
     * Store a newly created item in a panel.
     */
    public function storeItem(Request $request, DokumentasiPanel $panel): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang dapat menambah item legend.');

        $validated = $request->validate([
            'label' => 'required|string|max:150',
            'gaya' => 'required|in:solid,dashed,border',
            'warna' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $maxUrutan = $panel->items()->max('urutan') ?? -1;

        $item = $panel->items()->create([
            'label' => $validated['label'],
            'gaya' => $validated['gaya'],
            'warna' => strtolower($validated['warna']),
            'urutan' => $maxUrutan + 1,
        ]);

        AuditLogService::log('tambah', 'sistem', "Menambahkan item legend: {$item->label} pada panel {$panel->judul}");

        return redirect()->route('dokumentasi.index')->with('success', 'Item legend berhasil ditambahkan.');
    }

    /**
     * Update the specified item.
     */
    public function updateItem(Request $request, DokumentasiItem $item): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang dapat mengubah item legend.');

        $validated = $request->validate([
            'label' => 'required|string|max:150',
            'gaya' => 'required|in:solid,dashed,border',
            'warna' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $item->update([
            'label' => $validated['label'],
            'gaya' => $validated['gaya'],
            'warna' => strtolower($validated['warna']),
        ]);

        AuditLogService::log('edit', 'sistem', "Memperbarui item legend: {$item->label}");

        return redirect()->route('dokumentasi.index')->with('success', 'Item legend berhasil diperbarui.');
    }

    /**
     * Remove the specified item.
     */
    public function destroyItem(DokumentasiItem $item): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Administrator yang dapat menghapus item legend.');

        $label = $item->label;
        $item->delete();

        AuditLogService::log('hapus', 'sistem', "Menghapus item legend: {$label}");

        return redirect()->route('dokumentasi.index')->with('success', 'Item legend berhasil dihapus.');
    }
}
