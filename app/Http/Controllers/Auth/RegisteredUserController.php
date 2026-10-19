<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $nama = $request->input('nama') ?? $request->input('name');
        $username = $request->input('username');
        if (! $username && $nama) {
            $username = Str::slug($nama, '_').'_'.rand(100, 999);
        }

        $request->merge([
            'nama' => $nama,
            'username' => $username,
        ]);

        $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'username' => strtolower($request->username),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);

        foreach (['alat', 'lokasi', 'jadwal'] as $modul) {
            $user->permissions()->create([
                'modul' => $modul,
                'diizinkan' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
