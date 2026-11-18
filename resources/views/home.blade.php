<x-guest-layout title="Pertamina Rig Monitoring Zona 4 — Beranda Platform">
    <style>
        /* ===== HERO WITH BACKGROUND BANNER ===== */
        .hero-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding-bottom: 50px;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                radial-gradient(rgba(234,32,39,.22) 1.3px, transparent 1.3px),
                linear-gradient(180deg, 
                    rgba(21, 17, 19, 0.55) 0%, 
                    rgba(21, 17, 19, 0.68) 35%, 
                    rgba(21, 17, 19, 0.78) 60%, 
                    rgba(21, 17, 19, 0.88) 80%, 
                    rgba(21, 17, 19, 0.96) 92%, 
                    var(--bg) 100%),
                url('{{ asset('background_home.webp') }}');
            background-size: 26px 26px, 100% 100%, cover;
            background-position: 0 0, center center, center 20%;
            background-repeat: repeat, no-repeat, no-repeat;
        }
        :root[data-theme="light"] .hero-bg {
            background-image:
                radial-gradient(rgba(234,32,39,.18) 1.3px, transparent 1.3px),
                linear-gradient(180deg, 
                    rgba(245, 243, 241, 0.65) 0%, 
                    rgba(245, 243, 241, 0.78) 35%, 
                    rgba(245, 243, 241, 0.85) 60%, 
                    rgba(245, 243, 241, 0.92) 80%, 
                    rgba(245, 243, 241, 0.98) 92%, 
                    var(--bg) 100%),
                url('{{ asset('background_home.webp') }}');
        }
        .hero { position: relative; z-index: 1; max-width: 820px; margin: 0 auto; padding: 75px 24px 24px; text-align: center; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: var(--red); background: var(--red-soft); padding: 7px 14px; border-radius: 99px; margin-bottom: 20px; }
        .eyebrow svg { width: 13px; height: 13px; }
        .hero h1 { font-size: 50px; line-height: 1.08; font-weight: 800; }
        .hero h1 span { color: var(--red); }
        .hero p { font-size: 16px; color: var(--text-dim); line-height: 1.6; margin: 20px auto 0; max-width: 600px; }
        .hero-actions { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 30px; flex-wrap: wrap; }
        .btn-hero-primary { display: flex; align-items: center; gap: 8px; padding: 13px 22px; border-radius: 11px; border: 0; background: var(--red); color: #fff; font-size: 14.5px; font-weight: 700; cursor: pointer; font-family: inherit; box-shadow: 0 8px 24px rgba(234,32,39,.3); transition: background .2s; }
        .btn-hero-primary:hover { background: var(--red-strong); }
        .btn-hero-primary svg { width: 15px; height: 15px; }
        .btn-hero-secondary { display: flex; align-items: center; gap: 8px; padding: 13px 22px; border-radius: 11px; border: 1px solid var(--border-soft); background: var(--panel); color: var(--text); font-size: 14.5px; font-weight: 700; cursor: pointer; font-family: inherit; transition: border-color .2s; }
        .btn-hero-secondary:hover { border-color: var(--red); }
        .check-row { display: flex; align-items: center; justify-content: center; gap: 22px; margin-top: 24px; flex-wrap: wrap; }
        .check-row span { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--text-faint); }
        .check-row svg { width: 14px; height: 14px; color: var(--red); }

        /* ===== STATS STRIP ===== */
        .stats { position: relative; z-index: 1; max-width: 960px; margin: 32px auto 0; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: var(--border-soft); border: 1px solid var(--border-soft); border-radius: 16px; overflow: hidden; box-shadow: 0 16px 40px rgba(0,0,0,.35); }
        .stat { background: rgba(30, 21, 23, 0.88); backdrop-filter: blur(8px); padding: 22px 18px; text-align: center; }
        :root[data-theme="light"] .stat {
            background: rgba(255, 255, 255, 0.88);
        }
        .stat .n { font-size: 26px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; color: var(--red); }
        .stat .l { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; }

        /* ===== FLEET STATUS TICKER ===== */
        .fleet-ticker {
            position: relative;
            z-index: 1;
            max-width: 960px;
            margin: 16px auto 0;
            padding: 12px 18px;
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        .ticker-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text);
            font-family: 'Space Grotesk', sans-serif;
        }
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%; background: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,.3);
            animation: pulseGreen 2s infinite;
        }
        @keyframes pulseGreen {
            0%, 100% { box-shadow: 0 0 0 3px rgba(16,185,129,.3); }
            50% { box-shadow: 0 0 0 6px rgba(16,185,129,.1); }
        }
        .ticker-items {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .ticker-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            padding: 4px 10px;
            border-radius: 99px;
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            color: var(--text-dim);
        }
        .ticker-badge .dot {
            width: 6px; height: 6px; border-radius: 50%;
        }
        .ticker-badge.aktif .dot { background: #10b981; }
        .ticker-badge.standby .dot { background: #0ea5e9; }
        .ticker-badge.perbaikan .dot { background: #f59e0b; }
        .ticker-badge.bermasalah .dot { background: #ef4444; }
        .ticker-badge b { color: var(--text); }
        .ticker-badge.total {
            background: var(--red-soft);
            border-color: rgba(234,32,39,.3);
            color: var(--red);
        }
        .ticker-badge.total b { color: var(--red); }

        /* ===== GANTT SCHEDULE TIMELINE ===== */
        .gantt-section {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px 48px;
        }
        .search-gantt {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 12px;
            transition: all .2s;
        }
        .search-gantt:focus-within {
            border-color: var(--red);
        }
        .search-gantt svg { width: 14px; height: 14px; color: var(--text-faint); flex-shrink: 0; }
        .search-gantt input {
            background: transparent; border: 0; outline: 0;
            color: var(--text); font-size: 12.5px; font-family: inherit; width: 180px;
        }
        .select-gantt {
            background: var(--panel); border: 1px solid var(--border);
            color: var(--text-dim); font-size: 12px; padding: 8px 12px;
            border-radius: 10px; font-family: inherit; cursor: pointer; outline: 0;
        }
        .select-gantt:focus { border-color: var(--red); }

        .gantt-wrap {
            background: var(--panel); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden; box-shadow: 0 16px 40px rgba(0,0,0,.3);
        }
        .gantt-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .gantt { display: grid; min-width: max-content; }
        .g-head { display: contents; }
        .g-head > div {
            position: sticky; top: 0; z-index: 5; background: var(--panel-2);
            border-bottom: 1px solid var(--border); font-size: 10.5px; font-weight: 700;
            color: var(--text-dim); padding: 10px 8px; text-align: center;
        }
        .g-head .corner { position: sticky; left: 0; z-index: 6; text-align: left; padding-left: 14px; background: var(--panel-2); }
        .g-head .now { color: var(--red); }
        .g-head .plancol { background: var(--panel-3); }
        .g-row { display: contents; }
        .g-rig {
            position: sticky; left: 0; background: var(--panel); z-index: 3;
            border-bottom: 1px solid var(--border-soft); border-right: 1px solid var(--border);
            padding: 10px 12px; display: flex; flex-direction: column; justify-content: center; gap: 1px;
        }
        .g-rig .name { font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 165px; color: var(--text); }
        .g-rig .num { font-size: 10px; color: var(--text-faint); }
        .g-cell { border-bottom: 1px solid var(--border-soft); border-right: 1px solid var(--border-soft); position: relative; min-height: 52px; }
        .g-cell.now-col { background: rgba(234,32,39,.09); border-right: 1px solid rgba(234,32,39,.4); }
        .g-plan {
            border-bottom: 1px solid var(--border-soft); background: var(--panel-3);
            display: flex; align-items: center; justify-content: center; font-size: 11.5px;
            font-weight: 700; font-family: 'IBM Plex Mono', monospace;
        }
        .g-plan.prognose { color: #f0a93b; }
        .g-plan.plan { color: var(--text-dim); }

        .block {
            position: absolute; top: 8px; bottom: 8px; border-radius: 7px;
            padding: 0 8px; display: flex; align-items: center; font-size: 10px;
            font-weight: 700; color: #221417; overflow: hidden; white-space: nowrap;
            cursor: pointer; border: 1px solid rgba(0,0,0,.15);
            transition: transform .1s, box-shadow .1s;
        }
        .block:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.4); z-index: 4; }
        .block.dark { color: #f6f5f4; }
        .legend-hint {
            display: flex; align-items: center; gap: 8px; font-size: 11.5px;
            color: var(--text-faint); padding: 12px 18px; border-top: 1px solid var(--border-soft);
            background: var(--panel-2);
        }
        .legend-hint svg { width: 14px; height: 14px; color: var(--red); flex-shrink: 0; }

        /* Tooltip */
        .tt {
            position: fixed; z-index: 60; background: var(--panel);
            border: 1px solid var(--border); border-radius: 10px; padding: 12px 14px;
            font-size: 12px; color: var(--text); max-width: 280px;
            box-shadow: 0 10px 30px rgba(0,0,0,.5); display: none; pointer-events: none;
        }
        .tt.show { display: block; }
        .tt .tt-t { font-weight: 700; font-size: 12.5px; margin-bottom: 4px; color: var(--text); }
        .tt .tt-s { color: var(--text-dim); font-size: 11px; margin-bottom: 6px; }
        .tt .tt-n { color: var(--text-dim); line-height: 1.5; font-size: 11.5px; }
        .tt .tt-tag {
            display: inline-block; font-size: 9.5px; font-weight: 700;
            padding: 2px 8px; border-radius: 99px; background: var(--panel-2);
            color: var(--text-dim); border: 1px solid var(--border-soft);
        }

        /* ===== FEATURES 4 MENU INTI ===== */
        .section { position: relative; z-index: 1; max-width: 1080px; margin: 0 auto; padding: 48px 24px 80px; }
        .section-head { text-align: center; margin-bottom: 36px; }
        .section-head h2 { font-size: 28px; }
        .section-head p { font-size: 14px; color: var(--text-faint); margin-top: 10px; }
        .feat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .feat-card { background: var(--panel); border: 1px solid var(--border-soft); border-radius: 16px; padding: 24px 20px; transition: border-color .2s; }
        .feat-card:hover { border-color: var(--border); }
        .feat-card .ic { width: 42px; height: 42px; border-radius: 11px; background: var(--red-soft); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .feat-card .ic svg { width: 20px; height: 20px; color: var(--red); }
        .feat-card h3 { font-size: 15px; margin-bottom: 8px; }
        .feat-card p { font-size: 12.5px; color: var(--text-faint); line-height: 1.55; margin: 0; }

        @media (max-width: 900px) {
            .check-row { display: none; }
            .hero h1 { font-size: 36px; }
            .stats, .feat-grid { grid-template-columns: repeat(2, 1fr); }
            .search-gantt input { width: 130px; }
        }
    </style>

    <div class="hero-wrap">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="hero">
            <div class="eyebrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12l4 4L19 6"/></svg>
                Internal Monitoring Platform
            </div>
            <h1>Satu Dashboard untuk<br>Monitoring <span>Rig Zona 4</span></h1>
            <p>Pantau status alat, lokasi lapangan, dan jadwal pemboran secara real-time dalam satu platform — dari kesiapan operasional sampai progres tajak sumur.</p>
            <div class="hero-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero-primary" id="btnHeroLogin">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                        Masuk ke Dashboard
                    </a>
                @endauth
                <a href="#jadwal-publik" class="btn-hero-secondary">Lihat Jadwal Operasi</a>
                <a href="#fitur" class="btn-hero-secondary">Lihat Fitur</a>
            </div>
            <div class="check-row">
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>{{ $totalRig }} rig termonitor real-time</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>Data terpusat, satu sumber kebenaran</span>
            </div>
        </div>

        <!-- 1. Live Dynamic Stats Strip -->
        <div class="stats">
            <div class="stat"><div class="n">{{ $totalRig }}</div><div class="l">Rig Termonitor</div></div>
            <div class="stat"><div class="n">{{ $totalLokasi }}</div><div class="l">Kode Lapangan</div></div>
            <div class="stat"><div class="n">{{ $totalJadwalAktif }}</div><div class="l">Item Jadwal Aktif</div></div>
            <div class="stat"><div class="n">{{ $totalAlat }}</div><div class="l">Alat &amp; Rig Terdata</div></div>
        </div>

        <!-- 2. Public Fleet Status Ticker -->
        <div class="fleet-ticker">
            <div class="ticker-label">
                <span class="pulse-dot"></span>
                <span>Kesiapan Armada Rig Zona 4:</span>
            </div>
            <div class="ticker-items">
                <div class="ticker-badge aktif">
                    <span class="dot"></span>
                    <span>Aktif Beroperasi: <b>{{ $rigAktif }}</b> Rig</span>
                </div>
                <div class="ticker-badge standby">
                    <span class="dot"></span>
                    <span>Standby / Siaga: <b>{{ $rigStandby }}</b> Rig</span>
                </div>
                @if ($rigPerbaikan > 0)
                <div class="ticker-badge perbaikan">
                    <span class="dot"></span>
                    <span>Perbaikan: <b>{{ $rigPerbaikan }}</b> Rig</span>
                </div>
                @endif
                @if ($rigBermasalah > 0)
                <div class="ticker-badge bermasalah">
                    <span class="dot"></span>
                    <span>Kendala: <b>{{ $rigBermasalah }}</b> Rig</span>
                </div>
                @endif
                <div class="ticker-badge total">
                    <span>Total Armada: <b>{{ $totalRig }}</b> Rig</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Jadwal & Progres Penuh (Full Gantt Chart Timeline) -->
    <div class="gantt-section" id="jadwal-publik">
        <div class="sec-head" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:20px; text-align:left;">
            <div>
                <h2 style="font-size:24px; font-weight:800; font-family:'Space Grotesk',sans-serif; margin:0; color:var(--text);">Jadwal &amp; Progres Operasi Rig</h2>
                <div style="font-size:12.5px; color:var(--text-faint); margin-top:4px;">
                    Klik/hover blok untuk detail sumur &bull; scroll horizontal untuk melihat timeline Jun 2026 – Des 2027
                </div>
            </div>
            <div class="toolbar" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="search-gantt">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="publicRigSearch" type="text" placeholder="Cari rig atau sumur…">
                </div>
                <select class="select-gantt" id="publicRigStatusFilter">
                    <option value="">Semua status rig</option>
                    <option value="aktif">Aktif</option>
                    <option value="standby">Standby</option>
                    <option value="bermasalah">Bermasalah</option>
                </select>
            </div>
        </div>

        <div class="gantt-wrap">
            <div class="gantt-scroll">
                <div class="gantt" id="publicGanttGrid" style="grid-template-columns: 190px repeat({{ count($months) }}, 92px) 74px 74px;">
                    <!-- Header -->
                    <div class="g-head">
                        <div class="corner">Rig</div>
                        @foreach ($months as $m)
                            <div class="{{ $m['is_now'] ? 'now' : '' }}">{{ $m['label'] }}</div>
                        @endforeach
                        <div class="plancol">Plan</div>
                        <div class="plancol">Prognose</div>
                    </div>

                    <!-- Rows from DB -->
                    @foreach ($ganttRigs as $rig)
                        <div class="g-row"
                             data-rig-name="{{ strtolower($rig['nama']) }}"
                             data-rig-status="{{ strtolower($rig['status']) }}"
                             data-rig-wells="{{ strtolower(implode(' ', array_column($rig['blocks']->toArray(), 'well'))) }}">
                            <div class="g-rig">
                                <div class="name" title="{{ $rig['nama'] }}">{{ $rig['nama'] }}</div>
                                <div class="num">Rig {{ $rig['no'] }}</div>
                            </div>

                            @foreach ($months as $i => $m)
                                <div class="g-cell {{ $m['is_now'] ? 'now-col' : '' }}" data-col-index="{{ $i }}">
                                    @foreach ($rig['blocks'] as $block)
                                        @if ($block['startIndex'] == $i)
                                            <div class="block {{ $block['isDark'] ? 'dark' : '' }}"
                                                 style="width: calc({{ $block['duration'] * 92 - 6 }}px); left: 3px; background-color: {{ $block['warna'] }}; {{ $block['gaya'] === 'dashed' ? 'border: 2px dashed rgba(255,255,255,0.4);' : '' }} {{ $block['gaya'] === 'border' ? 'border: 2px solid var(--red);' : '' }}"
                                                 data-title="{{ $block['label'] }}"
                                                 data-sub="{{ $rig['nama'] }} &bull; {{ $block['well'] }}"
                                                 data-note="{{ $block['catatan'] }}"
                                                 data-tag="{{ ucfirst($block['status']) }}"
                                                 data-kategori="{{ $block['kategoriLabel'] }}"
                                                 data-lokasi="{{ $block['lokasiNama'] }}"
                                                 onmouseenter="showHomeTip(event, this)"
                                                 onmousemove="moveHomeTip(event)"
                                                 onmouseleave="hideHomeTip()">
                                                <span>{{ $block['label'] }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="g-plan plan">{{ $rig['plan'] }}</div>
                            <div class="g-plan prognose">{{ $rig['prognose'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="legend-hint">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5V4.6c0-.9.7-1.6 1.6-1.6H16l4 4v12.5a1.6 1.6 0 01-1.6 1.6H5.6A1.6 1.6 0 014 19.5z"/></svg>
                <span>Transparansi alur kerja tajak sumur WK Zona 4. Untuk manajemen perubahan jadwal dan integrasi data, silakan masuk ke dashboard internal.</span>
            </div>
        </div>
    </div>

    <!-- Tooltip Element -->
    <div id="homeTip" class="tt">
        <div class="tt-t" id="homeTtTitle"></div>
        <div class="tt-s" id="homeTtSub"></div>
        <div class="tt-n" id="homeTtNote"></div>
        <div style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;">
            <span class="tt-tag" id="homeTtTag"></span>
            <span class="tt-tag" id="homeTtKategori"></span>
        </div>
    </div>

    <!-- 4. Fitur Empat Menu Inti -->
    <div class="section" id="fitur">
        <div class="section-head">
            <h2>Empat menu inti, satu alur kerja</h2>
            <p>Dirancang untuk tim operasional WK Zona 4 — dari pantauan harian sampai pelacakan progres per sumur.</p>
        </div>
        <div class="feat-grid">
            <div class="feat-card">
                <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg></div>
                <h3>Dashboard</h3>
                <p>Ringkasan status rig, realisasi tajak &amp; completed, serta progres per rig dalam satu tampilan.</p>
            </div>
            <div class="feat-card">
                <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg></div>
                <h3>Alat</h3>
                <p>Kelola data rig &amp; alat pendukung lengkap — merek, model, nomor seri, status, dan riwayatnya.</p>
            </div>
            <div class="feat-card">
                <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg></div>
                <h3>Lokasi</h3>
                <p>Peta kode lapangan operasional beserta detail sumur dan rig yang beroperasi di tiap titik.</p>
            </div>
            <div class="feat-card">
                <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg></div>
                <h3>Jadwal</h3>
                <p>Gantt jadwal &amp; progres per pekerjaan sumur, dengan status Selesai, Progres, dan Kendala.</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const publicRigSearch = document.getElementById('publicRigSearch');
        const publicRigStatusFilter = document.getElementById('publicRigStatusFilter');

        function filterPublicGantt() {
            const query = (publicRigSearch ? publicRigSearch.value : '').toLowerCase().trim();
            const status = (publicRigStatusFilter ? publicRigStatusFilter.value : '').toLowerCase().trim();
            const rows = document.querySelectorAll('#publicGanttGrid .g-row');

            rows.forEach(row => {
                const name = row.getAttribute('data-rig-name') || '';
                const wells = row.getAttribute('data-rig-wells') || '';
                const rigStatus = row.getAttribute('data-rig-status') || '';

                const matchQuery = !query || name.includes(query) || wells.includes(query);
                const matchStatus = !status || rigStatus === status;

                row.style.display = (matchQuery && matchStatus) ? 'contents' : 'none';
            });
        }

        if (publicRigSearch) publicRigSearch.addEventListener('input', filterPublicGantt);
        if (publicRigStatusFilter) publicRigStatusFilter.addEventListener('change', filterPublicGantt);

        const homeTip = document.getElementById('homeTip');
        const homeTtTitle = document.getElementById('homeTtTitle');
        const homeTtSub = document.getElementById('homeTtSub');
        const homeTtNote = document.getElementById('homeTtNote');
        const homeTtTag = document.getElementById('homeTtTag');
        const homeTtKategori = document.getElementById('homeTtKategori');

        function showHomeTip(e, el) {
            if (!homeTip) return;
            homeTtTitle.textContent = el.getAttribute('data-title') || '';
            homeTtSub.textContent = el.getAttribute('data-sub') || '';
            homeTtNote.textContent = el.getAttribute('data-note') || '';
            homeTtTag.textContent = 'Status: ' + (el.getAttribute('data-tag') || 'Rencana');
            homeTtKategori.textContent = 'Kategori: ' + (el.getAttribute('data-kategori') || 'Umum');
            homeTip.classList.add('show');
            moveHomeTip(e);
        }

        function moveHomeTip(e) {
            if (!homeTip) return;
            const x = Math.min(e.clientX + 14, window.innerWidth - 290);
            const y = Math.min(e.clientY + 14, window.innerHeight - 150);
            homeTip.style.left = x + 'px';
            homeTip.style.top = y + 'px';
        }

        function hideHomeTip() {
            if (homeTip) homeTip.classList.remove('show');
        }
    </script>
    @endpush
</x-guest-layout>
