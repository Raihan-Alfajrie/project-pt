<x-guest-layout title="About — Pertamina Admin Panel">
    <style>
        .center-wrap {
            min-height: calc(100vh - 180px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative;
            z-index: 1;
        }
        .cs-card { text-align: center; max-width: 420px; }
        .cs-icon { width: 76px; height: 76px; border-radius: 20px; background: var(--red-soft); display: flex; align-items: center; justify-content: center; margin: 0 auto 26px; }
        .cs-icon svg { width: 34px; height: 34px; color: var(--red); }
        .cs-card h1 { font-size: 30px; margin-bottom: 10px; }
        .cs-card p { font-size: 14px; color: var(--text-dim); line-height: 1.6; margin-bottom: 26px; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border-radius: 10px; background: var(--panel); border: 1px solid var(--border-soft); color: var(--text); font-size: 13px; font-weight: 700; transition: border-color .2s; }
        .btn-back:hover { border-color: var(--red); }
        .btn-back svg { width: 14px; height: 14px; }
    </style>

    <div class="center-wrap">
        <div class="cs-card">
            <div class="cs-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M9.5 9a2.5 2.5 0 015 .5c0 1.6-2.3 1.8-2.4 3.4M12 17h.01"/>
                </svg>
            </div>
            <h1>Coming Soon</h1>
            <p>Halaman About sedang disiapkan. Informasi lengkap tentang platform ini akan hadir di sini.</p>
            <a class="btn-back" href="{{ route('home') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke Home
            </a>
        </div>
    </div>
</x-guest-layout>
