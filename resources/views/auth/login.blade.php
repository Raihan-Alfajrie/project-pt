<x-guest-layout title="Masuk — Pertamina Admin Panel">
    <div style="min-height: calc(100vh - 180px); display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; z-index: 1;">
        <div class="auth-modal" style="display: block;">
            <div class="auth-head" style="padding-bottom: 16px; border-bottom: 1px solid var(--border-soft);">
                <h3>Masuk ke Akun</h3>
            </div>
            <div class="auth-body">
                <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf
                    <div>
                        <label for="email">Email atau Username</label>
                        <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com atau username">
                        @error('email')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password">Kata Sandi</label>
                        <input id="password" type="password" name="password" required placeholder="••••••••">
                        @error('password')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
                        <label style="display: flex; align-items: center; gap: 7px; cursor: pointer;">
                            <input type="checkbox" name="remember" style="margin: 0; width: auto;"> Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="auth-submit">Masuk</button>
                    <div class="auth-note">
                        Belum punya akun? <a href="{{ route('register') }}" style="color: var(--red); font-weight: 700;">Daftar di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
