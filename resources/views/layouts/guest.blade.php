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
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('pertamina_theme') || 'dark';
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
        function toggleTheme() {
            var currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            var newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            try {
                localStorage.setItem('pertamina_theme', newTheme);
            } catch (e) {}
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
        }
    </script>
    <style>
        :root, :root[data-theme="dark"] {
            --red: #ea2027; --red-strong: #c8141b; --red-soft: rgba(234,32,39,.16);
            --bg: #151113; --panel: #1e1517; --panel-2: #251a1d; --panel-3: #2c1f22;
            --border: rgba(234,32,39,.25); --border-soft: rgba(255,255,255,.08);
            --text: #f6f5f4; --text-dim: rgba(246,245,244,.66); --text-faint: rgba(246,245,244,.42);
            font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box;
            padding-top: env(safe-area-inset-top, 0px); padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        :root[data-theme="light"] {
            --bg: #f5f3f1; --panel: #ffffff; --panel-2: #faf7f6; --panel-3: #f2ecea;
            --text: #221417; --text-dim: #5c4b4d; --text-faint: #8b7a7c; --border: rgba(234,32,39,.2); --border-soft: rgba(0,0,0,.08);
        }
        :root[data-theme="light"] .topbar { background: rgba(245,243,241,.85); }
        :root[data-theme="light"] .pubnav a.active, :root[data-theme="light"] .pubnav a:hover { color: var(--text); }
        :root[data-theme="dark"] .theme-icon-moon,
        :root:not([data-theme="light"]) .theme-icon-moon {
            display: none !important;
        }
        :root[data-theme="dark"] .theme-icon-sun,
        :root:not([data-theme="light"]) .theme-icon-sun {
            display: block !important;
        }
        :root[data-theme="light"] .theme-icon-sun {
            display: none !important;
        }
        :root[data-theme="light"] .theme-icon-moon {
            display: block !important;
        }
        .btn-theme-guest {
            width: 36px; height: 36px; border-radius: 9px;
            border: 1px solid var(--border-soft); background: var(--panel-2);
            color: var(--text); display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s;
        }
        .btn-theme-guest:hover { border-color: var(--red); color: #fff; transform: translateY(-1px); }
        :root[data-theme="light"] .btn-theme-guest:hover { color: var(--red); }
        .btn-theme-guest svg { width: 17px; height: 17px; }
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
            .pubnav { display: none !important; }
            .desktop-auth-actions { display: none !important; }
            .mobile-menu-btn { display: inline-flex !important; }
            .topbar { padding: 14px 18px; }
        }

        /* ===== mobile drawer ===== */
        [x-cloak] { display: none !important; }
        .mobile-menu-btn {
            display: none;
            width: 36px; height: 36px; border-radius: 9px;
            border: 1px solid var(--border-soft); background: var(--panel-2);
            color: var(--text); align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s;
        }
        .mobile-menu-btn:hover { border-color: var(--red); color: #fff; transform: translateY(-1px); }
        :root[data-theme="light"] .mobile-menu-btn:hover { color: var(--red); }
        .drawer-backdrop {
            position: fixed; inset: 0; background: rgba(8,6,7,.7); backdrop-filter: blur(4px);
            z-index: 100;
        }
        .drawer-panel {
            position: fixed; top: 0; right: 0; bottom: 0; width: 85%; max-width: 320px;
            background: var(--panel); border-left: 1px solid var(--border-soft);
            z-index: 101; display: flex; flex-direction: column; justify-content: space-between;
            padding: 24px 20px; box-shadow: -12px 0 40px rgba(0,0,0,.5);
            overflow-y: auto; box-sizing: border-box;
        }
        .drawer-head { display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid var(--border-soft); }
        .drawer-close {
            width: 32px; height: 32px; border-radius: 8px; background: var(--panel-2);
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            color: var(--text); border: 1px solid var(--border-soft); transition: all .2s;
        }
        .drawer-close:hover { border-color: var(--red); color: var(--red); }
        .drawer-nav { display: flex; flex-direction: column; gap: 8px; margin-top: 20px; }
        .drawer-nav a {
            display: flex; align-items: center; justify-content: space-between; padding: 12px 14px;
            border-radius: 10px; font-size: 14px; font-weight: 600; color: var(--text-dim);
            transition: all .2s; background: transparent; border: 1px solid transparent;
        }
        .drawer-nav a:hover, .drawer-nav a.active {
            color: var(--text); background: var(--panel-2); border-color: var(--border-soft);
            border-left: 3px solid var(--red);
        }
        .drawer-auth { display: flex; flex-direction: column; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-soft); }
        .drawer-auth .btn-outline, .drawer-auth .btn-fill { width: 100%; text-align: center; justify-content: center; padding: 11px; font-size: 13.5px; }

        /* ===== corporate footer ===== */
        .corp-footer {
            position: relative; z-index: 1; background: var(--panel);
            border-top: 1px solid var(--border-soft); margin-top: 60px;
        }
        :root[data-theme="light"] .corp-footer {
            background: #faf8f7; border-top-color: rgba(0,0,0,.08);
        }
        .corp-footer-container {
            max-width: 1200px; margin: 0 auto; padding: 56px 24px 40px;
            display: grid; grid-template-columns: 2fr 1fr 1.1fr 1.4fr; gap: 40px;
            box-sizing: border-box;
        }
        @media (max-width: 960px) {
            .corp-footer-container { grid-template-columns: 1fr 1fr; gap: 32px; padding: 44px 20px 32px; }
        }
        @media (max-width: 640px) {
            .corp-footer-container { grid-template-columns: 1fr; gap: 28px; padding: 36px 18px 24px; }
        }
        .corp-footer-col { display: flex; flex-direction: column; gap: 14px; }
        .corp-footer-brand { display: flex; align-items: center; gap: 12px; }
        .footer-logo { width: 38px; height: 38px; object-fit: contain; border-radius: 8px; flex-shrink: 0; }
        .footer-brand-title { font-weight: 700; font-size: 15px; color: var(--text); font-family: 'Space Grotesk', sans-serif; line-height: 1.2; }
        .footer-brand-sub { font-size: 11px; color: var(--text-faint); margin-top: 2px; }
        .footer-desc { font-size: 12.5px; color: var(--text-dim); line-height: 1.6; margin: 0; }
        .footer-live-badge {
            display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px;
            border-radius: 999px; background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25);
            font-size: 11.5px; font-weight: 600; color: #10b981; width: fit-content;
        }
        .live-dot {
            width: 7px; height: 7px; border-radius: 50%; background: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,.3); animation: pulseDot 2s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 3px rgba(16,185,129,.3); }
            50% { box-shadow: 0 0 0 6px rgba(16,185,129,.1); }
        }
        .footer-heading {
            font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
            color: var(--text); font-family: 'Space Grotesk', sans-serif; margin: 0 0 4px 0;
        }
        .footer-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 9px; }
        .footer-links li a, .footer-links li span {
            font-size: 12.5px; color: var(--text-dim); transition: color .2s; display: inline-flex; align-items: center;
        }
        .footer-links li a:hover { color: var(--red); }
        .footer-contact-item { display: flex; align-items: flex-start; gap: 10px; font-size: 12px; color: var(--text-dim); line-height: 1.5; }
        .footer-contact-item svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; color: var(--red); }
        .corp-footer-bottom {
            border-top: 1px solid var(--border-soft); padding: 18px 24px;
            background: var(--panel-2);
        }
        :root[data-theme="light"] .corp-footer-bottom {
            background: #f3efed;
        }
        .footer-bottom-inner {
            max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; font-size: 11.5px; color: var(--text-faint);
        }
        @media (max-width: 768px) {
            .footer-bottom-inner { flex-direction: column; text-align: center; justify-content: center; }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ mobileDrawerOpen: false }" @resize.window="if (window.innerWidth > 900) mobileDrawerOpen = false" :class="{ 'overflow-hidden': mobileDrawerOpen }">
    <div class="bgdots"></div>

    @if (! ($standalone ?? false))
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
            <a href="{{ route('faq') }}" class="{{ request()->routeIs('faq*') ? 'active' : '' }}">FAQ</a>
        </nav>
        <div class="topbar-actions">
            <!-- Theme Toggle Button -->
            <button class="btn-theme-guest"
                    type="button"
                    onclick="toggleTheme()"
                    title="Ganti mode terang/gelap"
                    aria-label="Ganti mode terang/gelap">
                <svg class="theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
            <div class="desktop-auth-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-fill">Ke Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline; margin:0;">
                        @csrf
                        <button type="submit" class="btn-outline">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline" id="btnOpenLogin">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-fill" id="btnOpenRegister">Daftar</a>
                @endauth
            </div>
            <!-- Mobile Hamburger Button -->
            <button class="mobile-menu-btn"
                    type="button"
                    @click="mobileDrawerOpen = !mobileDrawerOpen"
                    aria-label="Buka Menu Navigasi"
                    :aria-expanded="mobileDrawerOpen.toString()">
                <svg x-show="!mobileDrawerOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:19px; height:19px;">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
                <svg x-show="mobileDrawerOpen" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:19px; height:19px;">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Backdrop & Panel -->
    <div x-show="mobileDrawerOpen"
         x-cloak
         class="drawer-backdrop"
         @click="mobileDrawerOpen = false"
         @keydown.escape.window="mobileDrawerOpen = false"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <div x-show="mobileDrawerOpen"
         x-cloak
         class="drawer-panel"
         x-transition:enter="transform transition ease-out duration-200"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">
        <div>
            <div class="drawer-head">
                <a href="{{ route('home') }}" class="brand" @click="mobileDrawerOpen = false">
                    <div class="mark">
                        <img src="{{ asset('logo_pertamina.webp') }}" alt="Logo Pertamina" width="30" height="30">
                    </div>
                    <div>
                        <div class="name" style="font-size:13.5px;">Pertamina</div>
                        <div class="sub" style="font-size:10px;">Rig Monitoring Zona 4</div>
                    </div>
                </a>
                <button type="button" class="drawer-close" @click="mobileDrawerOpen = false" aria-label="Tutup Menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px;">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="drawer-nav">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @click="mobileDrawerOpen = false">
                    <span>Home</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px; opacity:.6;"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}" @click="mobileDrawerOpen = false">
                    <span>About</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px; opacity:.6;"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="{{ route('faq') }}" class="{{ request()->routeIs('faq*') ? 'active' : '' }}" @click="mobileDrawerOpen = false">
                    <span>FAQ</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px; opacity:.6;"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </nav>

            <div class="drawer-auth">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-fill" @click="mobileDrawerOpen = false">Ke Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn-outline" style="width:100%;">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline" @click="mobileDrawerOpen = false; openAuth('login'); return false;">Masuk ke Akun</a>
                    <a href="{{ route('register') }}" class="btn-fill" @click="mobileDrawerOpen = false; openAuth('register'); return false;">Daftar Akun Baru</a>
                @endauth
            </div>
        </div>

        <div style="padding-top:20px; border-top:1px solid var(--border-soft); margin-top:20px;">
            <div class="footer-live-badge" style="font-size:10.5px;">
                <span class="live-dot"></span>
                <span>Pusat Operasi WK Zona 4: Aktif</span>
            </div>
            <div style="font-size:11px; color:var(--text-faint); margin-top:10px;">
                Platform Monitoring Terpusat Armada &amp; Fasilitas Pemboran Migas.
            </div>
        </div>
    </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    @if (! ($standalone ?? false))
    <!-- Enhanced Corporate Footer -->
    <footer class="corp-footer">
        <div class="corp-footer-container">
            <!-- Kolom 1: Brand & Ikhtisar -->
            <div class="corp-footer-col">
                <div class="corp-footer-brand">
                    <img src="{{ asset('logo_pertamina.webp') }}" alt="Logo Pertamina" class="footer-logo">
                    <div>
                        <div class="footer-brand-title">PT Pertamina Hulu Rokan</div>
                        <div class="footer-brand-sub">Zona 4 — Regional 1 Sumatera</div>
                    </div>
                </div>
                <p class="footer-desc">
                    Sistem informasi operasional terpusat satu pintu (<em>single source of truth</em>) yang dirancang untuk mengawal keandalan armada rig pengeboran, inventaris peralatan, dan jadwal pergerakan tajuk sumur di wilayah kerja Zona 4 secara akurat dan transparan.
                </p>
                <div class="footer-live-badge">
                    <span class="live-dot"></span>
                    <span>Pusat Kendali Operasi: Aktif 24/7 (WIB)</span>
                </div>
            </div>

            <!-- Kolom 2: Navigasi Utama -->
            <div class="corp-footer-col">
                <h4 class="footer-heading">Navigasi Utama</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Beranda Platform</a></li>
                    <li><a href="{{ route('about') }}">Wilayah Kerja &amp; Lapangan</a></li>
                    <li><a href="{{ route('faq') }}">Pusat Bantuan &amp; FAQ</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard Operasional</a></li>
                    @else
                        <li><a href="{{ route('login') }}" onclick="openAuth('login'); return false;">Masuk ke Akun</a></li>
                        <li><a href="{{ route('register') }}" onclick="openAuth('register'); return false;">Registrasi Pekerja</a></li>
                    @endauth
                </ul>
            </div>

            <!-- Kolom 3: Wilayah Operasi Lapangan -->
            <div class="corp-footer-col">
                <h4 class="footer-heading">Wilayah Lapangan</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}#wilayah-lapangan">Lapangan Prabumulih</a></li>
                    <li><a href="{{ route('about') }}#wilayah-lapangan">Lapangan Limau</a></li>
                    <li><a href="{{ route('about') }}#wilayah-lapangan">Lapangan Pendopo</a></li>
                    <li><a href="{{ route('about') }}#wilayah-lapangan">Lapangan Adera</a></li>
                    <li><a href="{{ route('about') }}#wilayah-lapangan">Lapangan Ramba &amp; Ogan Komering</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Kontak & Dukungan Helpdesk -->
            <div class="corp-footer-col">
                <h4 class="footer-heading">Bantuan &amp; Operasional</h4>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Komplek Pertamina EP Asset 2, Kota Prabumulih, Sumatera Selatan</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span>helpdesk.rig@pertamina.com</span>
                </div>
                <div class="footer-contact-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Dukungan Operasi: 24 Jam Non-Stop</span>
                </div>
            </div>
        </div>

        <!-- Bagian Bawah Hak Cipta & Disclaimer -->
        <div class="corp-footer-bottom">
            <div class="footer-bottom-inner">
                <div class="footer-copy">
                    &copy; 2026 PT Pertamina Hulu Rokan Zona 4. Seluruh hak cipta dilindungi undang-undang.
                </div>
                <div class="footer-disclaimer">
                    <span>Dokumen &amp; Data Rahasia Perusahaan &bull; Akses Terbatas untuk Personel Berwenang</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Auth Modal -->
    <div class="overlay {{ ($errors->any() || request('auth')) ? 'show' : '' }}" id="overlayAuth">
        <div class="auth-modal">
            <div class="auth-head">
                <div style="display:flex; align-items:center; gap:10px;">
                    <img src="{{ asset('logo_pertamina_2.png') }}" alt="Logo Pertamina" style="width:28px; height:28px; object-fit:contain;">
                    <h3 id="authTitle">Masuk ke akun</h3>
                </div>
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
    @endif
    @stack('scripts')
</body>
</html>
