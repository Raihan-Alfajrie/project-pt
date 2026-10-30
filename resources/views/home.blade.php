<x-guest-layout>
    <style>
        /* ===== HERO WITH BACKGROUND BANNER ===== */
        .hero-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding-bottom: 60px;
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
        .hero { position: relative; z-index: 1; max-width: 820px; margin: 0 auto; padding: 80px 24px 30px; text-align: center; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: var(--red); background: var(--red-soft); padding: 7px 14px; border-radius: 99px; margin-bottom: 22px; }
        .eyebrow svg { width: 13px; height: 13px; }
        .hero h1 { font-size: 52px; line-height: 1.08; font-weight: 800; }
        .hero h1 span { color: var(--red); }
        .hero p { font-size: 16px; color: var(--text-dim); line-height: 1.6; margin: 22px auto 0; max-width: 560px; }
        .hero-actions { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 32px; flex-wrap: wrap; }
        .btn-hero-primary { display: flex; align-items: center; gap: 8px; padding: 13px 22px; border-radius: 11px; border: 0; background: var(--red); color: #fff; font-size: 14.5px; font-weight: 700; cursor: pointer; font-family: inherit; box-shadow: 0 8px 24px rgba(234,32,39,.3); transition: background .2s; }
        .btn-hero-primary:hover { background: var(--red-strong); }
        .btn-hero-primary svg { width: 15px; height: 15px; }
        .btn-hero-secondary { display: flex; align-items: center; gap: 8px; padding: 13px 22px; border-radius: 11px; border: 1px solid var(--border-soft); background: var(--panel); color: var(--text); font-size: 14.5px; font-weight: 700; cursor: pointer; font-family: inherit; transition: border-color .2s; }
        .btn-hero-secondary:hover { border-color: var(--red); }
        .check-row { display: flex; align-items: center; justify-content: center; gap: 22px; margin-top: 26px; flex-wrap: wrap; }
        .check-row span { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--text-faint); }
        .check-row svg { width: 14px; height: 14px; color: var(--red); }

        /* ===== stats strip ===== */
        .stats { position: relative; z-index: 1; max-width: 900px; margin: 36px auto 0; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; background: var(--border-soft); border: 1px solid var(--border-soft); border-radius: 16px; overflow: hidden; box-shadow: 0 16px 40px rgba(0,0,0,.35); }
        .stat { background: rgba(30, 21, 23, 0.88); backdrop-filter: blur(8px); padding: 22px 18px; text-align: center; }
        :root[data-theme="light"] .stat {
            background: rgba(255, 255, 255, 0.88);
        }
        .stat .n { font-size: 26px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; color: var(--red); }
        .stat .l { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; }

        /* ===== features ===== */
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
                <a href="#fitur" class="btn-hero-secondary">Lihat Fitur</a>
            </div>
            <div class="check-row">
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>16 rig termonitor real-time</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>Data terpusat, satu sumber kebenaran</span>
            </div>
        </div>

        <div class="stats">
            <div class="stat"><div class="n">16</div><div class="l">Rig Termonitor</div></div>
            <div class="stat"><div class="n">14</div><div class="l">Kode Lapangan</div></div>
            <div class="stat"><div class="n">30</div><div class="l">Item Jadwal Aktif</div></div>
            <div class="stat"><div class="n">24</div><div class="l">Alat &amp; Rig Terdata</div></div>
        </div>
    </div>

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
</x-guest-layout>
