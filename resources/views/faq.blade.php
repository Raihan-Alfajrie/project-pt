<x-app-layout :title="'Pertamina Admin Panel — FAQ'" pageTitle="FAQ" pageDesc="Pusat bantuan &amp; tanya-jawab operasional sistem">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 .5c0 1.6-2.3 1.8-2.4 3.4M12 17h.01"/></svg>
    </x-slot>

    @push('styles')
    <style>
        /* ===== Layout & Toolbar ===== */
        .faq-wrap {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 1040px;
            margin: 0 auto;
            width: 100%;
        }

        .sec-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .sec-head h2 {
            font-size: 16px;
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
        }

        .sec-head .hint {
            font-size: 12px;
            color: var(--text-faint);
            margin-top: 3px;
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 14px;
            min-width: 260px;
        }

        .search-box svg {
            width: 15px;
            height: 15px;
            color: var(--text-faint);
            flex: 0 0 auto;
        }

        .search-box input {
            background: transparent;
            border: 0;
            outline: 0;
            color: var(--text);
            font-size: 13px;
            width: 100%;
            font-family: inherit;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 10px;
            background: var(--red);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            border: 0;
            font-family: inherit;
            transition: background .15s ease;
        }

        .btn-add:hover {
            background: var(--red-strong);
        }

        .btn-add svg {
            width: 15px;
            height: 15px;
        }

        /* ===== Category Filter Pills ===== */
        .category-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 99px;
            background: var(--panel);
            border: 1px solid var(--border);
            color: var(--text-dim);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s ease;
            font-family: inherit;
        }

        .pill-btn:hover {
            border-color: var(--red);
            color: var(--text);
        }

        .pill-btn.active {
            background: var(--red);
            border-color: var(--red);
            color: #fff;
        }

        .pill-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1px 7px;
            border-radius: 99px;
            font-size: 10.5px;
            font-weight: 700;
            background: var(--panel-2);
            color: inherit;
        }

        .pill-btn.active .pill-count {
            background: rgba(255,255,255,.25);
            color: #fff;
        }

        /* ===== Accordion List ===== */
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .faq-item {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .faq-item:hover {
            border-color: var(--border-soft);
        }

        .faq-item.is-open {
            border-color: rgba(234,32,39,.35);
        }

        .faq-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            cursor: pointer;
            user-select: none;
            gap: 14px;
        }

        .faq-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .badge-cat {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            white-space: nowrap;
            text-transform: capitalize;
            flex: 0 0 auto;
        }

        .badge-cat.alat_rig {
            background: rgba(75,155,240,.15);
            color: #7ab4f5;
        }

        .badge-cat.lokasi_peta {
            background: rgba(74,222,128,.15);
            color: #4ade80;
        }

        .badge-cat.jadwal_gantt {
            background: rgba(240,169,59,.16);
            color: #f0a93b;
        }

        .badge-cat.akun_akses {
            background: rgba(168,85,247,.16);
            color: #c084fc;
        }

        .faq-q-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.4;
            flex: 1;
        }

        .faq-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-dim);
            transition: all .15s ease;
        }

        .action-btn:hover {
            color: var(--text);
            background: var(--panel-3);
        }

        .action-btn.btn-del:hover {
            color: #ff6b6f;
            border-color: rgba(234,32,39,.3);
            background: rgba(234,32,39,.1);
        }

        .action-btn svg {
            width: 14px;
            height: 14px;
        }

        .chevron-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-faint);
            transition: transform .2s ease;
        }

        .chevron-icon svg {
            width: 16px;
            height: 16px;
        }

        .chevron-icon.rotate {
            transform: rotate(180deg);
            color: var(--red);
        }

        .faq-body {
            padding: 0 20px 18px 20px;
            font-size: 13px;
            line-height: 1.65;
            color: var(--text-dim);
            border-top: 1px dashed var(--border-soft);
            margin-top: 2px;
            padding-top: 14px;
        }

        /* ===== Empty State ===== */
        .empty-faq {
            background: var(--panel);
            border: 1px dashed var(--border);
            border-radius: 12px;
            padding: 48px 24px;
            text-align: center;
            color: var(--text-faint);
        }

        .empty-faq svg {
            width: 40px;
            height: 40px;
            margin: 0 auto 14px;
            color: var(--text-faint);
            opacity: .6;
        }

        .empty-faq h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .empty-faq p {
            font-size: 12.5px;
            margin: 0;
        }

        /* ===== Support Helpdesk Card ===== */
        .support-card {
            background: linear-gradient(135deg, var(--panel) 0%, var(--panel-2) 100%);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 10px;
        }

        .support-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 280px;
        }

        .support-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--red-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            color: var(--red);
        }

        .support-icon svg {
            width: 26px;
            height: 26px;
        }

        .support-text h3 {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 4px;
            font-family: 'Space Grotesk', sans-serif;
        }

        .support-text p {
            font-size: 12px;
            color: var(--text-dim);
            margin: 0;
            line-height: 1.5;
        }

        .support-channels {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .support-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 9px;
            background: var(--panel-3);
            border: 1px solid var(--border-soft);
            color: var(--text);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s ease;
        }

        .support-btn:hover {
            border-color: var(--red);
            background: var(--panel);
        }

        .support-btn svg {
            width: 14px;
            height: 14px;
            color: var(--red);
        }

        /* ===== Modal Style ===== */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(10,8,9,.65);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .overlay.show {
            display: flex;
        }

        .modal {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,.5);
        }

        .modal.modal-confirm {
            max-width: 420px;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-soft);
        }

        .modal-head h3 {
            font-size: 16px;
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
        }

        .modal-close {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--panel-2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-dim);
            border: 0;
        }

        .modal-close:hover {
            color: var(--text);
        }

        .modal-body {
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modal-foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 24px;
            border-top: 1px solid var(--border-soft);
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-dim);
        }

        .field label .req {
            color: var(--red);
        }

        .field input, .field select, .field textarea {
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            border-radius: 9px;
            padding: 10px 14px;
            color: var(--text);
            font-size: 13px;
            font-family: inherit;
            outline: 0;
            transition: border-color .15s ease;
        }

        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--red);
        }

        .field textarea {
            resize: vertical;
            min-height: 110px;
            line-height: 1.5;
        }

        .btn-ghost {
            padding: 9px 16px;
            border-radius: 9px;
            background: var(--panel-2);
            color: var(--text-dim);
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid var(--border-soft);
            font-family: inherit;
        }

        .btn-ghost:hover {
            color: #fff;
        }

        .btn-submit {
            padding: 9px 18px;
            border-radius: 9px;
            background: var(--red);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            border: 0;
            font-family: inherit;
        }

        .btn-submit:hover {
            background: var(--red-strong);
        }

        .btn-danger {
            padding: 9px 18px;
            border-radius: 9px;
            background: var(--red);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            border: 0;
            font-family: inherit;
        }

        .btn-danger:hover {
            background: var(--red-strong);
        }

        .flash-alert {
            padding: 12px 18px;
            border-radius: 10px;
            background: rgba(74,222,128,.14);
            border: 1px solid rgba(74,222,128,.3);
            color: #4ade80;
            font-size: 12.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
    @endpush

    <div class="faq-wrap" x-data="faqApp()">
        {{-- Flash Notification --}}
        @if(session('success'))
        <div class="flash-alert">
            <div style="display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none; border:0; color:inherit; cursor:pointer; font-size:16px;">&times;</button>
        </div>
        @endif

        {{-- Top Toolbar & Header --}}
        <div class="sec-head">
            <div>
                <h2>Pusat Bantuan &amp; FAQ</h2>
                <div class="hint">Temukan jawaban seputar modul rig, pemetaan, penjadwalan, dan perizinan akun.</div>
            </div>
            <div class="toolbar">
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" x-model="searchQuery" placeholder="Cari pertanyaan atau kata kunci…" id="faqSearchInput">
                </div>
                @if($canManage)
                <button type="button" class="btn-add" @click="openCreateModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Tambah FAQ</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Category Pills --}}
        <div class="category-pills">
            <button type="button" class="pill-btn" :class="{ 'active': activeCategory === 'semua' }" @click="activeCategory = 'semua'">
                <span>Semua Kategori</span>
                <span class="pill-count" x-text="counts.semua"></span>
            </button>
            <button type="button" class="pill-btn" :class="{ 'active': activeCategory === 'alat_rig' }" @click="activeCategory = 'alat_rig'">
                <span>Operasional Rig &amp; Alat</span>
                <span class="pill-count" x-text="counts.alat_rig"></span>
            </button>
            <button type="button" class="pill-btn" :class="{ 'active': activeCategory === 'lokasi_peta' }" @click="activeCategory = 'lokasi_peta'">
                <span>Peta &amp; Lapangan</span>
                <span class="pill-count" x-text="counts.lokasi_peta"></span>
            </button>
            <button type="button" class="pill-btn" :class="{ 'active': activeCategory === 'jadwal_gantt' }" @click="activeCategory = 'jadwal_gantt'">
                <span>Jadwal Operasi &amp; Gantt</span>
                <span class="pill-count" x-text="counts.jadwal_gantt"></span>
            </button>
            <button type="button" class="pill-btn" :class="{ 'active': activeCategory === 'akun_akses' }" @click="activeCategory = 'akun_akses'">
                <span>Akun &amp; Hak Akses</span>
                <span class="pill-count" x-text="counts.akun_akses"></span>
            </button>
        </div>

        {{-- FAQ Accordion List --}}
        <div class="faq-list">
            <template x-for="item in filteredFaqs" :key="item.id">
                <div class="faq-item" :class="{ 'is-open': openItem === item.id }">
                    <div class="faq-header" @click="toggleItem(item.id)">
                        <div class="faq-title-group">
                            <span class="badge-cat" :class="item.kategori" x-text="item.kategori_label"></span>
                            <span class="faq-q-text" x-text="item.pertanyaan"></span>
                        </div>
                        <div class="faq-actions">
                            @if($canManage)
                            <button type="button" class="action-btn" title="Edit FAQ" @click.stop="openEditModal(item)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button type="button" class="action-btn btn-del" title="Hapus FAQ" @click.stop="openDeleteModal(item)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            </button>
                            @endif
                            <div class="chevron-icon" :class="{ 'rotate': openItem === item.id }">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9l6 6 6-6"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="faq-body" x-show="openItem === item.id" x-transition.opacity.duration.200ms>
                        <div style="white-space: pre-line;" x-text="item.jawaban"></div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <div class="empty-faq" x-show="filteredFaqs.length === 0">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h3>Tidak ada pertanyaan yang sesuai</h3>
                <p>Coba gunakan kata kunci pencarian yang berbeda atau pilih kategori lain.</p>
            </div>
        </div>

        {{-- Contact Helpdesk Card --}}
        <div class="support-card">
            <div class="support-info">
                <div class="support-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <div class="support-text">
                    <h3>Butuh Bantuan Operasional Tambahan?</h3>
                    <p>Tim IT Operation &amp; Support Rig Pertamina Subholding Upstream Regional 1 Zona 4 siap membantu kendala sistem Anda 24/7.</p>
                </div>
            </div>
            <div class="support-channels">
                <a href="mailto:it-support.zona4@pertamina.com" class="support-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>Email Tim Support</span>
                </a>
                <a href="tel:+62711388000" class="support-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    <span>Hotline Lapangan</span>
                </a>
            </div>
        </div>

        @if($canManage)
        {{-- Modal Tambah FAQ --}}
        <div class="overlay" :class="{ 'show': showCreateModal }" @click.self="showCreateModal = false">
            <div class="modal">
                <div class="modal-head">
                    <h3>Tambah Butir FAQ</h3>
                    <button type="button" class="modal-close" @click="showCreateModal = false">&times;</button>
                </div>
                <form action="{{ route('faq.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="field">
                            <label>Pertanyaan <span class="req">*</span></label>
                            <input type="text" name="pertanyaan" required placeholder="Contoh: Bagaimana cara memperbarui status operasi Rig?">
                        </div>
                        <div class="field">
                            <label>Kategori <span class="req">*</span></label>
                            <select name="kategori" required>
                                <option value="alat_rig">Operasional Rig &amp; Alat</option>
                                <option value="lokasi_peta">Peta &amp; Lapangan</option>
                                <option value="jadwal_gantt">Jadwal Operasi &amp; Gantt</option>
                                <option value="akun_akses">Akun &amp; Hak Akses</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Urutan Tampil (Opsional)</label>
                            <input type="number" name="urutan" min="0" value="0">
                        </div>
                        <div class="field">
                            <label>Jawaban Lengkap <span class="req">*</span></label>
                            <textarea name="jawaban" required placeholder="Tuliskan petunjuk operasional atau penjelasan teknis di sini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-ghost" @click="showCreateModal = false">Batal</button>
                        <button type="submit" class="btn-submit">Simpan FAQ</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit FAQ --}}
        <div class="overlay" :class="{ 'show': showEditModal }" @click.self="showEditModal = false">
            <div class="modal">
                <div class="modal-head">
                    <h3>Edit Butir FAQ</h3>
                    <button type="button" class="modal-close" @click="showEditModal = false">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="field">
                            <label>Pertanyaan <span class="req">*</span></label>
                            <input type="text" name="pertanyaan" x-model="editItem.pertanyaan" required>
                        </div>
                        <div class="field">
                            <label>Kategori <span class="req">*</span></label>
                            <select name="kategori" x-model="editItem.kategori" required>
                                <option value="alat_rig">Operasional Rig &amp; Alat</option>
                                <option value="lokasi_peta">Peta &amp; Lapangan</option>
                                <option value="jadwal_gantt">Jadwal Operasi &amp; Gantt</option>
                                <option value="akun_akses">Akun &amp; Hak Akses</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Urutan Tampil</label>
                            <input type="number" name="urutan" min="0" x-model="editItem.urutan">
                        </div>
                        <div class="field">
                            <label>Jawaban Lengkap <span class="req">*</span></label>
                            <textarea name="jawaban" x-model="editItem.jawaban" required></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-ghost" @click="showEditModal = false">Batal</button>
                        <button type="submit" class="btn-submit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div class="overlay" :class="{ 'show': showDeleteModal }" @click.self="showDeleteModal = false">
            <div class="modal modal-confirm">
                <div class="modal-head">
                    <h3>Konfirmasi Hapus</h3>
                    <button type="button" class="modal-close" @click="showDeleteModal = false">&times;</button>
                </div>
                <form :action="deleteActionUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p style="font-size:13px; color:var(--text-dim); margin:0; line-height:1.5;">
                            Apakah Anda yakin ingin menghapus butir pertanyaan ini:
                        </p>
                        <div style="background:var(--panel-2); border-left:3px solid var(--red); padding:10px 14px; border-radius:6px; font-size:12.5px; font-weight:600; color:var(--text);" x-text="deleteItem.pertanyaan"></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-ghost" @click="showDeleteModal = false">Batal</button>
                        <button type="submit" class="btn-danger">Hapus Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function faqApp() {
            const rawFaqs = @json($faqs);
            const initialCounts = @json($kategoriCounts);

            return {
                faqs: rawFaqs,
                counts: initialCounts,
                searchQuery: '',
                activeCategory: 'semua',
                openItem: rawFaqs.length > 0 ? rawFaqs[0].id : null,

                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,

                editItem: { id: null, pertanyaan: '', jawaban: '', kategori: 'alat_rig', urutan: 0 },
                editActionUrl: '',

                deleteItem: { id: null, pertanyaan: '' },
                deleteActionUrl: '',

                get filteredFaqs() {
                    const q = this.searchQuery.toLowerCase().trim();
                    return this.faqs.filter(item => {
                        const matchCat = this.activeCategory === 'semua' || item.kategori === this.activeCategory;
                        const matchSearch = !q || 
                            item.pertanyaan.toLowerCase().includes(q) || 
                            item.jawaban.toLowerCase().includes(q) ||
                            (item.kategori_label && item.kategori_label.toLowerCase().includes(q));
                        return matchCat && matchSearch;
                    });
                },

                toggleItem(id) {
                    this.openItem = this.openItem === id ? null : id;
                },

                openCreateModal() {
                    this.showCreateModal = true;
                },

                openEditModal(item) {
                    this.editItem = { ...item };
                    this.editActionUrl = `/faq/${item.id}`;
                    this.showEditModal = true;
                },

                openDeleteModal(item) {
                    this.deleteItem = { ...item };
                    this.deleteActionUrl = `/faq/${item.id}`;
                    this.showDeleteModal = true;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
