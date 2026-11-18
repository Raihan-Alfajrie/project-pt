<x-guest-layout title="Tentang Platform — Pertamina Rig Monitoring Zona 4">
    @push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        /* ===== LEAFLET MAP IN ABOUT ===== */
        .about-map-box {
            margin: 28px 0 32px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(0,0,0,.3);
        }
        .about-map-header {
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-soft);
            background: var(--panel-2);
            flex-wrap: wrap;
            gap: 12px;
        }
        .about-map-header h3 {
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: var(--text);
            font-family: 'Space Grotesk', sans-serif;
        }
        .about-map-header svg { width: 18px; height: 18px; color: var(--red); }
        .about-map-legend {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 12px;
            color: var(--text-dim);
            flex-wrap: wrap;
        }
        .legend-dot {
            width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px;
        }
        .legend-dot.red { background: #ea2027; }
        .legend-dot.green { background: #10b981; }
        .legend-dot.amber { background: #f59e0b; }
        #leafletAboutMap {
            height: 420px;
            width: 100%;
            background: #151113;
            position: relative;
            z-index: 1;
        }
        :root[data-theme="light"] #leafletAboutMap {
            background: #e5e0dc;
        }
        .leaflet-popup-content-wrapper {
            background: var(--panel) !important;
            color: var(--text) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            box-shadow: 0 12px 30px rgba(0,0,0,.5) !important;
            padding: 0 !important;
        }
        .leaflet-popup-tip {
            background: var(--panel) !important;
            border: 1px solid var(--border) !important;
        }
        .leaflet-popup-content {
            margin: 14px 16px !important;
            line-height: 1.5 !important;
            font-family: 'Inter', system-ui, sans-serif !important;
        }
        .popup-field-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .popup-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 99px;
            background: var(--red-soft);
            color: var(--red);
        }
        .popup-field-desc {
            font-size: 12px;
            color: var(--text-dim);
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .popup-field-meta {
            font-size: 11px;
            color: var(--text-faint);
            display: flex;
            flex-direction: column;
            gap: 3px;
            border-top: 1px dashed var(--border-soft);
            padding-top: 6px;
        }

        /* ===== HELPDESK CONTACT FORM ===== */
        .helpdesk-grid {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            gap: 32px;
            margin-top: 28px;
        }
        @media (max-width: 860px) {
            .helpdesk-grid { grid-template-columns: 1fr; }
        }
        .helpdesk-info-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .helpdesk-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .helpdesk-ic {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--red-soft); color: var(--red);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .helpdesk-ic svg { width: 18px; height: 18px; }
        .helpdesk-text h5 { font-size: 13.5px; font-weight: 700; margin: 0 0 3px 0; color: var(--text); }
        .helpdesk-text p { font-size: 12.5px; color: var(--text-dim); margin: 0; line-height: 1.5; }
        .helpdesk-form-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 26px;
        }
        .helpdesk-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .helpdesk-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        @media (max-width: 560px) {
            .helpdesk-row { grid-template-columns: 1fr; }
        }
    </style>
    @endpush

    <style>
        /* ===== ABOUT PAGE STYLING (IDE B: SINGLE-PAGE CLEAN SCROLLING) ===== */
        .about-wrap {
            position: relative;
            z-index: 1;
            max-width: 1140px;
            margin: 0 auto;
            padding: 48px 24px 80px;
        }

        /* Hero Banner with Background */
        .about-hero-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding-bottom: 60px;
        }
        .about-hero-bg {
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
                url('{{ asset('background_about.jpeg') }}');
            background-size: 26px 26px, 100% 100%, cover;
            background-position: 0 0, center center, center 20%;
            background-repeat: repeat, no-repeat, no-repeat;
        }
        :root[data-theme="light"] .about-hero-bg {
            background-image:
                radial-gradient(rgba(234,32,39,.18) 1.3px, transparent 1.3px),
                linear-gradient(180deg, 
                    rgba(245, 243, 241, 0.65) 0%, 
                    rgba(245, 243, 241, 0.78) 35%, 
                    rgba(245, 243, 241, 0.85) 60%, 
                    rgba(245, 243, 241, 0.92) 80%, 
                    rgba(245, 243, 241, 0.98) 92%, 
                    var(--bg) 100%),
                url('{{ asset('background_about.jpeg') }}');
        }
        .about-hero-inner {
            position: relative;
            z-index: 1;
            max-width: 1140px;
            margin: 0 auto;
            padding: 50px 24px 0;
        }
        .about-hero {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--red);
            background: var(--red-soft);
            padding: 7px 15px;
            border-radius: 99px;
            margin-bottom: 22px;
            border: 1px solid rgba(234,32,39,.2);
        }
        .eyebrow svg { width: 14px; height: 14px; }
        .about-hero h1 {
            font-size: 46px;
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .about-hero h1 span { color: var(--red); }
        .about-hero p {
            font-size: 16px;
            color: var(--text-dim);
            line-height: 1.7;
            margin: 22px auto 0;
            max-width: 720px;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 32px;
            flex-wrap: wrap;
        }
        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 22px;
            height: 44px;
            box-sizing: border-box;
            border-radius: 11px;
            border: 0;
            background: var(--red);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
            text-decoration: none;
            box-shadow: 0 8px 24px rgba(234,32,39,.3);
            transition: background .2s, transform .15s;
        }
        .btn-hero-primary:hover {
            background: var(--red-strong);
            transform: translateY(-1px);
        }
        .btn-hero-primary svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 20px;
            height: 44px;
            box-sizing: border-box;
            border-radius: 11px;
            border: 1px solid var(--border-soft);
            background: var(--panel);
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
            text-decoration: none;
            transition: border-color .2s, transform .15s;
        }
        .btn-hero-secondary:hover {
            border-color: var(--red);
            transform: translateY(-1px);
        }
        .btn-hero-secondary svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* Stats Strip */
        .stats {
            margin: 48px auto 0;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border-soft);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(0,0,0,.35);
        }
        .stat {
            background: rgba(30, 21, 23, 0.88);
            backdrop-filter: blur(8px);
            padding: 24px 20px;
            text-align: center;
            transition: background .2s;
        }
        :root[data-theme="light"] .stat {
            background: rgba(255, 255, 255, 0.88);
        }
        .stat:hover { background: rgba(37, 26, 29, 0.95); }
        .stat .n {
            font-size: 28px;
            font-weight: 800;
            font-family: 'Space Grotesk', sans-serif;
            color: var(--red);
            line-height: 1.1;
        }
        .stat .l {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            margin-top: 6px;
        }
        .stat .sub {
            font-size: 10.5px;
            color: var(--text-faint);
            margin-top: 3px;
        }

        /* Section Global */
        .section-block {
            margin-top: 88px;
            scroll-margin-top: 96px;
        }
        .section-head {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 36px;
        }
        .section-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--red);
            margin-bottom: 8px;
        }
        .section-head h2 {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
        }
        .section-head p {
            font-size: 14.5px;
            color: var(--text-dim);
            line-height: 1.6;
            margin-top: 10px;
        }

        /* 3 Pillars */
        .pillars-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .pillar-card {
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            transition: border-color .2s, transform .2s;
        }
        .pillar-card:hover {
            border-color: var(--border);
            transform: translateY(-2px);
        }
        .pillar-ic {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--red-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .pillar-ic svg { width: 22px; height: 22px; color: var(--red); }
        .pillar-card h3 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .pillar-card p {
            font-size: 13px;
            color: var(--text-dim);
            line-height: 1.6;
            margin: 0 0 16px;
            flex-grow: 1;
        }
        .pillar-bullets {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            border-top: 1px solid var(--border-soft);
            padding-top: 14px;
        }
        .pillar-bullets li {
            font-size: 11.5px;
            color: var(--text-faint);
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .pillar-bullets li svg { width: 13px; height: 13px; color: var(--red); flex-shrink: 0; }

        /* Field Cards (Wilayah Kerja) */
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .field-card {
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 20px 18px;
            transition: all .2s;
            position: relative;
            overflow: hidden;
        }
        .field-card:hover {
            border-color: var(--border);
            background: var(--panel-2);
        }
        .field-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 3px;
            background: var(--red);
            opacity: .6;
        }
        .field-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .field-top h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
        }
        .field-badge {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--panel-2);
            color: var(--text-dim);
            border: 1px solid var(--border-soft);
        }
        .field-card p {
            font-size: 12px;
            color: var(--text-faint);
            line-height: 1.55;
            margin: 0 0 12px;
        }
        .field-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--red);
            font-weight: 600;
        }
        .field-meta svg { width: 12px; height: 12px; }

        /* Rig Types Banner */
        .rig-types-box {
            margin-top: 24px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .rig-type-item {
            display: flex;
            gap: 16px;
        }
        .rig-type-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--red-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .rig-type-icon svg { width: 20px; height: 20px; color: var(--red); }
        .rig-type-item h5 {
            margin: 0 0 6px;
            font-size: 14.5px;
            font-weight: 700;
        }
        .rig-type-item p {
            margin: 0;
            font-size: 12px;
            color: var(--text-dim);
            line-height: 1.5;
        }

        /* 6 Capabilities */
        .caps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .cap-card {
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 24px 20px;
            transition: all .2s;
        }
        .cap-card:hover {
            border-color: var(--border);
            transform: translateY(-2px);
        }
        .cap-ic {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: var(--red-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .cap-ic svg { width: 20px; height: 20px; color: var(--red); }
        .cap-card h3 {
            font-size: 15.5px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .cap-card p {
            font-size: 12.5px;
            color: var(--text-faint);
            line-height: 1.55;
            margin: 0;
        }

        /* Governance / Standards */
        .gov-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .gov-card {
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            padding: 24px 20px;
        }
        .gov-card h4 {
            font-size: 14.5px;
            font-weight: 700;
            margin: 0 0 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .gov-card h4 svg { width: 16px; height: 16px; color: var(--red); }
        .gov-card p {
            font-size: 12px;
            color: var(--text-dim);
            line-height: 1.6;
            margin: 0;
        }

        /* CTA */
        .about-cta {
            margin-top: 88px;
            background: linear-gradient(145deg, var(--panel), var(--panel-2));
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 32px;
            text-align: center;
            box-shadow: 0 16px 40px rgba(0,0,0,.35);
            position: relative;
            overflow: hidden;
        }
        .about-cta h2 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
        }
        .about-cta p {
            font-size: 15px;
            color: var(--text-dim);
            max-width: 580px;
            margin: 0 auto 28px;
            line-height: 1.6;
        }
        .about-cta .cta-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .cta-note {
            margin-top: 22px;
            font-size: 11.5px;
            color: var(--text-faint);
        }

        /* Responsive */
        @media (max-width: 960px) {
            .about-hero h1 { font-size: 36px; }
            .stats { grid-template-columns: repeat(2, 1fr); }
            .pillars-grid, .fields-grid, .caps-grid, .gov-grid { grid-template-columns: repeat(2, 1fr); }
            .rig-types-box { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .about-hero h1 { font-size: 28px; }
            .about-wrap { padding: 20px 16px 60px; }
            .stats { grid-template-columns: 1fr; }
            .pillars-grid, .fields-grid, .caps-grid, .gov-grid { grid-template-columns: 1fr; }
            .about-cta { padding: 36px 20px; }
            .about-cta h2 { font-size: 24px; }
        }
    </style>

    <!-- ===== HERO SECTION WITH BANNER BACKGROUND ===== -->
    <div class="about-hero-wrap">
        <div class="about-hero-bg" aria-hidden="true"></div>
        <div class="about-hero-inner">
            <div class="about-hero">
                <div class="eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    Wilayah Kerja Zona 4 — Regional 1 Sumatera
                </div>
                <h1>Platform Monitoring Terintegrasi<br><span>Rig &amp; Aset Pemboran</span></h1>
                <p>
                    Sistem informasi operasional terpusat satu pintu (<em>single source of truth</em>) yang dirancang untuk mengawal keandalan armada rig pemboran, peralatan pendukung, timeline pergerakan jadwal tajak, dan pemetaan lapangan migas di WK Zona 4 secara akurat, real-time, dan transparan.
                </p>
                <div class="hero-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                            Masuk ke Platform
                        </a>
                    @endauth
                    <a href="#lapangan" class="btn-hero-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
                        Lihat Wilayah Lapangan
                    </a>
                    <a href="#kapabilitas" class="btn-hero-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                        Kapabilitas Sistem
                    </a>
                </div>
            </div>

            <!-- ===== STATS STRIP ===== -->
            <div class="stats">
                <div class="stat">
                    <div class="n">16</div>
                    <div class="l">Rig Termonitor</div>
                    <div class="sub">Armada Drilling &amp; Workover</div>
                </div>
                <div class="stat">
                    <div class="n">14+</div>
                    <div class="l">Struktur Lapangan</div>
                    <div class="sub">Tersebar di Sumatera Bagian Selatan</div>
                </div>
                <div class="stat">
                    <div class="n">100%</div>
                    <div class="l">Tracking Terintegrasi</div>
                    <div class="sub">Jadwal Tajak, Moving &amp; Kesiapan</div>
                </div>
                <div class="stat">
                    <div class="n">24/7</div>
                    <div class="l">Sistem Peringatan Dini</div>
                    <div class="sub">Audit Log &amp; Notifikasi Instan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="about-wrap">
        <!-- ===== SECTION 1: 3 PILAR NILAI UTAMA ===== -->
        <div class="section-block" id="pilar">
            <div class="section-head">
                <span class="section-tag">Nilai &amp; Filosofi</span>
                <h2>Tiga Pilar Keunggulan Operasional</h2>
                <p>Mengintegrasikan data teknis dari lapangan langsung ke meja pengambil keputusan untuk meminimalkan downtime dan menjaga target lifting migas nasional.</p>
            </div>
            <div class="pillars-grid">
                <div class="pillar-card">
                    <div class="pillar-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    </div>
                    <h3>Keandalan &amp; Integritas Data</h3>
                    <p>Menghilangkan fragmentasi dan inkonsistensi dari pelaporan manual berbasis spreadsheet terpisah. Seluruh data rig, spesifikasi teknis, dan pergerakan tersimpan dalam basis data terpusat.</p>
                    <ul class="pillar-bullets">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Satu sumber kebenaran (single source of truth)</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Validasi otomatis ketat saat input dan impor</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Perlindungan terhadap injeksi rumus formula</li>
                    </ul>
                </div>
                <div class="pillar-card">
                    <div class="pillar-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3>Kecepatan Keputusan (Zero Latency)</h3>
                    <p>Visibilitas langsung status alat (Aktif, Standby, Bermasalah) dan jadwal tajak sumur memungkinkan pimpinan dan insinyur lapangan mendeteksi kendala serta mengambil mitigasi seketika.</p>
                    <ul class="pillar-bullets">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Dashboard eksekutif status operasional</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Gantt chart jadwal pergerakan rig (moving)</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Prediksi ketersediaan slot alat pendukung</li>
                    </ul>
                </div>
                <div class="pillar-card">
                    <div class="pillar-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Keselamatan &amp; Kepatuhan HSSE</h3>
                    <p>Mendukung kepatuhan standar Health, Safety, Security, and Environment Pertamina lewat pencatatan mutasi yang akuntabel, kontrol hak akses berbasis peran, dan peringatan darurat seketika.</p>
                    <ul class="pillar-bullets">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Otorisasi ketat Administrator vs Pekerja (RBAC)</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Audit log rekaman mutasi data secara transparan</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Notifikasi pemicu anomali status alat seketika</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ===== SECTION 2: WILAYAH KERJA ZONA 4 ===== -->
        <div class="section-block" id="lapangan">
            <div class="section-head">
                <span class="section-tag">Cakupan Geografis</span>
                <h2>Wilayah Lapangan Produksi WK Zona 4</h2>
                <p>Mengelola sebaran lapangan minyak dan gas bumi di Sumatera Bagian Selatan dengan mobilitas rig yang tinggi.</p>
            </div>

            <!-- Peta Interaktif Leaflet Lapangan -->
            <div class="about-map-box">
                <div class="about-map-header">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <span>Peta Interaktif Sebaran Wilayah Kerja Zona 4</span>
                    </h3>
                    <div class="about-map-legend">
                        <span><span class="legend-dot red"></span>Pusat Komando &amp; Workshop</span>
                        <span><span class="legend-dot green"></span>Klaster Produksi Aktif</span>
                        <span><span class="legend-dot amber"></span>Lapangan Satelit</span>
                    </div>
                </div>
                <div id="leafletAboutMap"></div>
            </div>

            <div class="fields-grid">
                <div class="field-card">
                    <div class="field-top">
                        <h4>Prabumulih Field</h4>
                        <span class="field-badge">Pusat Operasi</span>
                    </div>
                    <p>Pusat komando operasional, workshop pemeliharaan alat berat, serta koordinasi rig pemboran sumur eksploitasi dan eksplorasi utama.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Konsentrasi Rig &amp; Workshop
                    </div>
                </div>

                <div class="field-card">
                    <div class="field-top">
                        <h4>Limau Field</h4>
                        <span class="field-badge">Klaster Minyak</span>
                    </div>
                    <p>Salah satu lapangan minyak produktif dengan intensitas perawatan sumur rutin menggunakan armada rig workover &amp; well service.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Rotasi Workover Aktif
                    </div>
                </div>

                <div class="field-card">
                    <div class="field-top">
                        <h4>Pendopo Field</h4>
                        <span class="field-badge">Reaktivasi &amp; Gas</span>
                    </div>
                    <p>Wilayah kerja strategis dengan reaktivasi sumur-sumur tua dan program pengeboran sumur baru terencana untuk menjaga tekanan reservoir.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Jadwal Pemboran Terencana
                    </div>
                </div>

                <div class="field-card">
                    <div class="field-top">
                        <h4>Adera Field</h4>
                        <span class="field-badge">Operasi Tajak</span>
                    </div>
                    <p>Operasi pemboran terpadu dengan pemantauan ketat terhadap jadwal tajak (spud in) dan pergerakan rig (moving) antar-pad sumur.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Moving &amp; Rigging Up
                    </div>
                </div>

                <div class="field-card">
                    <div class="field-top">
                        <h4>Ramba Field</h4>
                        <span class="field-badge">Area Strategis</span>
                    </div>
                    <p>Area operasi dengan tantangan logistik jalan masuk khusus, memerlukan akurasi koordinat pemetaan geografis titik rig yang presisi.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Pemetaan Koordinat GIS
                    </div>
                </div>

                <div class="field-card">
                    <div class="field-top">
                        <h4>Ogan Komering Field</h4>
                        <span class="field-badge">Klaster Migas</span>
                    </div>
                    <p>Lapangan minyak dan gas bumi dengan rotasi rig standby serta pengawasan performa alat berat pendukung kelancaran produksi.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Monitoring Kesiapan Alat
                    </div>
                </div>

                <div class="field-card">
                    <div class="field-top">
                        <h4>Raja / Tempirai Field</h4>
                        <span class="field-badge">Lapangan Satelit</span>
                    </div>
                    <p>Klaster sumur penunjang dengan program optimalisasi pengangkatan buatan (artificial lift) dan jadwal servis berkala.</p>
                    <div class="field-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                        Perawatan Sumur Terjadwal
                    </div>
                </div>
            </div>

            <!-- Rig Categories Banner -->
            <div class="rig-types-box">
                <div class="rig-type-item">
                    <div class="rig-type-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg>
                    </div>
                    <div>
                        <h5>Rig Drilling (Pemboran)</h5>
                        <p>Armada rig berkapasitas daya tinggi (550 HP hingga 1500 HP) yang didedikasikan untuk pengeboran sumur baru menuju lapisan reservoir hidrokarbon target.</p>
                    </div>
                </div>
                <div class="rig-type-item">
                    <div class="rig-type-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div>
                        <h5>Rig Workover &amp; Well Service</h5>
                        <p>Armada dengan mobilitas tinggi untuk perbaikan sumur aktif, stimulasi lapisan, penggantian pompa ESP/sucker rod, dan pencegahan degradasi produksi.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SECTION 3: 6 KAPABILITAS SISTEM ===== -->
        <div class="section-block" id="kapabilitas">
            <div class="section-head">
                <span class="section-tag">Fitur &amp; Modularitas</span>
                <h2>Enam Pilar Kapabilitas Sistem Terpadu</h2>
                <p>Ekosistem aplikasi lengkap yang menghubungkan aktivitas kru lapangan dengan tim perencana di kantor pusat.</p>
            </div>
            <div class="caps-grid">
                <div class="cap-card">
                    <div class="cap-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                    </div>
                    <h3>Dashboard Eksekutif</h3>
                    <p>Menampilkan agregasi real-time status rig (aktif, standby, bermasalah), capaian target rencana kerja tahunan (RK), dan progres operasi.</p>
                </div>
                <div class="cap-card">
                    <div class="cap-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg>
                    </div>
                    <h3>Manajemen Aset &amp; Rig</h3>
                    <p>Inventaris komprehensif aset rig dan alat pendukung beserta spesifikasi teknis, horsepower, nomor seri, riwayat perbaikan, dan foto fisik.</p>
                </div>
                <div class="cap-card">
                    <div class="cap-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
                    </div>
                    <h3>Pemetaan Lokasi GIS</h3>
                    <p>Visualisasi geografis kode struktur lapangan dengan koordinat latitude/longitude berbasis peta interaktif Leaflet untuk memantau sebaran rig.</p>
                </div>
                <div class="cap-card">
                    <div class="cap-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <h3>Timeline &amp; Gantt Jadwal</h3>
                    <p>Pemantauan alur jadwal tajak sumur, estimasi moving rig, durasi pekerjaan, dan kategorisasi progres bulanan dari 2026 hingga 2027.</p>
                </div>
                <div class="cap-card">
                    <div class="cap-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </div>
                    <h3>Otomasi Import &amp; Export</h3>
                    <p>Pertukaran data massal XLSX/CSV dengan wizard 3 langkah, validasi duplikat, formula injection protection, dan transaksi atomik database.</p>
                </div>
                <div class="cap-card">
                    <div class="cap-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
                    </div>
                    <h3>Sistem Notifikasi Terpadu</h3>
                    <p>Deteksi dini perubahan status alat darurat, kendala jadwal, badge angka unread di navbar, polling 30 detik, dan halaman riwayat notifikasi.</p>
                </div>
            </div>
        </div>

        <!-- ===== SECTION 4: TATA KELOLA & KEAMANAN ===== -->
        <div class="section-block" id="keamanan">
            <div class="section-head">
                <span class="section-tag">Keamanan &amp; Kepatuhan</span>
                <h2>Standar Keamanan Enterprise</h2>
                <p>Dirancang untuk melindungi kerahasiaan dan integritas data operasional vital industri minyak dan gas bumi.</p>
            </div>
            <div class="gov-grid">
                <div class="gov-card">
                    <h4>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Otorisasi RBAC Berlapis
                    </h4>
                    <p>Pemisahan peran tegas antara Administrator dan Pekerja. Akses modul (Alat, Lokasi, Jadwal, Import/Export, Audit Log) dapat didelegasikan secara terisolasi per pengguna.</p>
                </div>
                <div class="gov-card">
                    <h4>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Audit Trail Transparan
                    </h4>
                    <p>Pencatatan live mutasi data sistem ke dalam tabel audit log mencakup jenis aksi, pengguna pelaksana, alamat IP, dan waktu eksekusi untuk audit kepatuhan internal.</p>
                </div>
                <div class="gov-card">
                    <h4>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Proteksi Formula &amp; Enkripsi
                    </h4>
                    <p>Data ekspor spreadsheet dinetralisasi otomatis dari Formula Injection (=, +, -, @), validasi server-side berlapis, perlindungan CSRF, dan session aman.</p>
                </div>
            </div>
        </div>

        <!-- ===== BANNER FAQ & PUSAT BANTUAN ===== -->
        <div style="margin-top: 48px; padding: 28px 32px; border-radius: 16px; background: var(--panel-2); border: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div style="max-width: 620px;">
                <div style="display:flex; align-items:center; gap:8px; font-size:11.5px; font-weight:700; color:var(--red); text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px;"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>Pusat Bantuan &amp; Tanya Jawab</span>
                </div>
                <h3 style="font-size:18px; font-weight:700; margin:0 0 6px 0; color:var(--text); font-family:'Space Grotesk',sans-serif;">Punya Pertanyaan Seputar Operasional Rig &amp; Sistem?</h3>
                <p style="font-size:13.5px; color:var(--text-dim); margin:0; line-height:1.6;">
                    Kunjungi Pusat FAQ Publik kami untuk menemukan panduan lengkap, SOP pergantian shift, rincian koordinat lapangan, atau ajukan pertanyaan baru secara langsung.
                </p>
            </div>
            <a href="{{ route('faq') }}" class="btn-hero-primary" style="padding:10px 22px; font-size:13.5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>Buka Halaman FAQ</span>
            </a>
        </div>

        <!-- ===== SEKSI FORMULIR HUBUNGI KAMI / PERMOHONAN BANTUAN ===== -->
        <div class="section-block" id="bantuan" style="margin-top: 60px;">
            <div class="section-head">
                <span class="section-tag">Helpdesk &amp; Layanan</span>
                <h2>Hubungi Kami &amp; Permohonan Bantuan</h2>
                <p>Kirimkan permohonan koordinasi operasional, kendala rig, atau pesan kedinasan langsung kepada tim operasional Pertamina Hulu Rokan Zona 4.</p>
            </div>

            @if (session('success_kontak'))
            <div style="background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.3); border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:center; gap:12px; color:#10b981; font-weight:600; font-size:14px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px; height:22px; flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>{{ session('success_kontak') }}</span>
            </div>
            @endif

            <div class="helpdesk-grid">
                <!-- Kontak Informasi Perusahaan -->
                <div class="helpdesk-info-card">
                    <div>
                        <h4 style="font-size:16px; font-weight:700; margin:0 0 6px 0; color:var(--text); font-family:'Space Grotesk',sans-serif;">Pusat Komando Operasi Zona 4</h4>
                        <p style="font-size:13px; color:var(--text-dim); margin:0; line-height:1.6;">
                            Pengawasan keandalan armada rig dan manajemen pergerakan tajuk sumur terhubung dengan sistem pelaporan terintegrasi.
                        </p>
                    </div>

                    <div class="helpdesk-item">
                        <div class="helpdesk-ic">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="helpdesk-text">
                            <h5>Lokasi Kantor Operasional</h5>
                            <p>Komplek Pertamina EP Asset 2, Kota Prabumulih, Sumatera Selatan 31111</p>
                        </div>
                    </div>

                    <div class="helpdesk-item">
                        <div class="helpdesk-ic">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div class="helpdesk-text">
                            <h5>Layanan Helpdesk &amp; Telepon</h5>
                            <p>(0713) 321-0004 &bull; Ext. 4022 (Pusat Koordinasi Rig)</p>
                        </div>
                    </div>

                    <div class="helpdesk-item">
                        <div class="helpdesk-ic">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </div>
                        <div class="helpdesk-text">
                            <h5>Email Resmi Komunikasi</h5>
                            <p>helpdesk.rig@pertamina.com &bull; info.zona4@pertamina.com</p>
                        </div>
                    </div>

                    <div class="helpdesk-item">
                        <div class="helpdesk-ic">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="helpdesk-text">
                            <h5>Jam Layanan &amp; Pengawasan</h5>
                            <p>Siaga 24 Jam Non-Stop (7 Hari Seminggu) Waktu Indonesia Barat</p>
                        </div>
                    </div>
                </div>

                <!-- Formulir Bantuan -->
                <div class="helpdesk-form-card">
                    <form method="POST" action="{{ route('kontak.store') }}" class="helpdesk-form">
                        @csrf
                        <div class="helpdesk-row">
                            <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                                <label for="kontak_nama" style="font-size:12px; font-weight:700; color:var(--text-dim);">Nama Lengkap <span style="color:var(--red);">*</span></label>
                                <input id="kontak_nama" type="text" name="nama" value="{{ old('nama', auth()->user()?->nama) }}" required placeholder="cth. Budi Santoso" style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; outline:0;">
                                @error('nama')
                                    <div class="auth-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                                <label for="kontak_email" style="font-size:12px; font-weight:700; color:var(--text-dim);">Alamat Email <span style="color:var(--red);">*</span></label>
                                <input id="kontak_email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="nama@perusahaan.com" style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; outline:0;">
                                @error('email')
                                    <div class="auth-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="helpdesk-row">
                            <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                                <label for="kontak_no_hp" style="font-size:12px; font-weight:700; color:var(--text-dim);">Nomor HP / WhatsApp (Opsional)</label>
                                <input id="kontak_no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="cth. 081234567890" style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; outline:0;">
                            </div>
                            <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                                <label for="kontak_instansi" style="font-size:12px; font-weight:700; color:var(--text-dim);">Instansi / Perusahaan (Opsional)</label>
                                <input id="kontak_instansi" type="text" name="instansi" value="{{ old('instansi') }}" placeholder="cth. PT Pertamina Drilling Services" style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; outline:0;">
                            </div>
                        </div>

                        <div class="helpdesk-row">
                            <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                                <label for="kontak_kategori" style="font-size:12px; font-weight:700; color:var(--text-dim);">Kategori Permohonan <span style="color:var(--red);">*</span></label>
                                <select id="kontak_kategori" name="kategori" required style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; outline:0;">
                                    <option value="bantuan" {{ old('kategori') == 'bantuan' ? 'selected' : '' }}>Permohonan Bantuan / Layanan</option>
                                    <option value="operasional" {{ old('kategori') == 'operasional' ? 'selected' : '' }}>Operasional Lapangan &amp; Rig</option>
                                    <option value="saran" {{ old('kategori') == 'saran' ? 'selected' : '' }}>Kritik &amp; Saran Sistem</option>
                                    <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                                <label for="kontak_perihal" style="font-size:12px; font-weight:700; color:var(--text-dim);">Perihal Pesan <span style="color:var(--red);">*</span></label>
                                <input id="kontak_perihal" type="text" name="perihal" value="{{ old('perihal') }}" required placeholder="cth. Koordinasi Pergerakan Rig Limau" style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; outline:0;">
                                @error('perihal')
                                    <div class="auth-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group" style="display:flex; flex-direction:column; gap:6px;">
                            <label for="kontak_pesan" style="font-size:12px; font-weight:700; color:var(--text-dim);">Rincian Pesan / Deskripsi Permohonan <span style="color:var(--red);">*</span></label>
                            <textarea id="kontak_pesan" name="pesan" rows="4" required placeholder="Jelaskan kebutuhan koordinasi, kendala di lapangan, atau pertanyaan teknis Anda secara detail..." style="background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:13px; font-family:inherit; outline:0;">{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <div class="auth-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-fill" style="padding:12px 20px; font-size:13.5px; width:100%; justify-content:center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:8px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Kirimkan Pesan Bantuan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ===== SECTION 6: CTA ===== -->
        <div class="about-cta">
            <h2>Siap Mengoptimalkan Operasional Rig Anda?</h2>
            <p>
                Gunakan platform terintegrasi Pertamina Rig Monitoring WK Zona 4 untuk koordinasi aset yang lebih cepat, transparan, dan presisi.
            </p>
            <div class="cta-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                        Buka Dashboard Sekarang
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                        Masuk ke Akun
                    </a>
                @endauth
                <a href="{{ route('home') }}" class="btn-hero-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali ke Home
                </a>
            </div>
            <div class="cta-note">
                Pertamina Hulu Rokan (PHR) Regional 1 Zona 4 — Platform Internal Khusus Pengguna Berwenang
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fields = [
                {
                    name: 'Prabumulih Field',
                    badge: 'Pusat Operasi',
                    category: 'pusat',
                    lat: -3.4354,
                    lng: 104.2312,
                    desc: 'Pusat komando operasional, workshop pemeliharaan alat berat, serta koordinasi rig pemboran sumur eksploitasi dan eksplorasi utama.',
                    rig: 'Konsentrasi Rig & Workshop',
                    status: 'Aktif 24/7'
                },
                {
                    name: 'Limau Field',
                    badge: 'Klaster Minyak',
                    category: 'aktif',
                    lat: -3.5620,
                    lng: 104.1205,
                    desc: 'Salah satu lapangan minyak produktif dengan intensitas perawatan sumur rutin menggunakan armada rig workover & well service.',
                    rig: 'Rotasi Workover Aktif',
                    status: 'Operasional Aktif'
                },
                {
                    name: 'Pendopo Field',
                    badge: 'Reaktivasi & Gas',
                    category: 'aktif',
                    lat: -3.2980,
                    lng: 103.8560,
                    desc: 'Wilayah kerja strategis dengan reaktivasi sumur-sumur tua dan program pengeboran sumur baru terencana untuk menjaga tekanan reservoir.',
                    rig: 'Jadwal Pemboran Terencana',
                    status: 'Operasional Aktif'
                },
                {
                    name: 'Adera Field',
                    badge: 'Intensitas Tinggi',
                    category: 'aktif',
                    lat: -3.2100,
                    lng: 104.1450,
                    desc: 'Area operasional dengan mobilitas tinggi untuk kegiatan workover berkala guna memitigasi penurunan produksi alamiah.',
                    rig: 'Sumur Workover & Re-entry',
                    status: 'Operasional Aktif'
                },
                {
                    name: 'Ramba Field',
                    badge: 'Fasilitas Separasi',
                    category: 'aktif',
                    lat: -2.8900,
                    lng: 103.9200,
                    desc: 'Klaster sumur penting terhubung jaringan pipa distribusi dan fasilitas separasi minyak-gas regional Sumatera Bagian Selatan.',
                    rig: 'Rig Standby & Perawatan',
                    status: 'Operasional Aktif'
                },
                {
                    name: 'Ogan Komering Field',
                    badge: 'Rotasi Standby',
                    category: 'aktif',
                    lat: -3.8500,
                    lng: 104.4500,
                    desc: 'Lapangan minyak dan gas bumi dengan rotasi rig standby serta pengawasan performa alat berat pendukung kelancaran produksi.',
                    rig: 'Monitoring Kesiapan Alat',
                    status: 'Operasional Aktif'
                },
                {
                    name: 'Raja / Tempirai Field',
                    badge: 'Lapangan Satelit',
                    category: 'satelit',
                    lat: -3.1500,
                    lng: 104.0500,
                    desc: 'Klaster sumur penunjang dengan program optimalisasi pengangkatan buatan (artificial lift) dan jadwal servis berkala.',
                    rig: 'Perawatan Sumur Terjadwal',
                    status: 'Operasional Aktif'
                }
            ];

            const mapEl = document.getElementById('leafletAboutMap');
            if (!mapEl) return;

            const map = L.map('leafletAboutMap', {
                zoomControl: true,
                scrollWheelZoom: false,
                attributionControl: true
            }).setView([-3.42, 104.15], 8);

            function getMapThemeUrls(theme) {
                const isDark = theme !== 'light';
                return {
                    base: isDark
                        ? 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}'
                        : 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
                    ref: isDark
                        ? 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Reference/MapServer/tile/{z}/{y}/{x}'
                        : 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Reference/MapServer/tile/{z}/{y}/{x}'
                };
            }

            let currentMapTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            let mapUrls = getMapThemeUrls(currentMapTheme);

            let baseTileLayer = L.tileLayer(mapUrls.base, {
                attribution: '&copy; <a href="https://www.esri.com/" target="_blank" rel="noopener">Esri</a>',
                maxZoom: 16
            }).addTo(map);

            let refTileLayer = L.tileLayer(mapUrls.ref, {
                attribution: '',
                maxZoom: 16
            }).addTo(map);

            window.addEventListener('theme-changed', (e) => {
                const newTheme = e.detail?.theme || document.documentElement.getAttribute('data-theme') || 'dark';
                const newUrls = getMapThemeUrls(newTheme);
                baseTileLayer.setUrl(newUrls.base);
                refTileLayer.setUrl(newUrls.ref);
            });

            // Add markers with custom divIcon
            const bounds = L.latLngBounds();

            fields.forEach(f => {
                const markerColor = f.category === 'pusat' ? '#ea2027' : (f.category === 'satelit' ? '#f59e0b' : '#10b981');
                const customIcon = L.divIcon({
                    className: 'custom-field-pin',
                    html: `<div style="
                        width: 26px; height: 26px; border-radius: 50%;
                        background: ${markerColor}; border: 2.5px solid #fff;
                        box-shadow: 0 0 12px ${markerColor}99;
                        display: flex; align-items: center; justify-content: center;
                        color: #fff;
                    "><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" style="width:13px;height:13px;"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg></div>`,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13],
                    popupAnchor: [0, -14]
                });

                const popupContent = `
                    <div class="popup-field-title">
                        <span>${f.name}</span>
                        <span class="popup-badge">${f.badge}</span>
                    </div>
                    <div class="popup-field-desc">${f.desc}</div>
                    <div class="popup-field-meta">
                        <div><strong>Fokus Rig:</strong> ${f.rig}</div>
                        <div><strong>Koordinat:</strong> ${f.lat}, ${f.lng}</div>
                        <div><strong>Status:</strong> <span style="color:#10b981; font-weight:700;">● ${f.status}</span></div>
                    </div>
                `;

                const marker = L.marker([f.lat, f.lng], { icon: customIcon }).addTo(map);
                marker.bindPopup(popupContent);
                bounds.extend([f.lat, f.lng]);
            });

            if (fields.length > 0) {
                map.fitBounds(bounds, { padding: [35, 35] });
            }
        });
    </script>
    @endpush
    </div>
</x-guest-layout>
