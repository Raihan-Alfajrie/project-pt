<x-guest-layout title="Tentang Platform — Pertamina Rig Monitoring Zona 4">
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

        <!-- ===== SECTION 5: CTA ===== -->
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
</x-guest-layout>
