<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display list of FAQs with interactive search and category filtering.
     */
    public function index(Request $request): View
    {
        $faqs = Faq::orderBy('urutan')
            ->orderBy('id')
            ->get();

        $canManage = auth()->user()?->isAdmin() ?? false;

        $kategoriCounts = [
            'semua' => $faqs->count(),
            'alat_rig' => $faqs->where('kategori', 'alat_rig')->count(),
            'lokasi_peta' => $faqs->where('kategori', 'lokasi_peta')->count(),
            'jadwal_gantt' => $faqs->where('kategori', 'jadwal_gantt')->count(),
            'akun_akses' => $faqs->where('kategori', 'akun_akses')->count(),
        ];

        return view('faq', compact('faqs', 'canManage', 'kategoriCounts'));
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat menambah FAQ.');

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'jawaban' => ['required', 'string'],
            'kategori' => ['required', 'in:alat_rig,lokasi_peta,jadwal_gantt,akun_akses'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 0;

        $faq = Faq::create($validated);

        AuditLogService::log(
            'tambah',
            'sistem',
            "Menambahkan butir FAQ baru: \"{$faq->pertanyaan}\""
        );

        return redirect()->route('faq')->with('success', 'Butir FAQ berhasil ditambahkan.');
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, Faq $faq): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat mengubah FAQ.');

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'jawaban' => ['required', 'string'],
            'kategori' => ['required', 'in:alat_rig,lokasi_peta,jadwal_gantt,akun_akses'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 0;

        $faq->update($validated);

        AuditLogService::log(
            'edit',
            'sistem',
            "Memperbarui butir FAQ #{$faq->id}: \"{$faq->pertanyaan}\""
        );

        return redirect()->route('faq')->with('success', 'Butir FAQ berhasil diperbarui.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat menghapus FAQ.');

        $pertanyaan = $faq->pertanyaan;
        $faqId = $faq->id;

        $faq->delete();

        AuditLogService::log(
            'hapus',
            'sistem',
            "Menghapus butir FAQ #{$faqId}: \"{$pertanyaan}\""
        );

        return redirect()->route('faq')->with('success', 'Butir FAQ berhasil dihapus.');
    }
}
