<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $lastLoginLog = $user->auditLogs()->where('aksi', 'login')->latest('waktu')->first();
        $lastLogin = $lastLoginLog?->waktu
            ? $lastLoginLog->waktu->translatedFormat('d M Y, H:i')
            : ($user->created_at ? $user->created_at->translatedFormat('d M Y, H:i') : now()->translatedFormat('d M Y, H:i'));

        $ipAddress = $request->ip() ?: '127.0.0.1';
        $userAgent = $request->userAgent() ?: 'Chrome 153.0.0.0 · Windows';

        return view('profile.edit', [
            'user' => $user,
            'lastLogin' => $lastLogin,
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $nama = $validated['nama'] ?? $validated['name'] ?? $user->nama;
        $user->nama = $nama;
        $user->email = $validated['email'];

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = $request->file('foto_profil')->store('profile_photos', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        AuditLogService::log('edit', 'user', 'Memperbarui profil akun sendiri', $user);

        return Redirect::route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
