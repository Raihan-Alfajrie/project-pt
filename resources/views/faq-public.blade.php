<x-guest-layout title="Pusat Bantuan & FAQ — Pertamina Rig Monitoring Zona 4">
    @push('styles')
    <style>
        .faq-hero-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 50px 24px 40px;
            background: linear-gradient(180deg, rgba(234,32,39,.08) 0%, transparent 100%);
            border-bottom: 1px solid var(--border-soft);
        }
        .faq-hero {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
        }
        .faq-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--red);
            background: var(--red-soft);
            padding: 7px 15px;
            border-radius: 99px;
            margin-bottom: 18px;
            border: 1px solid rgba(234,32,39,.25);
        }
        .faq-eyebrow svg { width: 14px; height: 14px; }
        .faq-hero h1 {
            font-size: 40px;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.02em;
        }
        .faq-hero h1 span { color: var(--red); }
        .faq-hero p {
            font-size: 15.5px;
            color: var(--text-dim);
            line-height: 1.7;
            margin: 16px auto 0;
            max-width: 680px;
        }

        /* Search Bar */
        .faq-search-box {
            max-width: 620px;
            margin: 28px auto 0;
            position: relative;
        }
        .faq-search-inner {
            display: flex;
            align-items: center;
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 6px 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,.25);
            transition: all .2s;
        }
        .faq-search-inner:focus-within {
            border-color: var(--red);
            box-shadow: 0 0 0 4px var(--red-soft);
        }
        .faq-search-inner svg {
            width: 18px;
            height: 18px;
            color: var(--text-faint);
            flex-shrink: 0;
            margin-right: 12px;
        }
        .faq-search-input {
            width: 100%;
            background: transparent;
            border: 0;
            outline: 0;
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            padding: 8px 0;
        }
        .faq-search-clear {
            background: transparent;
            border: 0;
            color: var(--text-faint);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .faq-search-clear:hover { color: var(--text); }
        .faq-search-clear svg { width: 14px; height: 14px; margin: 0; }

        /* Container & Tabs */
        .faq-main-wrap {
            max-width: 1000px;
            margin: 0 auto;
            padding: 36px 24px 60px;
        }
        .cat-pills {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 32px;
        }
        .cat-pill {
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid var(--border-soft);
            background: var(--panel-2);
            color: var(--text-dim);
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .cat-pill:hover {
            border-color: var(--red);
            color: var(--text);
        }
        .cat-pill.active {
            background: var(--red);
            border-color: var(--red);
            color: #fff;
        }
        .cat-pill .badge {
            font-size: 11px;
            background: rgba(255,255,255,.2);
            padding: 1px 7px;
            border-radius: 99px;
            font-weight: 700;
        }
        .cat-pill:not(.active) .badge {
            background: var(--panel);
            color: var(--text-faint);
        }

        /* FAQ Accordion */
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .faq-item {
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            overflow: hidden;
            transition: all .2s;
        }
        .faq-item:hover {
            border-color: var(--border);
        }
        .faq-item.is-open {
            border-color: rgba(234,32,39,.4);
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        .faq-trigger {
            width: 100%;
            text-align: left;
            background: transparent;
            border: 0;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            cursor: pointer;
            font-family: inherit;
            color: var(--text);
        }
        .faq-trigger-left {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .faq-cat-tag {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--red);
        }
        .faq-question {
            font-size: 15.5px;
            font-weight: 700;
            line-height: 1.35;
            color: var(--text);
        }
        .faq-chevron {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--panel-2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dim);
            flex-shrink: 0;
            transition: transform .25s ease;
        }
        .faq-chevron svg { width: 14px; height: 14px; }
        .faq-item.is-open .faq-chevron {
            transform: rotate(180deg);
            background: var(--red-soft);
            color: var(--red);
        }
        .faq-body {
            padding: 0 20px 20px;
            font-size: 14px;
            line-height: 1.7;
            color: var(--text-dim);
            border-top: 1px dashed var(--border-soft);
            margin-top: 4px;
            padding-top: 14px;
        }

        /* Success Alert */
        .faq-alert-success {
            background: rgba(16,185,129,.12);
            border: 1px solid rgba(16,185,129,.3);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #10b981;
            font-size: 13.5px;
            font-weight: 600;
        }

        /* Empty State */
        .faq-empty {
            text-align: center;
            padding: 48px 24px;
            background: var(--panel);
            border-radius: 14px;
            border: 1px dashed var(--border);
        }
        .faq-empty svg { width: 42px; height: 42px; color: var(--text-faint); margin-bottom: 12px; }
        .faq-empty h3 { font-size: 16px; margin-bottom: 6px; }
        .faq-empty p { font-size: 13px; color: var(--text-dim); margin-bottom: 18px; }

        /* Ask Question CTA Card */
        .faq-ask-card {
            margin-top: 48px;
            padding: 32px 28px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--panel-2) 0%, var(--panel) 100%);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
        }
        .faq-ask-info {
            max-width: 580px;
        }
        .faq-ask-info h3 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }
        .faq-ask-info p {
            font-size: 13.5px;
            color: var(--text-dim);
            line-height: 1.6;
            margin: 0;
        }
        .faq-ask-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            background: var(--red);
            color: #fff;
            font-size: 13.5px;
            font-weight: 700;
            border: 0;
            cursor: pointer;
            transition: all .2s;
            font-family: inherit;
        }
        .faq-ask-btn:hover {
            background: var(--red-strong);
            transform: translateY(-1px);
        }
        .faq-ask-btn svg { width: 16px; height: 16px; }

        /* Modal Dialog */
        .faq-modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(8,6,7,.75);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .faq-modal-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(0,0,0,.65);
        }
        .faq-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--border-soft);
        }
        .faq-modal-head h3 { font-size: 17px; font-weight: 700; }
        .faq-modal-body {
            padding: 20px 24px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .faq-form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .faq-form-group label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-dim);
        }
        .faq-form-group input,
        .faq-form-group select,
        .faq-form-group textarea {
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            border-radius: 9px;
            padding: 10px 12px;
            color: var(--text);
            font-size: 13px;
            font-family: inherit;
            outline: 0;
            width: 100%;
            box-sizing: border-box;
        }
        .faq-form-group input:focus,
        .faq-form-group select:focus,
        .faq-form-group textarea:focus {
            border-color: var(--red);
        }
        .faq-modal-foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 8px;
        }
    </style>
    @endpush

    <!-- Main FAQ Alpine Container -->
    <div x-data="{
        q: '',
        activeCat: 'semua',
        activeId: null,
        askModalOpen: false,
        faqs: {{ Js::from($faqs->map(function($f) {
            return [
                'id' => $f->id,
                'pertanyaan' => $f->pertanyaan,
                'jawaban' => $f->jawaban,
                'kategori' => $f->kategori,
                'kategori_label' => $f->kategori_label,
            ];
        })) }},
        matches(item) {
            const matchCat = this.activeCat === 'semua' || item.kategori === this.activeCat;
            if (!matchCat) return false;
            if (!this.q.trim()) return true;
            const term = this.q.toLowerCase();
            return item.pertanyaan.toLowerCase().includes(term) || item.jawaban.toLowerCase().includes(term);
        },
        get filteredFaqs() {
            return this.faqs.filter(item => this.matches(item));
        },
        toggleItem(id) {
            this.activeId = (this.activeId === id) ? null : id;
        }
    }">

        <!-- Hero Header -->
        <div class="faq-hero-wrap">
            <div class="faq-hero">
                <div class="faq-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>Pusat Bantuan &amp; FAQ Publik</span>
                </div>
                <h1>Pertanyaan yang Sering Diajukan <span>(FAQ)</span></h1>
                <p>
                    Temukan panduan lengkap seputar operasional armada rig pemboran, inventaris peralatan, pemetaan koordinat lapangan, dan timeline jadwal tajak di Wilayah Kerja Zona 4.
                </p>

                <!-- Live Search Bar -->
                <div class="faq-search-box">
                    <div class="faq-search-inner">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text"
                               class="faq-search-input"
                               placeholder="Cari kata kunci topik (contoh: jadwal rig, SOP keselamatan, koordinat)..."
                               x-model="q"
                               aria-label="Cari pertanyaan FAQ">
                        <button type="button"
                                class="faq-search-clear"
                                x-show="q.length > 0"
                                @click="q = ''"
                                aria-label="Bersihkan pencarian">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="faq-main-wrap">

            @if (session('success_pertanyaan'))
            <div class="faq-alert-success">
                <div style="display:flex; align-items:center; gap:10px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px; height:20px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('success_pertanyaan') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;" aria-label="Tutup pesan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            @endif

            <!-- Category Pills Filter -->
            <div class="cat-pills">
                <button type="button"
                        class="cat-pill"
                        :class="{ 'active': activeCat === 'semua' }"
                        @click="activeCat = 'semua'">
                    <span>Semua Topik</span>
                    <span class="badge">{{ $kategoriCounts['semua'] }}</span>
                </button>
                <button type="button"
                        class="cat-pill"
                        :class="{ 'active': activeCat === 'alat_rig' }"
                        @click="activeCat = 'alat_rig'">
                    <span>Operasional Rig &amp; Alat</span>
                    <span class="badge">{{ $kategoriCounts['alat_rig'] }}</span>
                </button>
                <button type="button"
                        class="cat-pill"
                        :class="{ 'active': activeCat === 'lokasi_peta' }"
                        @click="activeCat = 'lokasi_peta'">
                    <span>Peta &amp; Lapangan</span>
                    <span class="badge">{{ $kategoriCounts['lokasi_peta'] }}</span>
                </button>
                <button type="button"
                        class="cat-pill"
                        :class="{ 'active': activeCat === 'jadwal_gantt' }"
                        @click="activeCat = 'jadwal_gantt'">
                    <span>Jadwal Operasi &amp; Gantt</span>
                    <span class="badge">{{ $kategoriCounts['jadwal_gantt'] }}</span>
                </button>
                <button type="button"
                        class="cat-pill"
                        :class="{ 'active': activeCat === 'akun_akses' }"
                        @click="activeCat = 'akun_akses'">
                    <span>Akun &amp; Hak Akses</span>
                    <span class="badge">{{ $kategoriCounts['akun_akses'] }}</span>
                </button>
            </div>

            <!-- Dynamic Accordion List -->
            <div class="faq-list">
                <template x-for="item in filteredFaqs" :key="item.id">
                    <div class="faq-item" :class="{ 'is-open': activeId === item.id }">
                        <button type="button"
                                class="faq-trigger"
                                @click="toggleItem(item.id)"
                                :aria-expanded="(activeId === item.id).toString()">
                            <div class="faq-trigger-left">
                                <span class="faq-cat-tag" x-text="item.kategori_label"></span>
                                <span class="faq-question" x-text="item.pertanyaan"></span>
                            </div>
                            <div class="faq-chevron">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </button>
                        <div class="faq-body"
                             x-show="activeId === item.id"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             x-cloak>
                            <div style="white-space: pre-line;" x-text="item.jawaban"></div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div class="faq-empty" x-show="filteredFaqs.length === 0" x-cloak>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    <h3>Tidak Menemukan Pertanyaan</h3>
                    <p>Tidak ada butir FAQ yang cocok dengan kata kunci pencarian atau kategori yang dipilih.</p>
                    <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="btn-outline" @click="q = ''; activeCat = 'semua'">Reset Pencarian</button>
                        <button type="button" class="btn-fill" @click="askModalOpen = true">Ajukan Pertanyaan Baru</button>
                    </div>
                </div>
            </div>

            <!-- Card Ajukan Pertanyaan / CTA -->
            <div class="faq-ask-card">
                <div class="faq-ask-info">
                    <h3>Belum Menemukan Jawaban yang Anda Cari?</h3>
                    <p>
                        Kirimkan pertanyaan teknis atau pertanyaan operasional langsung ke tim pengelola platform. Kami akan meninjau pertanyaan Anda dan memberikan jawaban resmi melalui email.
                    </p>
                </div>
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('faq') }}" class="btn-outline">
                                Kelola FAQ di Dashboard
                            </a>
                        @endif
                    @endauth
                    <button type="button" class="faq-ask-btn" @click="askModalOpen = true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <span>Ajukan Pertanyaan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Ajukan Pertanyaan Baru -->
        <div x-show="askModalOpen"
             x-cloak
             class="faq-modal-backdrop"
             @click.self="askModalOpen = false"
             @keydown.escape.window="askModalOpen = false">
            <div class="faq-modal-card"
                 x-show="askModalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <div class="faq-modal-head">
                    <h3>Ajukan Pertanyaan ke Tim Operasional</h3>
                    <button type="button" class="auth-close" @click="askModalOpen = false" aria-label="Tutup Modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('faq.tanya') }}" class="faq-modal-body">
                    @csrf
                    <div class="faq-form-group">
                        <label for="faq_nama">Nama Lengkap <span style="color:var(--red);">*</span></label>
                        <input id="faq_nama" type="text" name="nama" value="{{ old('nama', auth()->user()?->nama) }}" required placeholder="cth. Budi Santoso">
                        @error('nama')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="faq-form-group">
                        <label for="faq_email">Email Aktif <span style="color:var(--red);">*</span></label>
                        <input id="faq_email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="nama@perusahaan.com">
                        @error('email')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="faq-form-group">
                        <label for="faq_instansi">Instansi / Unit Kerja (Opsional)</label>
                        <input id="faq_instansi" type="text" name="instansi" value="{{ old('instansi') }}" placeholder="cth. PT Mitra Pengeboran / Pertamina EP">
                    </div>

                    <div class="faq-form-group">
                        <label for="faq_kategori">Kategori Topik</label>
                        <select id="faq_kategori" name="kategori">
                            <option value="alat_rig">Operasional Rig &amp; Alat</option>
                            <option value="lokasi_peta">Peta &amp; Struktur Lapangan</option>
                            <option value="jadwal_gantt">Jadwal Operasi &amp; Timeline</option>
                            <option value="akun_akses">Akun &amp; Hak Akses</option>
                            <option value="lainnya">Pertanyaan Umum Lainnya</option>
                        </select>
                    </div>

                    <div class="faq-form-group">
                        <label for="faq_pertanyaan">Pertanyaan Anda <span style="color:var(--red);">*</span></label>
                        <textarea id="faq_pertanyaan" name="pertanyaan" rows="4" required placeholder="Tuliskan detail pertanyaan atau informasi yang Anda butuhkan secara jelas...">{{ old('pertanyaan') }}</textarea>
                        @error('pertanyaan')
                            <div class="auth-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="faq-modal-foot">
                        <button type="button" class="btn-outline" @click="askModalOpen = false">Batal</button>
                        <button type="submit" class="btn-fill">Kirimkan Pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-guest-layout>
