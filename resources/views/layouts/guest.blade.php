<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pertamina Admin Panel — Rig Monitoring Zona 4' }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo_pertamina.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --red: #ea2027; --red-strong: #c8141b; --red-soft: rgba(234,32,39,.16);
            --bg: #151113; --panel: #1e1517; --panel-2: #251a1d; --panel-3: #2c1f22;
            --border: rgba(234,32,39,.25); --border-soft: rgba(255,255,255,.08);
            --text: #f6f5f4; --text-dim: rgba(246,245,244,.66); --text-faint: rgba(246,245,244,.42);
            font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box;
            padding-top: env(safe-area-inset-top, 0px); padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        @media (prefers-color-scheme: light) {
            :root:not([data-theme="dark"]) {
                --bg: #f5f3f1; --panel: #ffffff; --panel-2: #faf7f6; --panel-3: #f2ecea;
                --text: #221417; --text-dim: #5c4b4d; --text-faint: #8b7a7c; --border: rgba(234,32,39,.2); --border-soft: rgba(0,0,0,.08);
            }
        }
        :root[data-theme="dark"] {
            --bg: #151113; --panel: #1e1517; --panel-2: #251a1d; --text: #f6f5f4; --text-dim: rgba(246,245,244,.66); --text-faint: rgba(246,245,244,.42);
        }
        * { box-sizing: border-box; }
        html { scroll-padding-top: 80px; scroll-behavior: smooth; }
        body { margin: 0; background: var(--bg); color: var(--text); min-height: 100%; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Space Grotesk', sans-serif; letter-spacing: -.01em; margin: 0; }
        a { text-decoration: none; color: inherit; }
        .bgdots {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image: radial-gradient(rgba(234,32,39,.28) 1.3px, transparent 1.3px);
            background-size: 26px 26px; opacity: .5;
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 40%, transparent 90%);
            mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 40%, transparent 90%);
        }

        /* ===== public topbar ===== */
        .topbar { position: sticky; top: 0; z-index: 30; display: flex; align-items: center; justify-content: space-between; padding: 16px 32px; padding-top: calc(16px + env(safe-area-inset-top,0px)); background: rgba(21,17,19,.85); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-soft); }
        @media (prefers-color-scheme: light) { :root:not([data-theme="dark"]) .topbar { background: rgba(245,243,241,.85); } }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand .mark { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .brand .mark img { width: 34px; height: 34px; object-fit: contain; display: block; border-radius: 8px; }
        .brand .name { font-weight: 700; font-size: 14.5px; line-height: 1.15; }
        .brand .sub { font-size: 10.5px; color: var(--text-faint); }
        .pubnav { display: flex; align-items: center; gap: 26px; }
        .pubnav a { font-size: 13.5px; font-weight: 600; color: var(--text-dim); transition: color .2s; }
        .pubnav a.active, .pubnav a:hover { color: #fff; }
        @media (prefers-color-scheme: light) { :root:not([data-theme="dark"]) .pubnav a.active, :root:not([data-theme="dark"]) .pubnav a:hover { color: var(--text); } }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .btn-outline { padding: 9px 17px; border-radius: 9px; border: 1px solid var(--border); color: var(--text); font-size: 13px; font-weight: 700; cursor: pointer; background: transparent; font-family: inherit; transition: all .2s; display: inline-flex; align-items: center; justify-content: center; }
        .btn-outline:hover { border-color: var(--red); }
        .btn-fill { padding: 9px 17px; border-radius: 9px; border: 0; background: var(--red); color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .2s; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fill:hover { background: var(--red-strong); }

        /* ===== auth modal ===== */
        .overlay { position: fixed; inset: 0; background: rgba(8,6,7,.75); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
        .overlay.show { display: flex; }
        .auth-modal { background: var(--panel); border: 1px solid var(--border); border-radius: 18px; width: 100%; max-width: 420px; overflow: hidden; box-shadow: 0 24px 70px rgba(0,0,0,.65); }
        .auth-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 22px 0; }
        .auth-head h3 { font-size: 17px; }
        .auth-close { width: 28px; height: 28px; border-radius: 8px; background: var(--panel-2); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text); border: 0; }
        .auth-close svg { width: 13px; height: 13px; }
        .auth-tabs { display: flex; gap: 4px; margin: 18px 22px 0; background: var(--panel-2); padding: 3px; border-radius: 10px; }
        .auth-tab { flex: 1; text-align: center; padding: 9px; border-radius: 8px; font-size: 12.5px; font-weight: 700; color: var(--text-dim); cursor: pointer; border: 0; background: transparent; font-family: inherit; }
        .auth-tab.on { background: var(--red); color: #fff; }
        .auth-body { padding: 20px 22px 22px; display: flex; flex-direction: column; gap: 12px; }
        .auth-body input[type="text"], .auth-body input[type="email"], .auth-body input[type="password"] { background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 9px; padding: 11px 13px; color: var(--text); font-size: 13px; font-family: inherit; outline: 0; width: 100%; box-sizing: border-box; }
        .auth-body input:focus { border-color: var(--red); }
        .auth-body label { font-size: 11.5px; font-weight: 700; color: var(--text-dim); margin-bottom: -4px; display: block; }
        .auth-submit { margin-top: 6px; padding: 12px; border-radius: 9px; border: 0; background: var(--red); color: #fff; font-size: 13.5px; font-weight: 700; cursor: pointer; font-family: inherit; width: 100%; }
        .auth-submit:hover { background: var(--red-strong); }
        .auth-error { color: #ff5a5f; font-size: 11.5px; margin-top: -6px; line-height: 1.3; }
        .auth-note { font-size: 11px; color: var(--text-faint); text-align: center; margin-top: 6px; line-height: 1.5; }
        .auth-panel { display: none; flex-direction: column; gap: 12px; }
        .auth-panel.on { display: flex; }

        @media (max-width: 900px) {
            .pubnav { display: none; }
            .topbar { padding: 14px 18px; }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="bgdots"></div>

    <div class="topbar">
        <a href="{{ route('home') }}" class="brand">
            <div class="mark">
                <img src="{{ asset('logo_pertamina.webp') }}" alt="Logo Pertamina" width="34" height="34">
            </div>
            <div>
                <div class="name">Pertamina</div>
                <div class="sub">Rig Monitoring Zona 4</div>
            </div>
        </a>
        <nav class="pubnav">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
        </nav>
        <div class="topbar-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-fill">Ke Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline; margin:0;">
                    @csrf
                    <button type="submit" class="btn-outline">Keluar</button>
                </form>
            @else
                <button class="btn-outline" id="btnOpenLogin" type="button">Masuk</button>
                <button class="btn-fill" id="btnOpenRegister" type="button">Daftar</button>
            @endauth
        </div>
    </div>

    <main>
        {{ $slot }}
    </main>

    <div class="footer" style="position:relative; z-index:1; text-align:center; padding:32px 24px 48px; font-size:11.5px; color:var(--text-faint); border-top:1px solid var(--border-soft);">
        © 2026 Pertamina Admin Panel — Internal Use Only
    </div>

    <!-- Auth Modal -->
    <div class="overlay {{ ($errors->any() || request('auth')) ? 'show' : '' }}" id="overlayAuth">
        <div class="auth-modal">
            <div class="auth-head">
                <h3 id="authTitle">Masuk ke akun</h3>
                <button class="auth-close" id="authClose" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="auth-tabs">
                <button class="auth-tab" id="tabLogin" type="button">Masuk</button>
                <button class="auth-tab" id="tabRegister" type="button">Daftar</button>
            </div>
            <div class="auth-body">
                <!-- Panel Login -->
                <form method="POST" action="{{ route('login') }}" class="auth-panel" id="panelLogin">
                    @csrf
                    <div>
                        <label for="login_email">Email atau Username</label>
                        <input id="login_email" type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com atau username">
                        @if ($errors->has('email') && !old('nama') && !old('username'))
                            <div class="auth-error">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="login_password">Kata Sandi</label>
                        <input id="login_password" type="password" name="password" required placeholder="••••••••">
                        @if ($errors->has('password') && !old('nama') && !old('username'))
                            <div class="auth-error">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; margin-top:2px;">
                        <label style="display:flex; align-items:center; gap:7px; cursor:pointer;">
                            <input type="checkbox" name="remember" style="margin:0; width:auto;"> Ingat saya
                        </label>
                    </div>
                    <button type="submit" class="auth-submit" id="submitLogin">Masuk</button>
                    <div class="auth-note">Gunakan akun terdaftar atau hubungi Administrator untuk akses modul tambahan.</div>
                </form>

                <!-- Panel Register -->
                <form method="POST" action="{{ route('register') }}" class="auth-panel" id="panelRegister">
                    @csrf
                    <div>
                        <label for="reg_nama">Nama Lengkap</label>
                        <input id="reg_nama" type="text" name="nama" value="{{ old('nama') }}" required placeholder="Nama Lengkap Anda">
                        @error('nama')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="reg_username">Username</label>
                        <input id="reg_username" type="text" name="username" value="{{ old('username') }}" required placeholder="cth. budi.santoso">
                        @error('username')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="reg_email">Email</label>
                        <input id="reg_email" type="email" name="email" value="{{ old('nama') ? old('email') : '' }}" required placeholder="nama@perusahaan.com">
                        @if ($errors->has('email') && (old('nama') || old('username')))
                            <div class="auth-error">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="reg_password">Kata Sandi</label>
                        <input id="reg_password" type="password" name="password" required placeholder="Minimal 6 karakter">
                        @if ($errors->has('password') && (old('nama') || old('username')))
                            <div class="auth-error">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <button type="submit" class="auth-submit" id="submitRegister">Daftar Akun Baru</button>
                    <div class="auth-note">Akun baru akan didaftarkan sebagai Pekerja operasional WK Zona 4.</div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('overlayAuth');
        const tabLogin = document.getElementById('tabLogin');
        const tabRegister = document.getElementById('tabRegister');
        const panelLogin = document.getElementById('panelLogin');
        const panelRegister = document.getElementById('panelRegister');
        const authTitle = document.getElementById('authTitle');
        const authClose = document.getElementById('authClose');
        const btnOpenLogin = document.getElementById('btnOpenLogin');
        const btnOpenRegister = document.getElementById('btnOpenRegister');

        function setTab(tab) {
            const isLogin = tab === 'login';
            tabLogin.classList.toggle('on', isLogin);
            tabRegister.classList.toggle('on', !isLogin);
            panelLogin.classList.toggle('on', isLogin);
            panelRegister.classList.toggle('on', !isLogin);
            authTitle.textContent = isLogin ? "Masuk ke akun" : "Buat akun baru";
        }

        function openAuth(tab) {
            overlay.classList.add('show');
            setTab(tab);
        }

        function closeAuth() {
            overlay.classList.remove('show');
        }

        if (btnOpenLogin) btnOpenLogin.addEventListener('click', () => openAuth('login'));
        if (btnOpenRegister) btnOpenRegister.addEventListener('click', () => openAuth('register'));
        if (tabLogin) tabLogin.addEventListener('click', () => setTab('login'));
        if (tabRegister) tabRegister.addEventListener('click', () => setTab('register'));
        if (authClose) authClose.addEventListener('click', closeAuth);

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeAuth();
        });

        // Initial tab check from URL or Errors
        const isRegisterActive = @json(old('nama') || old('username') || request('auth') === 'register');
        setTab(isRegisterActive ? 'register' : 'login');
    </script>
</body>
</html>
