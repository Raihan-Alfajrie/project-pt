<x-guest-layout title="Daftar Akun — Pertamina Admin Panel">
    <div style="min-height: calc(100vh - 180px); display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; z-index: 1;">
        <div class="auth-modal" style="display: block;">
            <div class="auth-head" style="padding-bottom: 16px; border-bottom: 1px solid var(--border-soft);">
                <h3>Buat Akun Baru</h3>
            </div>
            <div class="auth-body">
                <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf
                    <div>
                        <label for="nama">Nama Lengkap</label>
                        <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required autofocus placeholder="Nama Lengkap Anda">
                        @error('nama')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required placeholder="cth. budi.santoso">
                        @error('username')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@perusahaan.com">
                        @error('email')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password">Kata Sandi</label>
                        <input id="password" type="password" name="password" required placeholder="Minimal 6 karakter">
                        @error('password')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="auth-submit">Daftar</button>
                    <div class="auth-note">
                        Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--red); font-weight: 700;">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
