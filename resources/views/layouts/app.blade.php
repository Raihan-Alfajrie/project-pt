<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pertamina Admin Panel — ' . ($pageTitle ?? 'Dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --red: #ea2027;
            --red-strong: #c8141b;
            --red-soft: rgba(234,32,39,.16);
            --bg: #212326;
            --panel: #3b2226;
            --panel-2: #492226;
            --panel-3: #54282d;
            --border: rgba(234,32,39,.28);
            --border-soft: rgba(255,255,255,.08);
            --white: #ffffff;
            --text: #f6f5f4;
            --text-dim: rgba(246,245,244,.66);
            --text-faint: rgba(246,245,244,.4);
            --sidebar-w: 264px;
            --radius: 14px;
            font-family: 'Inter', system-ui, sans-serif;
            box-sizing: border-box;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        @media (prefers-color-scheme: light) {
            :root:not([data-theme="dark"]) {
                --bg: #eceae7; --panel: #ffffff; --panel-2: #f7f1f1; --panel-3: #f2e9e9;
                --text: #241417; --text-dim: #5c4b4d; --text-faint: #8b7a7c; --border: rgba(234,32,39,.22); --border-soft: rgba(0,0,0,.08);
            }
        }
        :root[data-theme="dark"] {
            --bg: #212326; --panel: #3b2226; --panel-2: #492226; --text: #f6f5f4; --text-dim: rgba(246,245,244,.66); --text-faint: rgba(246,245,244,.4);
        }
        * { box-sizing: border-box; }
        html { scroll-padding-top: env(safe-area-inset-top, 0px); }
        body { margin: 0; background: var(--bg); color: var(--text); min-height: 100%; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Space Grotesk', sans-serif; letter-spacing: -.01em; margin: 0; }
        .mono { font-family: 'IBM Plex Mono', monospace; }
        a { text-decoration: none; color: inherit; }

        /* ===== layout shell ===== */
        .shell { min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            display: flex; align-items: center; background: var(--red); color: #fff;
            padding-top: env(safe-area-inset-top, 0px); position: sticky; top: 0; z-index: 30;
        }
        .logo-block { width: var(--sidebar-w); flex: 0 0 auto; display: flex; align-items: center; gap: 11px; padding: 14px 18px; }
        .logo-block .mark { width: 36px; height: 36px; border-radius: 10px; background: #fff; display: flex; align-items: center; justify-content: center; }
        .logo-block .mark svg { width: 20px; height: 20px; }
        .logo-block .name { font-weight: 700; font-size: 15px; line-height: 1.15; }
        .logo-block .sub { font-size: 11px; opacity: .85; }
        .collapse-btn { margin-left: auto; width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.12); cursor: pointer; flex: 0 0 auto; border: 0; color: #fff; }
        .collapse-btn svg { width: 14px; height: 14px; }

        .topbar-mid { flex: 1; display: flex; align-items: center; gap: 12px; padding: 0 20px; border-left: 1px solid rgba(255,255,255,.18); min-width: 0; }
        .topbar-mid .ic { width: 34px; height: 34px; border-radius: 9px; background: rgba(255,255,255,.14); display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
        .topbar-mid .ic svg { width: 17px; height: 17px; }
        .topbar-mid h1 { font-size: 17px; }
        .topbar-mid .desc { font-size: 11.5px; opacity: .82; margin-top: 1px; }

        .topbar-right { display: flex; align-items: center; gap: 6px; padding: 0 20px; flex: 0 0 auto; }
        .navbtn { display: flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 9px; font-size: 13.5px; font-weight: 600; color: #fff; cursor: pointer; white-space: nowrap; transition: background .2s; }
        .navbtn svg { width: 15px; height: 15px; opacity: .92; }
        .navbtn:hover { background: rgba(255,255,255,.12); }
        .navbtn.active { background: rgba(255,255,255,.18); }
        .tb-divider { width: 1px; height: 24px; background: rgba(255,255,255,.28); margin: 0 6px; }
        .iconbtn { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; border: 0; background: transparent; color: #fff; }
        .iconbtn:hover { background: rgba(255,255,255,.12); }
        .iconbtn svg { width: 16px; height: 16px; }
        .badge-dot { position: absolute; top: 6px; right: 6px; width: 7px; height: 7px; border-radius: 50%; background: #fff; border: 2px solid var(--red); }
        .clockpill { display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 600; background: rgba(255,255,255,.14); padding: 7px 12px; border-radius: 99px; }
        .clockpill svg { width: 13px; height: 13px; }

        /* ===== body: sidebar + content ===== */
        .body-row { display: flex; align-items: stretch; flex: 1; min-height: calc(100vh - 66px); }
        .sidebar {
            width: var(--sidebar-w); flex: 0 0 auto; background: var(--red); min-height: calc(100vh - 66px);
            padding: 18px 14px 20px; display: flex; flex-direction: column; gap: 4px;
            position: sticky; top: 66px; height: calc(100vh - 66px); overflow-y: auto; transition: transform .25s ease;
        }
        .navgroup { display: flex; flex-direction: column; gap: 2px; margin-bottom: 14px; }
        .navlabel { font-size: 10.5px; font-weight: 700; color: rgba(255,255,255,.62); text-transform: uppercase; letter-spacing: .09em; padding: 8px 12px 6px; }
        .navitem { display: flex; align-items: center; gap: 11px; padding: 10px 12px; border-radius: 10px; font-size: 13.5px; font-weight: 600; color: rgba(255,255,255,.92); cursor: pointer; transition: background .15s; }
        .navitem svg { width: 17px; height: 17px; flex: 0 0 auto; }
        .navitem:hover { background: rgba(255,255,255,.12); }
        .navitem.active { background: #fff; color: var(--red-strong); }
        .navitem .tag { margin-left: auto; font-size: 9px; font-weight: 700; background: rgba(255,255,255,.22); color: #fff; padding: 2px 7px; border-radius: 99px; }
        .navitem.active .tag { background: var(--red-soft); color: var(--red-strong); }

        .sidebar-foot { margin-top: auto; display: flex; align-items: center; gap: 10px; padding: 12px 10px; border-top: 1px solid rgba(255,255,255,.2); text-decoration: none; border-radius: 8px; }
        .sidebar-foot:hover { background: rgba(255,255,255,.08); }
        .avatar { width: 32px; height: 32px; border-radius: 9px; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--red-strong); }
        .sidebar-foot .who { font-size: 12.5px; font-weight: 700; color: #fff; line-height: 1.2; }
        .sidebar-foot .role { font-size: 10.5px; color: rgba(255,255,255,.75); }
        .sidebar-foot svg { width: 14px; height: 14px; margin-left: auto; color: rgba(255,255,255,.75); }

        .main { flex: 1; min-width: 0; background: var(--bg); }
        .content { padding: 20px 24px 60px; display: flex; flex-direction: column; gap: 16px; max-width: 1600px; margin: 0 auto; }

        @media (max-width: 980px) {
            .sidebar {
                position: fixed; left: -264px; top: 0; bottom: 0; z-index: 50; height: 100vh;
            }
            .sidebar.mobile-open {
                left: 0; box-shadow: 0 0 30px rgba(0,0,0,.5);
            }
            .logo-block { width: auto; }
            .topbar-right .navbtn span { display: none; }
            .content { padding: 14px; }
        }
    </style>
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="shell" x-data="{ sidebarOpen: false }">

    <!-- Topbar -->
    <header class="topbar">
        <div class="logo-block">
            <a href="{{ route('dashboard') }}" class="mark" aria-label="Logo Pertamina">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#ea2027"/><path d="M12 5c2 3 4 4.5 4 7.5A4 4 0 0112 17a4 4 0 01-4-4.5C8 9.5 10 8 12 5z" fill="#fff"/></svg>
            </a>
            <div>
                <div class="name">Pertamina</div>
                <div class="sub">Admin Panel</div>
            </div>
            <button class="collapse-btn" @click="sidebarOpen = !sidebarOpen" type="button" aria-label="Toggle Sidebar">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16"/></svg>
            </button>
        </div>

        <div class="topbar-mid">
            <div class="ic">
                {{ $pageIcon ?? '' }}
                @if (!isset($pageIcon))
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                @endif
            </div>
            <div>
                <h1>{{ $pageTitle ?? 'Dashboard' }}</h1>
                <div class="desc">{{ $pageDesc ?? 'Pantau status, jadwal dan progres' }}</div>
            </div>
        </div>

        <div class="topbar-right">
            <a class="navbtn" href="{{ route('home') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                <span>Home</span>
            </a>
            <a class="navbtn" href="{{ route('about') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v5h1"/></svg>
                <span>About</span>
            </a>
            <a class="navbtn {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                <span>Dashboard</span>
            </a>
            <div class="tb-divider"></div>
            <button class="iconbtn" type="button" aria-label="Notifikasi">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                <span class="badge-dot"></span>
            </button>
            <div class="clockpill">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                <span id="clock">--:--</span>
            </div>
        </div>
    </header>

    <div class="body-row">
        <!-- Sidebar -->
        <aside class="sidebar" :class="{ 'mobile-open': sidebarOpen }">
            <div class="navgroup">
                <div class="navlabel">Utama</div>
                <a class="navitem {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="navgroup">
                <div class="navlabel">Manajemen Data</div>
                <a class="navitem {{ request()->routeIs('alat.*') ? 'active' : '' }}" href="{{ route('alat.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg>
                    Alat
                </a>
                <a class="navitem {{ request()->routeIs('lokasi.*') ? 'active' : '' }}" href="{{ route('lokasi.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
                    Lokasi
                </a>
                <a class="navitem {{ request()->routeIs('jadwal.*') ? 'active' : '' }}" href="{{ route('jadwal.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                    Jadwal
                </a>
                @if (Auth::user()->isAdmin())
                    <a class="navitem {{ request()->routeIs('import-export.*') ? 'active' : '' }}" href="{{ route('import-export.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3v12M8 11l4 4 4-4"/><path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                        Import / Export
                    </a>
                @endif
            </div>

            <div class="navgroup">
                <div class="navlabel">System &amp; Security</div>
                @if (Auth::user()->isAdmin())
                    <a class="navitem {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.4"/><path d="M4 20c1.6-4 5-6 8-6s6.4 2 8 6"/></svg>
                        Manajemen User
                    </a>
                @endif
                <a class="navitem {{ request()->routeIs('audit-log.*') ? 'active' : '' }}" href="{{ route('audit-log.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M6 3h9l5 5v13H6z"/><path d="M9 12h6M9 16h6M9 8h2"/></svg>
                    Audit Log
                </a>
            </div>

            <div class="navgroup">
                <div class="navlabel">Support</div>
                <a class="navitem {{ request()->routeIs('dokumentasi.*') ? 'active' : '' }}" href="{{ route('dokumentasi.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 19.5V4.6c0-.9.7-1.6 1.6-1.6H16l4 4v12.5a1.6 1.6 0 01-1.6 1.6H5.6A1.6 1.6 0 014 19.5z"/><path d="M15 3v4.4c0 .66.54 1.2 1.2 1.2H20"/></svg>
                    Dokumentasi
                </a>
                <a class="navitem {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 .5c0 1.6-2.3 1.8-2.4 3.4M12 17h.01"/></svg>
                    FAQ
                </a>
                <a class="navitem {{ request()->routeIs('profil') || request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profil') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20c1.8-4.2 5.4-6.4 7.5-6.4s5.7 2.2 7.5 6.4"/></svg>
                    Profil
                </a>
            </div>

            @php
                $user = Auth::user();
                $words = explode(' ', $user->nama ?? $user->name ?? 'User');
                $initials = '';
                foreach ($words as $w) {
                    if (!empty($w)) $initials .= strtoupper($w[0]);
                }
                $initials = substr($initials, 0, 2) ?: 'U';
            @endphp
            <a class="sidebar-foot" href="{{ route('profil') }}">
                <div class="avatar" style="overflow:hidden;">
                    @if ($user->foto_profil)
                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama ?? $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div>
                    <div class="who">{{ $user->nama ?? $user->name }}</div>
                    <div class="role">{{ ucfirst($user->jabatan ?? 'Pekerja') }}</div>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </a>
        </aside>

        <!-- Main Content Area -->
        <main class="main">
            <div class="content">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<script>
    function tickClock() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const clockEl = document.getElementById('clock');
        if (clockEl) clockEl.textContent = `${hh}:${mm}`;
    }
    tickClock();
    setInterval(tickClock, 1000);
</script>
@stack('scripts')
</body>
</html>
