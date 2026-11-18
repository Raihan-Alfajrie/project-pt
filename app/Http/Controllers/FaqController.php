<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\KontakBantuan;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        // Jika user login di internal panel dan tidak secara eksplisit meminta tampilan publik
        if (auth()->check() && $request->query('view') !== 'public') {
            return view('faq', compact('faqs', 'canManage', 'kategoriCounts'));
        }

        return view('faq-public', compact('faqs', 'canManage', 'kategoriCounts'));
    }

    /**
     * Kirim pertanyaan baru dari halaman FAQ publik oleh pengunjung.
     */
    public function submitQuestion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'instansi' => ['nullable', 'string', 'max:150'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'pertanyaan' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'pertanyaan.min' => 'Pertanyaan minimal terdiri dari 5 karakter.',
        ]);

        $pesan = KontakBantuan::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'instansi' => $validated['instansi'] ?? null,
            'kategori' => 'pertanyaan_faq',
            'perihal' => 'Pertanyaan FAQ: ' . Str::limit($validated['pertanyaan'], 60),
            'pesan' => $validated['pertanyaan'],
            'status' => 'baru',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        NotificationService::notifyKontakBantuanSubmitted($pesan);

        return redirect()->route('faq', ['view' => 'public'])
            ->with('success_pertanyaan', 'Pertanyaan Anda berhasil diajukan! Tim Administrator Pertamina akan merespons pertanyaan Anda secepatnya.');
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
