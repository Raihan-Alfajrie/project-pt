<x-guest-layout title="Masuk — Pertamina Admin Panel" :standalone="true">
    @push('styles')
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            position: relative;
            z-index: 1;
        }
        .auth-top-nav {
            width: 100%;
            max-width: 440px;
            margin-bottom: 14px;
            display: flex;
            justify-content: flex-start;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-dim);
            text-decoration: none;
            padding: 7px 13px;
            border-radius: 9px;
            background: var(--panel);
            border: 1px solid var(--border-soft);
            transition: all .2s ease;
        }
        .back-link:hover {
            color: #fff;
            border-color: var(--border);
            transform: translateX(-2px);
        }
        .back-link svg {
            width: 14px;
            height: 14px;
        }
        .auth-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 38px 34px 34px;
            box-shadow: 0 24px 70px rgba(0,0,0,.65);
            position: relative;
            box-sizing: border-box;
        }
        .auth-logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .auth-logo {
            width: 86px;
            height: 86px;
            object-fit: contain;
            filter: drop-shadow(0 6px 16px rgba(0,0,0,.4));
        }
        .auth-title {
            font-size: 21px;
            font-weight: 800;
            color: #fff;
            text-align: center;
            margin: 0 0 6px;
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -.02em;
        }
        .auth-sub {
            font-size: 13px;
            color: var(--text-dim);
            text-align: center;
            margin: 0 0 26px;
            font-weight: 400;
            line-height: 1.5;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 7px;
        }
        .auth-card .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .auth-card .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 5;
            color: rgba(246, 245, 244, 0.55);
            transition: color .2s ease;
        }
        .auth-card .input-icon svg {
            width: 19px;
            height: 19px;
            display: block;
        }
        .auth-card input.form-input,
        .auth-card input[type="text"],
        .auth-card input[type="email"],
        .auth-card input[type="password"] {
            width: 100% !important;
            height: 48px !important;
            background-color: #251a1d !important;
            border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 14px !important;
            padding-left: 48px !important;
            padding-right: 16px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            color: #ffffff !important;
            font-size: 13.5px !important;
            line-height: 48px !important;
            font-family: inherit !important;
            outline: none !important;
            box-shadow: none !important;
            transition: all .2s ease !important;
            box-sizing: border-box !important;
        }
        .auth-card input.form-input::placeholder,
        .auth-card input[type="text"]::placeholder,
        .auth-card input[type="email"]::placeholder,
        .auth-card input[type="password"]::placeholder {
            color: rgba(246, 245, 244, 0.4) !important;
            opacity: 1 !important;
        }
        .auth-card input.form-input:focus,
        .auth-card input[type="text"]:focus,
        .auth-card input[type="email"]:focus,
        .auth-card input[type="password"]:focus {
            border-color: var(--red) !important;
            background-color: #2a1c20 !important;
            box-shadow: 0 0 0 3px rgba(234, 32, 39, 0.22) !important;
        }
        .auth-card .input-wrap:focus-within .input-icon {
            color: var(--red) !important;
        }
        .auth-card input.is-invalid {
            border-color: #ff5a5f !important;
        }
        .auth-card input:-webkit-autofill,
        .auth-card input:-webkit-autofill:hover, 
        .auth-card input:-webkit-autofill:focus, 
        .auth-card input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #251a1d inset !important;
            -webkit-text-fill-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 4px;
            margin-bottom: 22px;
            font-size: 13px;
        }
        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-dim);
            user-select: none;
            font-size: 13px;
        }
        .auth-card input[type="checkbox"] {
            width: 18px !important;
            height: 18px !important;
            border-radius: 5px !important;
            background-color: #251a1d !important;
            border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
            accent-color: var(--red) !important;
            cursor: pointer !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .link-blue {
            color: #3b82f6;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: color .15s ease;
        }
        .link-blue:hover {
            color: #60a5fa;
            text-decoration: underline;
        }
        .font-bold {
            font-weight: 700;
        }
        .btn-submit {
            width: 100%;
            height: 46px;
            background: var(--red);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: inherit;
            transition: all .2s ease;
            box-shadow: 0 4px 14px rgba(234,32,39,.3);
        }
        .btn-submit:hover {
            background: var(--red-strong);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(234,32,39,.45);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .auth-bottom {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--text-dim);
        }
        .form-error {
            color: #ff5a5f;
            font-size: 11.5px;
            margin-top: 6px;
            line-height: 1.3;
        }
    </style>
    @endpush

    <div class="auth-page">
        <div class="auth-top-nav">
            <a href="{{ route('home') }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <div class="auth-card">
            <div class="auth-logo-wrap">
                <img src="{{ asset('logo_pertamina_2.png') }}" alt="Logo Pertamina" class="auth-logo">
            </div>

            <h1 class="auth-title">Selamat Datang Kembali</h1>
            <p class="auth-sub">Silahkan konfirmasi diri anda sebelum masuk</p>

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <!-- Username atau Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Username atau Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan username atau email" class="form-input @error('email') is-invalid @enderror">
                    </div>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </span>
                        <input id="password" type="password" name="password" required placeholder="Masukkan password" class="form-input @error('password') is-invalid @enderror">
                    </div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Ingat Saya & Lupa Password -->
                <div class="form-row">
                    <label class="remember-label" for="remember">
                        <input type="checkbox" name="remember" id="remember" class="form-checkbox">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-blue">Lupa Password?</a>
                    @endif
                </div>

                <!-- Tombol Masuk -->
                <button type="submit" class="btn-submit">Masuk</button>

                <!-- Belum punya akun -->
                <div class="auth-bottom">
                    Belum punya akun? <a href="{{ route('register') }}" class="link-blue font-bold">Daftar</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
