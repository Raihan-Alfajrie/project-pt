<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pertamina Admin Panel — ' . ($pageTitle ?? 'Dashboard') }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo_pertamina.webp') }}">
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
        .logo-block .mark { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; text-decoration: none; flex-shrink: 0; }
        .logo-block .mark img { width: 36px; height: 36px; object-fit: contain; display: block; border-radius: 9px; }
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
        [x-cloak] { display: none !important; }
        .tb-divider { width: 1px; height: 24px; background: rgba(255,255,255,.28); margin: 0 6px; }
        .iconbtn { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; border: 0; background: transparent; color: #fff; }
        .iconbtn:hover { background: rgba(255,255,255,.12); }
        .iconbtn svg { width: 16px; height: 16px; }
        .badge-dot { position: absolute; top: 6px; right: 6px; width: 7px; height: 7px; border-radius: 50%; background: #fff; border: 2px solid var(--red); }
        .clockpill { display: flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 600; background: rgba(255,255,255,.14); padding: 7px 12px; border-radius: 99px; }
        .clockpill svg { width: 13px; height: 13px; }

        /* ===== Notification Dropdown Styles ===== */
        .notif-wrapper { position: relative; display: inline-flex; align-items: center; }
        .notif-badge {
            position: absolute; top: -2px; right: -2px; min-width: 17px; height: 17px;
            padding: 0 4px; border-radius: 99px; background: #ffffff; color: var(--red-strong);
            font-size: 10px; font-weight: 800; line-height: 17px; text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,.3); border: 1.5px solid var(--red);
            display: flex; align-items: center; justify-content: center; pointer-events: none;
        }
        .notif-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0; width: 380px; max-width: calc(100vw - 24px);
            background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius);
            box-shadow: 0 16px 40px rgba(0,0,0,.45); z-index: 100; overflow: hidden;
            color: var(--text); display: flex; flex-direction: column; text-align: left;
        }
        .notif-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px; border-bottom: 1px solid var(--border-soft); background: var(--panel-2);
        }
        .notif-header .title-group { display: flex; align-items: center; gap: 8px; }
        .notif-header h3 { font-size: 13.5px; font-weight: 700; color: var(--text); margin: 0; }
        .notif-header .unread-pill {
            font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px;
            background: var(--red-soft); color: var(--red); border: 1px solid rgba(234,32,39,.25);
        }
        .notif-header .mark-all-btn {
            font-size: 11px; font-weight: 600; color: var(--text-dim); background: transparent;
            border: 0; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 3px 6px;
            border-radius: 6px; transition: color .15s, background .15s;
        }
        .notif-header .mark-all-btn:hover { color: var(--text); background: rgba(255,255,255,.08); }
        .notif-tabs {
            display: flex; gap: 6px; padding: 8px 14px; border-bottom: 1px solid var(--border-soft);
            background: var(--panel);
        }
        .notif-tab {
            padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600;
            cursor: pointer; border: 0; background: transparent; color: var(--text-dim);
            transition: all .15s;
        }
        .notif-tab:hover { color: var(--text); background: var(--panel-2); }
        .notif-tab.active { background: var(--red-soft); color: var(--red); font-weight: 700; }
        .notif-body {
            max-height: 380px; overflow-y: auto; display: flex; flex-direction: column;
        }
        .notif-item {
            display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px;
            border-bottom: 1px solid var(--border-soft); text-decoration: none; color: inherit;
            cursor: pointer; transition: background .15s; position: relative; text-align: left;
        }
        .notif-item:hover { background: var(--panel-2); }
        .notif-item.unread { background: rgba(234,32,39,.06); }
        .notif-item.unread:hover { background: rgba(234,32,39,.1); }
        .notif-ic-box {
            width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center;
            justify-content: center; flex: 0 0 auto;
        }
        .notif-ic-box svg { width: 17px; height: 17px; }
        .notif-ic-box.danger { background: rgba(234,32,39,.18); color: #ea2027; }
        .notif-ic-box.warning { background: rgba(245,158,11,.18); color: #f59e0b; }
        .notif-ic-box.success { background: rgba(16,185,129,.18); color: #10b981; }
        .notif-ic-box.info { background: rgba(59,130,246,.18); color: #3b82f6; }
        .notif-content { flex: 1; min-width: 0; }
        .notif-title-row { display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px; }
        .notif-title { font-size: 12.5px; font-weight: 600; color: var(--text); line-height: 1.25; }
        .notif-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); flex: 0 0 auto; }
        .notif-msg { font-size: 11px; color: var(--text-dim); line-height: 1.35; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .notif-meta { display: flex; align-items: center; gap: 8px; font-size: 10px; color: var(--text-faint); }
        .notif-meta .tag { font-weight: 600; color: var(--text-dim); background: var(--panel-3); padding: 1px 6px; border-radius: 4px; }
        .notif-empty {
            padding: 36px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
        }
        .notif-empty svg { width: 36px; height: 36px; color: var(--text-faint); }
        .notif-empty .empty-title { font-size: 13px; font-weight: 600; color: var(--text); }
        .notif-empty .empty-desc { font-size: 11px; color: var(--text-dim); max-width: 240px; margin: 0; }
        .notif-footer {
            padding: 10px 14px; text-align: center; border-top: 1px solid var(--border-soft); background: var(--panel-2);
        }
        .notif-footer a {
            font-size: 11.5px; font-weight: 700; color: var(--red); display: inline-flex; align-items: center; gap: 4px; transition: opacity .15s;
        }
        .notif-footer a:hover { opacity: .85; }

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
                <img src="{{ asset('logo_pertamina.webp') }}" alt="Logo Pertamina" width="36" height="36">
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
            <div class="notif-wrapper"
                 x-data="notificationDropdown({{ Auth::user() ? rescue(fn() => Auth::user()->unreadNotifications()->count(), 0, false) : 0 }})"
                 x-init="init()"
                 @click.outside="open = false">
                <button class="iconbtn"
                        type="button"
                        @click="toggle()"
                        aria-label="Notifikasi"
                        :aria-expanded="open"
                        id="notif-bell-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                    <template x-if="unreadCount > 0">
                        <span class="notif-badge" id="notif-badge-count" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </template>
                </button>

                <!-- Popover Dropdown Panel -->
                <div class="notif-dropdown"
                     x-show="open"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                     id="notif-dropdown-panel">

                    <!-- Header -->
                    <div class="notif-header">
                        <div class="title-group">
                            <h3>Notifikasi</h3>
                            <template x-if="unreadCount > 0">
                                <span class="unread-pill" x-text="`${unreadCount} baru`"></span>
                            </template>
                        </div>
                        <template x-if="unreadCount > 0">
                            <button type="button" class="mark-all-btn" @click="markAllRead()" title="Tandai semua sudah dibaca">
                                <svg style="width:13px; height:13px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Tandai dibaca</span>
                            </button>
                        </template>
                    </div>

                    <!-- Tabs Filter -->
                    <div class="notif-tabs">
                        <button type="button" class="notif-tab" :class="{ 'active': filter === 'all' }" @click="setFilter('all')">
                            Semua
                        </button>
                        <button type="button" class="notif-tab" :class="{ 'active': filter === 'unread' }" @click="setFilter('unread')">
                            Belum Dibaca
                            <template x-if="unreadCount > 0">
                                <span x-text="`(${unreadCount})`"></span>
                            </template>
                        </button>
                    </div>

                    <!-- Notification Body -->
                    <div class="notif-body">
                        <!-- Loading State -->
                        <div x-show="loading" style="padding: 24px; text-align: center; color: var(--text-dim); font-size: 12px;">
                            Memuat notifikasi...
                        </div>

                        <!-- Notification List -->
                        <div x-show="!loading && notifications.length > 0">
                            <template x-for="item in notifications" :key="item.id">
                                <div class="notif-item" :class="{ 'unread': !item.is_read }" @click="markAsRead(item.id, item.url)">
                                    <div class="notif-ic-box" :class="item.tipe">
                                        <template x-if="item.icon === 'tool'">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                                        </template>
                                        <template x-if="item.icon === 'alert-triangle'">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                        </template>
                                        <template x-if="item.icon === 'calendar'">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        </template>
                                        <template x-if="item.icon === 'file-spreadsheet'">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h8M8 17h8M10 9h4"/></svg>
                                        </template>
                                        <template x-if="item.icon === 'user-check'">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                                        </template>
                                        <template x-if="!['tool', 'alert-triangle', 'calendar', 'file-spreadsheet', 'user-check'].includes(item.icon)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                                        </template>
                                    </div>
                                    <div class="notif-content">
                                        <div class="notif-title-row">
                                            <span class="notif-title" x-text="item.judul"></span>
                                            <template x-if="!item.is_read">
                                                <span class="notif-dot"></span>
                                            </template>
                                        </div>
                                        <div class="notif-msg" x-text="item.pesan"></div>
                                        <div class="notif-meta">
                                            <span class="tag" x-text="item.modul_label"></span>
                                            <span>&bull;</span>
                                            <span x-text="item.waktu_display"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Empty State -->
                        <div class="notif-empty" x-show="!loading && notifications.length === 0">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                            <div class="empty-title">Tidak ada notifikasi</div>
                            <p class="empty-desc">Semua pemantauan operasional berjalan normal.</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="notif-footer">
                        <a href="{{ route('notifications.page') }}">
                            <span>Lihat Semua Notifikasi</span>
                            <svg style="width:13px; height:13px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
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

    function notificationDropdown(initialUnread = 0) {
        return {
            open: false,
            loading: false,
            filter: 'all',
            unreadCount: initialUnread,
            notifications: [],
            pollInterval: null,

            init() {
                this.fetchCount();
                this.pollInterval = setInterval(() => {
                    this.fetchCount();
                }, 30000);
            },

            async toggle() {
                this.open = !this.open;
                if (this.open) {
                    await this.fetchNotifications();
                }
            },

            async fetchCount() {
                try {
                    const res = await fetch('{{ route('notifications.unread-count') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.unreadCount = data.count || 0;
                    }
                } catch (err) {
                    // Ignore transient network errors
                }
            },

            async fetchNotifications() {
                this.loading = true;
                try {
                    const res = await fetch(`{{ route('notifications.index') }}?filter=${this.filter}&limit=8`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const json = await res.json();
                        this.notifications = json.data || [];
                        this.unreadCount = json.unread_count || 0;
                    }
                } catch (err) {
                    console.error('Gagal memuat notifikasi:', err);
                } finally {
                    this.loading = false;
                }
            },

            setFilter(f) {
                if (this.filter !== f) {
                    this.filter = f;
                    this.fetchNotifications();
                }
            },

            async markAsRead(id, targetUrl = null) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || ''
                        }
                    });
                    if (res.ok) {
                        const json = await res.json();
                        this.unreadCount = json.unread_count || 0;
                        const item = this.notifications.find(n => n.id === id);
                        if (item) item.is_read = true;
                        if (this.filter === 'unread') {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                        }
                    }
                } catch (err) {
                    console.error('Gagal menandai notifikasi dibaca:', err);
                } finally {
                    if (targetUrl && targetUrl !== '#' && targetUrl !== '') {
                        window.location.href = targetUrl;
                    }
                }
            },

            async markAllRead() {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch('{{ route('notifications.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || ''
                        }
                    });
                    if (res.ok) {
                        this.unreadCount = 0;
                        this.notifications.forEach(n => n.is_read = true);
                        if (this.filter === 'unread') {
                            this.notifications = [];
                        }
                    }
                } catch (err) {
                    console.error('Gagal menandai semua dibaca:', err);
                }
            }
        };
    }
</script>
@stack('scripts')
</body>
</html>
