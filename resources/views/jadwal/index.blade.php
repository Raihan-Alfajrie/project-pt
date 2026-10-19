<x-app-layout :title="'Pertamina Admin Panel — Jadwal'" pageTitle="Jadwal" pageDesc="Jadwal &amp; progres pekerjaan sumur per rig">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
    </x-slot>

    @push('styles')
    <style>
        /* ===== Status cards ===== */
        .kpi-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .kpi { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; cursor: pointer; transition: border-color .15s, transform .1s; user-select: none; }
        .kpi:hover { transform: translateY(-1px); }
        .kpi.on { border-color: var(--red); box-shadow: 0 0 0 1px var(--red); }
        .kpi .top { display: flex; align-items: center; justify-content: space-between; }
        .kpi .lbl { font-size: 12px; color: var(--text-dim); font-weight: 600; }
        .kpi .icon { width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center; background: var(--red-soft); }
        .kpi .icon svg { width: 15px; height: 15px; color: var(--red); }
        .kpi.selesai .icon { background: rgba(62,199,118,.16); } .kpi.selesai .icon svg { color: #4ade80; }
        .kpi.kendala .icon { background: rgba(234,32,39,.18); } .kpi.kendala .icon svg { color: #ff6b6f; }
        .kpi .val { font-size: 32px; font-weight: 700; margin-top: 12px; font-family: 'Space Grotesk', sans-serif; }
        .kpi .foot { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; }

        /* ===== Toolbar ===== */
        .sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .sec-head h2 { font-size: 15.5px; }
        .sec-head .hint { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .search { display: flex; align-items: center; gap: 7px; background: var(--panel); border: 1px solid var(--border); border-radius: 9px; padding: 8px 12px; }
        .search svg { width: 14px; height: 14px; color: var(--text-faint); }
        .search input { background: transparent; border: 0; outline: 0; color: var(--text); font-size: 12.5px; width: 170px; font-family: inherit; }
        .select { background: var(--panel); border: 1px solid var(--border); color: var(--text-dim); font-size: 12px; padding: 8px 11px; border-radius: 9px; font-family: inherit; cursor: pointer; }
        .btn-primary { display: flex; align-items: center; gap: 7px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; padding: 9px 15px; border-radius: 9px; cursor: pointer; border: 0; font-family: inherit; }
        .btn-primary svg { width: 14px; height: 14px; }
        .btn-primary:hover { background: var(--red-strong); }
        .chip-filter { display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 600; color: var(--red); background: var(--red-soft); padding: 6px 11px; border-radius: 99px; cursor: pointer; user-select: none; }
        .chip-filter svg { width: 11px; height: 11px; }

        /* ===== Gantt Chart ===== */
        .gantt-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .gantt-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .gantt { display: grid; grid-template-columns: 190px repeat(19, 92px) 74px 74px; min-width: max-content; }
        .g-head { display: contents; }
        .g-head > div { position: sticky; top: 0; z-index: 5; background: var(--panel-2); border-bottom: 1px solid var(--border); font-size: 10.5px; font-weight: 700; color: var(--text-dim); padding: 10px 8px; text-align: center; }
        .g-head .corner { position: sticky; left: 0; z-index: 6; text-align: left; padding-left: 14px; background: var(--panel-2); }
        .g-head .now { color: var(--red); }
        .g-head .plancol { background: var(--panel-3); }
        .g-row { display: contents; }
        .g-rig { position: sticky; left: 0; background: var(--panel); z-index: 3; border-bottom: 1px solid var(--border-soft); border-right: 1px solid var(--border); padding: 10px 12px; display: flex; flex-direction: column; justify-content: center; gap: 1px; }
        .g-rig .name { font-size: 11.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 165px; }
        .g-rig .num { font-size: 10px; color: var(--text-faint); }
        .g-cell { border-bottom: 1px solid var(--border-soft); border-right: 1px solid var(--border-soft); position: relative; min-height: 52px; }
        .g-cell.now-col { background: rgba(234,32,39,.09); border-right: 1px solid rgba(234,32,39,.4); }
        .g-plan { border-bottom: 1px solid var(--border-soft); background: var(--panel-3); display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 700; font-family: 'IBM Plex Mono', monospace; }
        .g-plan.prognose { color: #f0a93b; }
        .g-plan.plan { color: var(--text-dim); }

        .block { position: absolute; top: 8px; bottom: 8px; border-radius: 7px; padding: 0 8px 0 20px; display: flex; align-items: center; font-size: 10px; font-weight: 700; color: #221417; overflow: hidden; white-space: nowrap; cursor: pointer; border: 1px solid rgba(0,0,0,.15); transition: transform .1s, box-shadow .1s; }
        .block:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.4); z-index: 4; }
        .block.dark { color: #f6f5f4; }
        .block .stag { position: absolute; left: 6px; top: 50%; transform: translateY(-50%); width: 11px; height: 11px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 8px; }
        .block .stag.selesai { background: #2f9e5c; color: #fff; }
        .block .stag.progres { background: #e0980a; color: #fff; }
        .block .stag.kendala { background: #c8141b; color: #fff; }

        .empty-row { padding: 40px; text-align: center; color: var(--text-faint); font-size: 13px; }

        /* ===== Modals ===== */
        .overlay { position: fixed; inset: 0; background: rgba(10,8,9,.6); backdrop-filter: blur(2px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
        .overlay.show { display: flex; }
        .modal { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 560px; max-height: 88vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .modal.modal-detail { max-width: 600px; }
        .modal.modal-confirm { max-width: 400px; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border-soft); position: sticky; top: 0; background: var(--panel); z-index: 2; }
        .modal-head h3 { font-size: 16px; }
        .modal-close { width: 28px; height: 28px; border-radius: 8px; background: var(--panel-2); display: flex; align-items: center; justify-content: center; cursor: pointer; border: 0; }
        .modal-close svg { width: 13px; height: 13px; color: var(--text-dim); }
        .modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 16px; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 22px; border-top: 1px solid var(--border-soft); }
        .btn-ghost { padding: 9px 16px; border-radius: 9px; background: var(--panel-2); color: var(--text-dim); font-size: 12.5px; font-weight: 700; cursor: pointer; border: 1px solid var(--border-soft); font-family: inherit; }
        .btn-ghost:hover { color: #fff; }
        .btn-danger { padding: 9px 16px; border-radius: 9px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; cursor: pointer; border: 0; font-family: inherit; }
        .btn-danger:hover { background: var(--red-strong); }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field.full { grid-column: 1/-1; }
        .field label { font-size: 11.5px; font-weight: 700; color: var(--text-dim); }
        .field label .req { color: var(--red); }
        .field input, .field select, .field textarea { background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 9px; padding: 10px 12px; color: var(--text); font-size: 12.5px; font-family: inherit; outline: 0; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--red); }
        .field textarea { resize: vertical; min-height: 70px; }
        .field .err { font-size: 10.5px; color: #ff6b6f; display: none; }
        .field.invalid input, .field.invalid select { border-color: #ff6b6f; }
        .field.invalid .err { display: block; }

        .detail-hero { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
        .detail-hero h4 { font-size: 16.5px; font-family: 'Space Grotesk', sans-serif; margin: 0; }
        .detail-hero .rig { font-size: 12px; color: var(--text-faint); margin-top: 3px; }
        .status-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 99px; flex: 0 0 auto; }
        .status-chip.selesai { background: rgba(62,199,118,.16); color: #4ade80; }
        .status-chip.progres { background: rgba(240,169,59,.18); color: #f0a93b; }
        .status-chip.kendala { background: rgba(234,32,39,.18); color: #ff6b6f; }
        .status-chip .d { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
        .dkv .k { font-size: 10.5px; color: var(--text-faint); text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
        .dkv .v { font-size: 13px; margin-top: 4px; font-weight: 600; }
        .detail-note { font-size: 12.5px; color: var(--text-dim); line-height: 1.6; background: var(--panel-2); border-radius: 10px; padding: 12px 14px; }

        .alert-banner { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13px; background:rgba(46,204,113,.15); border:1px solid rgba(46,204,113,.4); color:#2ecc71; margin-bottom:12px; }
        .alert-banner.error { background:rgba(234,32,39,.15); border-color:rgba(234,32,39,.4); color:#ff6b6f; }

        @media (max-width: 980px){
            .kpi-row { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
    @endpush

    <!-- Main Content -->
    <div style="display:flex; flex-direction:column; gap:16px;">
        @if (session('success'))
            <div class="alert-banner">
                <svg style="width:16px; height:16px; flex:0 0 auto;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-banner error">
                <svg style="width:16px; height:16px; flex:0 0 auto;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Status Cards / KPI -->
        <div class="kpi-row">
            <div class="kpi selesai" data-status="selesai" id="cardKpiSelesai">
                <div class="top">
                    <span class="lbl">Selesai</span>
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4L19 6"/></svg>
                    </span>
                </div>
                <div class="val" id="kpiSelesai">{{ $countSelesai }}</div>
                <div class="foot">Pekerjaan sumur tuntas</div>
            </div>
            <div class="kpi" data-status="progres" id="cardKpiProgres">
                <div class="top">
                    <span class="lbl">Menunggu / Progres</span>
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    </span>
                </div>
                <div class="val" id="kpiProgres">{{ $countProgres }}</div>
                <div class="foot">Sedang berjalan / menunggu proses</div>
            </div>
            <div class="kpi kendala" data-status="kendala" id="cardKpiKendala">
                <div class="top">
                    <span class="lbl">Kendala</span>
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 18a2 2 0 001.8 3h15a2 2 0 001.8-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                    </span>
                </div>
                <div class="val" id="kpiKendala">{{ $countKendala }}</div>
                <div class="foot">Perlu tindak lanjut segera</div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="sec-head">
            <div>
                <h2>Jadwal &amp; Progres Pekerjaan Sumur</h2>
                <div class="hint" id="resultHint">Klik blok untuk detail · scroll untuk lihat Jun 2026 – Des 2027</div>
            </div>
            <div class="toolbar" id="toolbarRow">
                <div class="search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="searchInput" placeholder="Cari rig atau sumur…">
                </div>
                <select class="select" id="filterKategori">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoris as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->label }}</option>
                    @endforeach
                </select>
                @if ($canManage)
                    <button class="btn-primary" id="btnAdd" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                        Tambah Jadwal
                    </button>
                @endif
                <span class="chip-filter" id="kodeChip" style="{{ $activeKode ? '' : 'display:none;' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    <span id="kodeChipText">{{ $activeKode ? "Lapangan: {$activeKode} ✕" : '' }}</span>
                </span>
            </div>
        </div>

        <!-- Gantt Chart Container -->
        <div class="gantt-wrap">
            <div class="gantt-scroll">
                <div class="gantt" id="ganttGrid"></div>
            </div>
        </div>
    </div>

    @if ($canManage)
        <!-- Modal: Tambah / Edit Jadwal -->
        <div class="overlay" id="overlayForm">
            <div class="modal">
                <div class="modal-head">
                    <h3 id="formTitle">Tambah Jadwal</h3>
                    <button class="modal-close" onclick="closeOverlay('overlayForm')" type="button" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <form id="formJadwal" method="POST" action="{{ route('jadwal.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-grid">
                            <div class="field full">
                                <label>Rig <span class="req">*</span></label>
                                <select id="fRig" name="alat_id" required>
                                    @foreach ($rigs as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field full">
                                <label>Nama Sumur / Pekerjaan <span class="req">*</span></label>
                                <input id="fLabel" name="label" required placeholder="cth. KRG-PK5 (CF 2027)">
                                <span class="err">Wajib diisi</span>
                            </div>
                            <div class="field">
                                <label>Kategori (dari Dokumentasi) <span class="req">*</span></label>
                                <select id="fKategori" name="kategori_id" required>
                                    @foreach ($kategoris as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Status <span class="req">*</span></label>
                                <select id="fStatus" name="status" required>
                                    <option value="selesai">Selesai</option>
                                    <option value="progres" selected>Menunggu / Progres</option>
                                    <option value="kendala">Kendala</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Bulan Mulai <span class="req">*</span></label>
                                <select id="fBulan" name="bulan_index" required>
                                    @foreach ($months as $idx => $m)
                                        <option value="{{ $idx }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Durasi (bulan) <span class="req">*</span></label>
                                <input id="fDurasi" name="durasi_bulan" type="number" min="1" max="19" value="1" required>
                            </div>
                            <div class="field full">
                                <label>Lokasi Lapangan</label>
                                <select id="fLokasi" name="lokasi_id">
                                    <option value="">(Sesuai lokasi rig)</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}">{{ $lok->kode }} — {{ $lok->nama_tempat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field full">
                                <label>Catatan</label>
                                <textarea id="fCatatan" name="catatan" placeholder="Catatan tambahan…"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button class="btn-ghost" onclick="closeOverlay('overlayForm')" type="button">Batal</button>
                        <button class="btn-primary" type="submit" id="btnSave">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Konfirmasi Hapus -->
        <div class="overlay" id="overlayConfirm">
            <div class="modal modal-confirm">
                <div class="modal-head">
                    <h3>Hapus Jadwal?</h3>
                    <button class="modal-close" onclick="closeOverlay('overlayConfirm')" type="button" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <form id="formDelete" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p style="font-size:13px; color:var(--text-dim); line-height:1.6; margin:0">
                            Item jadwal <b id="confirmName" style="color:var(--text)"></b> akan dihapus permanen dari Gantt. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="modal-foot">
                        <button class="btn-ghost" onclick="closeOverlay('overlayConfirm')" type="button">Batal</button>
                        <button class="btn-danger" type="submit">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal: Detail Jadwal (Accessible to all authenticated users) -->
    <div class="overlay" id="overlayDetail">
        <div class="modal modal-detail">
            <div class="modal-head">
                <h3>Detail Jadwal</h3>
                <button class="modal-close" onclick="closeOverlay('overlayDetail')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-hero">
                    <div>
                        <h4 id="dLabel">—</h4>
                        <div class="rig" id="dRig">—</div>
                    </div>
                    <div id="dStatusChip"></div>
                </div>
                <div class="detail-grid">
                    <div class="dkv"><div class="k">Kategori</div><div class="v" id="dKategori">—</div></div>
                    <div class="dkv"><div class="k">Periode</div><div class="v" id="dPeriode">—</div></div>
                    <div class="dkv" style="grid-column:1/-1;"><div class="k">Lokasi Lapangan</div><div class="v" id="dLokasi">—</div></div>
                </div>
                <div>
                    <div class="dkv" style="margin-bottom:8px"><div class="k">Catatan</div></div>
                    <div class="detail-note" id="dCatatan">—</div>
                </div>
            </div>
            <div class="modal-foot">
                @if ($canManage)
                    <button class="btn-danger" id="btnDetailDelete" type="button" style="margin-right:auto;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px; margin-right:5px;"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg>
                        Hapus
                    </button>
                    <button class="btn-primary" id="btnDetailEdit" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                        Edit
                    </button>
                @endif
                <button class="btn-ghost" onclick="closeOverlay('overlayDetail')" type="button">Tutup</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const MONTHS = @json($months);
        const NOW_COL = 1; // Jul-26

        // Rigs array
        const RIGS = @json($rigsData, JSON_UNESCAPED_SLASHES);

        // Items array
        let ITEMS = @json($jadwalsData, JSON_UNESCAPED_SLASHES);

        let activeStatusFilter = null;
        let activeKodeFilter = @json($activeKode);
        if (activeKodeFilter) activeKodeFilter = activeKodeFilter.toUpperCase();

        function statusLabel(s) {
            return s === "selesai" ? "Selesai" : (s === "kendala" ? "Kendala" : "Menunggu / Progres");
        }
        function stagIcon(s) {
            return s === "selesai" ? "✓" : (s === "kendala" ? "!" : "…");
        }

        // Overlay helpers
        function openOverlay(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('show');
        }
        function closeOverlay(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('show');
        }

        document.querySelectorAll('.overlay').forEach(ov => {
            ov.addEventListener('click', (e) => {
                if (e.target === ov) closeOverlay(ov.id);
            });
        });

        // KPI card click filter
        document.querySelectorAll('.kpi[data-status]').forEach(el => {
            el.addEventListener('click', () => {
                activeStatusFilter = activeStatusFilter === el.dataset.status ? null : el.dataset.status;
                updateKPIHighlight();
                renderGantt();
            });
        });

        function updateKPIHighlight() {
            document.querySelectorAll('.kpi[data-status]').forEach(el => {
                el.classList.toggle('on', el.dataset.status === activeStatusFilter);
            });
        }

        // Filter and Search logic
        const searchInput = document.getElementById('searchInput');
        const filterKategori = document.getElementById('filterKategori');
        const kodeChip = document.getElementById('kodeChip');
        const kodeChipText = document.getElementById('kodeChipText');

        if (kodeChip) {
            kodeChip.addEventListener('click', () => {
                activeKodeFilter = null;
                kodeChip.style.display = 'none';
                const url = new URL(window.location.href);
                url.searchParams.delete('kode');
                window.history.replaceState({}, '', url);
                renderGantt();
            });
        }

        function getFiltered() {
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const kat = (filterKategori ? filterKategori.value : '');

            return ITEMS.filter(it => {
                const matchQ = !q || it.rig.toLowerCase().includes(q) || it.label.toLowerCase().includes(q);
                const matchKat = !kat || String(it.kategori_id) === String(kat);
                const matchStatus = !activeStatusFilter || it.status === activeStatusFilter;
                const matchKode = !activeKodeFilter || (it.lokasi_kode && it.lokasi_kode.toUpperCase() === activeKodeFilter);
                return matchQ && matchKat && matchStatus && matchKode;
            });
        }

        function renderGantt() {
            const filtered = getFiltered();
            const grid = document.getElementById('ganttGrid');
            grid.innerHTML = "";
            grid.style.gridTemplateColumns = `190px repeat(${MONTHS.length}, 92px) 74px 74px`;

            // Header
            const head = document.createElement('div');
            head.className = "g-head";

            const corner = document.createElement('div');
            corner.className = "corner";
            corner.textContent = "Rig";
            head.appendChild(corner);

            MONTHS.forEach((m, i) => {
                const d = document.createElement('div');
                d.textContent = m;
                if (i === NOW_COL) d.classList.add('now');
                head.appendChild(d);
            });

            const p1 = document.createElement('div');
            p1.className = "plancol";
            p1.textContent = "Plan";
            head.appendChild(p1);

            const p2 = document.createElement('div');
            p2.className = "plancol";
            p2.textContent = "Prognose";
            head.appendChild(p2);

            grid.appendChild(head);

            let anyVisible = false;

            RIGS.forEach((rigObj, idx) => {
                const rigName = rigObj.nama;
                const rigItems = filtered.filter(it => it.alat_id === rigObj.id || it.rig === rigName);

                if (rigItems.length === 0 && (activeStatusFilter || searchInput.value || filterKategori.value || activeKodeFilter)) {
                    return;
                }

                anyVisible = true;
                const row = document.createElement('div');
                row.className = "g-row";

                const rig = document.createElement('div');
                rig.className = "g-rig";
                rig.title = rigName;
                rig.innerHTML = `<div class="name">${rigName}</div><div class="num">Rig ${idx + 1}</div>`;
                row.appendChild(rig);

                for (let i = 0; i < MONTHS.length; i++) {
                    const cell = document.createElement('div');
                    cell.className = "g-cell";
                    if (i === NOW_COL) cell.classList.add('now-col');
                    row.appendChild(cell);
                }

                rigItems.forEach(it => {
                    const cellIndex = 1 + it.s;
                    const targetCell = row.children[cellIndex];
                    if (!targetCell) return;

                    const block = document.createElement('div');
                    block.className = "block";
                    block.style.background = it.kategori_warna;
                    block.style.width = `calc(${it.n * 92 - 6}px)`;
                    block.style.left = "3px";

                    // Text contrast
                    if (['#221417', '#5a3438', '#1a1113'].includes(it.kategori_warna.toLowerCase())) {
                        block.classList.add('dark');
                    }

                    if (it.kategori_gaya === 'dashed') {
                        block.style.border = '2px dashed rgba(255,255,255,0.7)';
                    }

                    block.innerHTML = `<span class="stag ${it.status}">${stagIcon(it.status)}</span><span>${it.label}</span>`;
                    block.title = `${it.label} (${it.kategori_nama}) — ${statusLabel(it.status)}`;
                    block.addEventListener('click', () => openDetail(it.id));
                    targetCell.appendChild(block);
                });

                const allRigItems = ITEMS.filter(it => it.alat_id === rigObj.id || it.rig === rigName);
                const plan = 6;
                const prognose = Math.max(1, Math.round(allRigItems.length * 2.2));

                const pc = document.createElement('div');
                pc.className = "g-plan plan";
                pc.textContent = plan;
                row.appendChild(pc);

                const pg = document.createElement('div');
                pg.className = "g-plan prognose";
                pg.textContent = prognose;
                row.appendChild(pg);

                grid.appendChild(row);
            });

            const resultHint = document.getElementById('resultHint');
            if (resultHint) {
                resultHint.textContent = anyVisible
                    ? `Menampilkan ${filtered.length} dari ${ITEMS.length} item jadwal`
                    : "Tidak ada data yang cocok.";
            }

            if (!anyVisible) {
                grid.innerHTML += `<div style="grid-column:1/-1"><div class="empty-row">Tidak ada item jadwal yang cocok dengan pencarian/filter.</div></div>`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', renderGantt);
        if (filterKategori) filterKategori.addEventListener('change', renderGantt);

        // Detail Modal
        function openDetail(id) {
            const it = ITEMS.find(x => x.id === id);
            if (!it) return;

            document.getElementById('dLabel').textContent = it.label;
            document.getElementById('dRig').textContent = it.rig;

            const chip = document.getElementById('dStatusChip');
            chip.innerHTML = `<span class="status-chip ${it.status}"><span class="d"></span>${statusLabel(it.status)}</span>`;

            document.getElementById('dKategori').textContent = it.kategori_nama;
            const endIdx = Math.min(it.s + it.n - 1, MONTHS.length - 1);
            document.getElementById('dPeriode').textContent = `${MONTHS[it.s]} — ${MONTHS[endIdx]} (${it.n} bulan)`;

            const lokasiText = it.lokasi_kode 
                ? `${it.lokasi_kode} — ${it.lokasi_nama || 'Lapangan Operasional'}`
                : 'Belum ditentukan';
            document.getElementById('dLokasi').textContent = lokasiText;

            document.getElementById('dCatatan').textContent = it.catatan || "Tidak ada catatan tambahan.";

            const btnDetailEdit = document.getElementById('btnDetailEdit');
            if (btnDetailEdit) {
                btnDetailEdit.onclick = () => {
                    closeOverlay('overlayDetail');
                    openEdit(id);
                };
            }

            const btnDetailDelete = document.getElementById('btnDetailDelete');
            if (btnDetailDelete) {
                btnDetailDelete.onclick = () => {
                    closeOverlay('overlayDetail');
                    openConfirmDelete(id);
                };
            }

            openOverlay('overlayDetail');
        }

        @if ($canManage)
        // Add & Edit Modals
        const btnAdd = document.getElementById('btnAdd');
        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                const form = document.getElementById('formJadwal');
                form.action = "{{ route('jadwal.store') }}";
                document.getElementById('formMethod').value = "POST";
                document.getElementById('formTitle').textContent = "Tambah Jadwal";

                if (RIGS.length > 0) document.getElementById('fRig').value = RIGS[0].id;
                document.getElementById('fLabel').value = "";
                document.getElementById('fStatus').value = "progres";
                document.getElementById('fBulan').value = "0";
                document.getElementById('fDurasi').value = "1";
                document.getElementById('fLokasi').value = "";
                document.getElementById('fCatatan').value = "";

                openOverlay('overlayForm');
            });
        }

        function openEdit(id) {
            const it = ITEMS.find(x => x.id === id);
            if (!it) return;

            const form = document.getElementById('formJadwal');
            form.action = `/jadwal/${id}`;
            document.getElementById('formMethod').value = "PUT";
            document.getElementById('formTitle').textContent = "Edit Jadwal";

            document.getElementById('fRig').value = it.alat_id;
            document.getElementById('fLabel').value = it.label;
            document.getElementById('fKategori').value = it.kategori_id;
            document.getElementById('fStatus').value = it.status;
            document.getElementById('fBulan').value = String(it.s);
            document.getElementById('fDurasi').value = it.n;
            document.getElementById('fLokasi').value = it.lokasi_id || "";
            document.getElementById('fCatatan').value = it.catatan || "";

            openOverlay('overlayForm');
        }

        function openConfirmDelete(id) {
            const it = ITEMS.find(x => x.id === id);
            if (!it) return;

            const form = document.getElementById('formDelete');
            form.action = `/jadwal/${id}`;
            document.getElementById('confirmName').textContent = `${it.rig} — ${it.label}`;
            openOverlay('overlayConfirm');
        }
        @endif

        // Initial render
        renderGantt();
    </script>
    @endpush
</x-app-layout>
