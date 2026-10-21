<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Ensure only administrators can access this controller.
     */
    protected function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Administrator.');
        }
    }

    /**
     * Display the user management listing and KPI cards.
     */
    public function index(Request $request): View
    {
        $this->checkAdmin();

        $users = User::with('permissions')
            ->orderBy('id')
            ->get();

        $adminTotal = $users->where('jabatan', 'administrator')->count();
        $adminOn = $users->where('jabatan', 'administrator')->where('status', 'online')->count();
        $adminOff = $users->where('jabatan', 'administrator')->where('status', 'offline')->count();

        $userTotal = $users->where('jabatan', 'pekerja')->count();
        $userOn = $users->where('jabatan', 'pekerja')->where('status', 'online')->count();
        $userOff = $users->where('jabatan', 'pekerja')->where('status', 'offline')->count();

        $usersData = $users->map(fn($u) => [
            'id' => $u->id,
            'nama' => $u->nama,
            'username' => $u->username,
            'email' => $u->email,
            'jabatan' => ucfirst($u->jabatan),
            'status' => $u->status,
            'initials' => $u->initials,
            'akses' => $u->akses_map,
        ]);

        return view('users.index', compact(
            'users',
            'usersData',
            'adminTotal',
            'adminOn',
            'adminOff',
            'userTotal',
            'userOn',
            'userOff'
        ));
    }

    /**
     * Store a newly created user with manual password.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'jabatan' => ['required', 'in:administrator,pekerja'],
            'status' => ['required', 'in:online,offline'],
        ]);

        $newUser = User::create([
            'nama' => trim($validated['nama']),
            'username' => strtolower(trim($validated['username'])),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'jabatan' => $validated['jabatan'],
            'status' => $validated['status'],
        ]);

        // Default permissions for 3 operational modules
        foreach (['alat', 'lokasi', 'jadwal'] as $modul) {
            UserPermission::create([
                'user_id' => $newUser->id,
                'modul' => $modul,
                'diizinkan' => $newUser->jabatan === 'administrator' ? 1 : 0,
            ]);
        }

        AuditLogService::log('tambah', 'user', "Menambahkan akun user: {$newUser->nama} (@{$newUser->username})");
        NotificationService::notifyUserRegistered($newUser);

        return redirect()->route('users.index')->with('success', "User {$newUser->nama} (@{$newUser->username}) berhasil ditambahkan.");
    }

    /**
     * Update existing user information.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'jabatan' => ['required', 'in:administrator,pekerja'],
            'status' => ['required', 'in:online,offline'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $updateData = [
            'nama' => trim($validated['nama']),
            'username' => strtolower(trim($validated['username'])),
            'email' => strtolower(trim($validated['email'])),
            'jabatan' => $validated['jabatan'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLogService::log('edit', 'user', "Memperbarui data user: {$user->nama} (@{$user->username})");

        return redirect()->route('users.index')->with('success', "Data user {$user->nama} berhasil diperbarui.");
    }

    /**
     * Update operational module permissions for a user.
     */
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $this->checkAdmin();

        foreach (['alat', 'lokasi', 'jadwal'] as $modul) {
            $diizinkan = $request->boolean($modul) ? 1 : 0;
            UserPermission::updateOrCreate(
                ['user_id' => $user->id, 'modul' => $modul],
                ['diizinkan' => $diizinkan]
            );
        }

        AuditLogService::log('edit', 'user', "Memperbarui hak akses modul user: {$user->nama}");
        NotificationService::notifyUserPermissionsUpdated($user);

        return redirect()->route('users.index')->with('success', "Hak akses untuk {$user->nama} berhasil diperbarui.");
    }

    /**
     * Remove the specified user from database.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->checkAdmin();

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->withErrors(['error' => 'Tidak dapat menghapus akun Anda sendiri yang sedang aktif.']);
        }

        $nama = $user->nama;
        $username = $user->username;
        $user->delete();

        AuditLogService::log('hapus', 'user', "Menghapus akun user: {$nama} (@{$username})");

        return redirect()->route('users.index')->with('success', "User {$nama} (@{$username}) berhasil dihapus.");
    }
}
