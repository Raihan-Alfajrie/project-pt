<?php

namespace App\Http\Controllers;

use App\Models\KontakBantuan;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KontakBantuanController extends Controller
{
    /**
     * Simpan pesan atau permohonan bantuan baru dari pengunjung publik.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'instansi' => ['nullable', 'string', 'max:150'],
            'kategori' => ['required', 'in:bantuan,operasional,saran,pertanyaan_faq,lainnya'],
            'perihal' => ['required', 'string', 'max:200'],
            'pesan' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'perihal.required' => 'Perihal pesan wajib diisi.',
            'pesan.required' => 'Rincian pesan wajib diisi.',
            'pesan.min' => 'Rincian pesan minimal terdiri dari 10 karakter.',
        ]);

        $pesan = KontakBantuan::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
            'instansi' => $validated['instansi'] ?? null,
            'kategori' => $validated['kategori'],
            'perihal' => $validated['perihal'],
            'pesan' => $validated['pesan'],
            'status' => 'baru',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        NotificationService::notifyKontakBantuanSubmitted($pesan);

        return redirect()->to(url()->previous() . '#bantuan')
            ->with('success_kontak', 'Terima kasih! Pesan dan permohonan bantuan Anda berhasil dikirimkan. Tim operasional Pertamina akan segera menindaklanjuti pesan Anda.');
    }
}
