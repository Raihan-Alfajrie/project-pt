<x-app-layout :title="'Pertamina Admin Panel — Lokasi'" pageTitle="Lokasi" pageDesc="Peta &amp; data kode lapangan operasional">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
    </x-slot>

    @push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        /* ===== Map panel ===== */
        .map-wrap { background:var(--panel); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
        .map-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); flex-wrap:wrap; gap:10px; }
        .map-head h2 { font-size:15.5px; }
        .map-head .hint { font-size:11.5px; color:var(--text-faint); margin-top:2px; }
        .map-controls { display:flex; gap:8px; }
        .map-btn { display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:700; color:var(--text-dim); background:var(--panel-2); border:1px solid var(--border-soft); padding:7px 12px; border-radius:8px; cursor:pointer; user-select:none; }
        .map-btn.on { background:var(--red); color:#fff; border-color:transparent; }
        .map-btn svg { width:13px; height:13px; }
        
        #leafletMap { height: 380px; width: 100%; background: var(--sea); position: relative; z-index: 1; }
        
        .map-legend { display:flex; align-items:center; gap:8px; padding:10px 20px; font-size:11px; color:var(--text-faint); border-top:1px solid var(--border-soft); }
        .map-legend .d { width:9px; height:9px; border-radius:50%; background:var(--red); border:1.5px solid #fff; flex: 0 0 auto; }

        /* Leaflet custom marker */
        .leaflet-marker-icon.custom-pertamina-div {
            background: transparent;
            border: none;
        }
        .pertamina-pin-wrap {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }
        .pertamina-pin-dot {
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: var(--red);
            border: 2px solid #ffffff;
            box-shadow: 0 0 10px rgba(234,32,39,0.85);
            transition: transform .18s ease;
            position: relative;
            z-index: 2;
        }
        .pertamina-pin-wrap:hover .pertamina-pin-dot,
        .pertamina-pin-wrap.active .pertamina-pin-dot {
            transform: scale(1.35);
            background: #ff4757;
            box-shadow: 0 0 14px rgba(234,32,39,1);
        }
        .pertamina-pin-pulse {
            position: absolute;
            top: -5px;
            left: -5px;
            width: 23px;
            height: 23px;
            border-radius: 50%;
            border: 1.5px solid var(--red);
            opacity: 0.65;
            animation: pinPulse 2.2s cubic-bezier(0, 0, 0.2, 1) infinite;
            z-index: 1;
            pointer-events: none;
        }
        @keyframes pinPulse {
            0% { transform: scale(0.7); opacity: 0.9; }
            100% { transform: scale(2.2); opacity: 0; }
        }
        .pertamina-pin-label {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10.5px;
            font-weight: 700;
            color: #ffffff;
            background: rgba(33, 35, 38, 0.9);
            border: 1px solid rgba(234,32,39,0.45);
            padding: 2px 6px;
            border-radius: 5px;
            margin-left: 7px;
            white-space: nowrap;
            letter-spacing: .03em;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
            pointer-events: none;
        }

        /* Leaflet UI theme adjustments */
        .leaflet-container {
            font-family: 'Inter', system-ui, sans-serif !important;
            background: var(--sea) !important;
        }
        .leaflet-popup-content-wrapper {
            background: var(--panel) !important;
            color: var(--text) !important;
            border: 1px solid var(--border);
            border-radius: 12px !important;
            box-shadow: 0 16px 36px rgba(0,0,0,0.55) !important;
        }
        .leaflet-popup-tip {
            background: var(--panel) !important;
        }
        .leaflet-popup-content {
            margin: 12px 16px !important;
            font-size: 12.5px !important;
            line-height: 1.5 !important;
        }
        .leaflet-bar {
            border: 1px solid var(--border-soft) !important;
            border-radius: 8px !important;
            overflow: hidden;
            box-shadow: none !important;
        }
        .leaflet-bar a {
            background-color: var(--panel-2) !important;
            border-bottom: 1px solid var(--border-soft) !important;
            color: var(--text) !important;
        }
        .leaflet-bar a:hover {
            background-color: var(--red-soft) !important;
            color: #ffffff !important;
        }
        .leaflet-control-attribution {
            background: rgba(33, 35, 38, 0.75) !important;
            color: var(--text-faint) !important;
            font-size: 9.5px !important;
        }
        .leaflet-control-attribution a {
            color: var(--text-dim) !important;
        }

        /* ===== Toolbar & Table (from prototype) ===== */
        .sec-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
        .sec-head h2 { font-size:15.5px; }
        .sec-head .hint { font-size:11.5px; color:var(--text-faint); margin-top:2px; }
        .toolbar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .search { display:flex; align-items:center; gap:7px; background:var(--panel); border:1px solid var(--border); border-radius:9px; padding:8px 12px; }
        .search svg { width:14px; height:14px; color:var(--text-faint); }
        .search input { background:transparent; border:0; outline:0; color:var(--text); font-size:12.5px; width:180px; font-family:inherit; }
        .select { background:var(--panel); border:1px solid var(--border); color:var(--text-dim); font-size:12px; padding:8px 11px; border-radius:9px; font-family:inherit; cursor:pointer; }
        .btn-primary { display:flex; align-items:center; gap:7px; background:var(--red); color:#fff; font-size:12.5px; font-weight:700; padding:9px 15px; border-radius:9px; cursor:pointer; border:0; font-family:inherit; }
        .btn-primary svg { width:14px; height:14px; }
        .btn-primary:hover { background:var(--red-strong); }

        .table-wrap { background:var(--panel); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
        .table-scroll { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        thead th { position:sticky; top:0; background:var(--panel-2); text-align:left; font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase; letter-spacing:.05em; padding:12px 14px; border-bottom:1px solid var(--border); white-space:nowrap; }
        tbody td { padding:11px 14px; font-size:12.5px; border-bottom:1px solid var(--border-soft); white-space:nowrap; }
        tbody tr:last-child td { border-bottom:0; }
        tbody tr { transition:background .15s; }
        tbody tr:hover, tbody tr.highlight { background:var(--panel-2); }
        .kode-chip { font-family:'IBM Plex Mono',monospace; font-weight:700; font-size:12px; background:var(--panel-3); color:var(--text); padding:4px 9px; border-radius:7px; letter-spacing:.03em; }
        .count-pill { font-size:11.5px; color:var(--text-dim); }
        .count-pill b { color:var(--text); }
        .row-actions { display:flex; align-items:center; gap:4px; }
        .act-btn { width:29px; height:29px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; background:var(--panel-3); border:1px solid var(--border-soft); }
        .act-btn svg { width:14px; height:14px; color:var(--text-dim); }
        .act-btn:hover { background:var(--red-soft); }
        .act-btn.danger:hover svg { color:#ff6b6f; }

        /* ===== Modal styling ===== */
        .overlay { position:fixed; inset:0; background:rgba(10,8,9,.6); backdrop-filter:blur(2px); display:none; align-items:center; justify-content:center; z-index:100; padding:20px; }
        .overlay.show { display:flex; }
        .modal { background:var(--panel); border:1px solid var(--border); border-radius:16px; width:100%; max-width:560px; max-height:88vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.5); }
        .modal.modal-detail { max-width:600px; }
        .modal.modal-confirm { max-width:400px; }
        .modal-head { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border-soft); position:sticky; top:0; background:var(--panel); z-index:2; }
        .modal-head h3 { font-size:16px; }
        .modal-close { width:28px; height:28px; border-radius:8px; background:var(--panel-2); display:flex; align-items:center; justify-content:center; cursor:pointer; border:0; }
        .modal-close svg { width:13px; height:13px; color:var(--text-dim); }
        .modal-body { padding:20px 22px; display:flex; flex-direction:column; gap:16px; }
        .modal-foot { display:flex; align-items:center; justify-content:flex-end; gap:10px; padding:16px 22px; border-top:1px solid var(--border-soft); }
        .btn-ghost { padding:9px 16px; border-radius:9px; background:var(--panel-2); color:var(--text-dim); font-size:12.5px; font-weight:700; cursor:pointer; border:1px solid var(--border-soft); font-family:inherit; }
        .btn-ghost:hover { color:#fff; }
        .btn-danger { padding:9px 16px; border-radius:9px; background:var(--red); color:#fff; font-size:12.5px; font-weight:700; cursor:pointer; border:0; font-family:inherit; }
        .btn-danger:hover { background:var(--red-strong); }
        .btn-disabled { padding:9px 16px; border-radius:9px; background:var(--panel-2); color:var(--text-faint); font-size:12.5px; font-weight:700; border:1px dashed var(--border-soft); cursor:not-allowed; font-family:inherit; }
        .btn-link { padding:9px 16px; border-radius:9px; background:var(--panel-2); color:var(--text-dim); font-size:12.5px; font-weight:700; border:1px solid var(--border-soft); cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; }
        .btn-link:hover { color:#fff; border-color:var(--red); }

        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .field { display:flex; flex-direction:column; gap:6px; }
        .field.full { grid-column:1/-1; }
        .field label { font-size:11.5px; font-weight:700; color:var(--text-dim); }
        .field label .req { color:var(--red); }
        .field input, .field select, .field textarea { background:var(--panel-2); border:1px solid var(--border-soft); border-radius:9px; padding:10px 12px; color:var(--text); font-size:12.5px; font-family:inherit; outline:0; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color:var(--red); }
        .field textarea { resize:vertical; min-height:70px; }
        .field .err { font-size:10.5px; color:#ff6b6f; display:none; }
        .field.invalid input, .field.invalid select { border-color:#ff6b6f; }
        .field.invalid .err { display:block; }
        .field .subnote { font-size:10.5px; color:var(--text-faint); }

        .detail-hero { display:flex; gap:14px; align-items:flex-start; }
        .detail-kode { width:60px; height:60px; border-radius:14px; background:var(--panel-3); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; flex:0 0 auto; font-family:'IBM Plex Mono',monospace; font-weight:700; font-size:14px; }
        .detail-hero h4 { font-size:17px; font-family:'Space Grotesk',sans-serif; margin:0; }
        .detail-hero .addr { font-size:12px; color:var(--text-faint); margin-top:3px; line-height:1.5; }
        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px 20px; }
        .dkv .k { font-size:10.5px; color:var(--text-faint); text-transform:uppercase; letter-spacing:.05em; font-weight:700; }
        .dkv .v { font-size:13px; margin-top:4px; font-weight:600; }
        .detail-note { font-size:12.5px; color:var(--text-dim); line-height:1.6; background:var(--panel-2); border-radius:10px; padding:12px 14px; }
        .chip-row { display:flex; flex-wrap:wrap; gap:6px; }
        .mini-chip { font-size:11px; font-weight:600; background:var(--panel-3); color:var(--text-dim); padding:4px 9px; border-radius:99px; border:1px solid var(--border-soft); }

        .alert-banner { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13px; background:rgba(46,204,113,.15); border:1px solid rgba(46,204,113,.4); color:#2ecc71; margin-bottom:12px; }
        .alert-banner.error { background:rgba(234,32,39,.15); border-color:rgba(234,32,39,.4); color:#ff6b6f; }

        @media (max-width:980px){
            .form-grid { grid-template-columns:1fr; }
            .detail-grid { grid-template-columns:1fr; }
        }
    </style>
    @endpush

    <!-- Main Content Area -->
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

        <!-- Peta Interaktif Leaflet -->
        <div class="map-wrap">
            <div class="map-head">
                <div>
                    <h2>Peta Lapangan Operasional</h2>
                    <div class="hint">Titik lokasi interaktif berbasis Leaflet — klik marker untuk melihat detail dan sorot tabel</div>
                </div>
                <div class="map-controls">
                    <div class="map-btn" id="btnZoomFull">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 00-2 2v3M16 3h3a2 2 0 012 2v3M8 21H5a2 2 0 01-2-2v-3M16 21h3a2 2 0 002-2v-3"/></svg>
                        Indonesia
                    </div>
                    <div class="map-btn on" id="btnZoomSumbagsel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                        Fokus Sumbagsel
                    </div>
                </div>
            </div>
            
            <div id="leafletMap"></div>
            
            <div class="map-legend">
                <span class="d"></span>Kode lapangan terdaftar &nbsp;·&nbsp; wilayah kerja operasional WK Zona 4
            </div>
        </div>

        <!-- Section Head & Toolbar -->
        <div class="sec-head">
            <div>
                <h2>Daftar Kode Lapangan</h2>
                <div class="hint" id="resultHint">Menampilkan {{ $lokasis->count() }} kode lapangan</div>
            </div>
            <div class="toolbar">
                <div class="search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="searchInput" placeholder="Cari kode / nama tempat…">
                </div>
                <select class="select" id="filterProvinsi">
                    <option value="">Semua provinsi</option>
                    @foreach ($provinsiList as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>
                @if ($canManage)
                    <button class="btn-primary" id="btnAdd" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                        Tambah Lokasi
                    </button>
                @endif
            </div>
        </div>

        <!-- Tabel Lokasi -->
        <div class="table-wrap">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Lapangan</th>
                            <th>Nama Tempat</th>
                            <th>Provinsi</th>
                            <th>Jml Sumur</th>
                            <th>Jml Rig</th>
                            <th>Tanggal Dipetakan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($lokasis as $index => $lokasi)
                            <tr data-id="{{ $lokasi->id }}"
                                data-kode="{{ $lokasi->kode }}"
                                data-nama="{{ $lokasi->nama_tempat }}"
                                data-provinsi="{{ $lokasi->provinsi }}"
                                data-kota="{{ $lokasi->kota ?? '' }}"
                                data-kecamatan="{{ $lokasi->kecamatan ?? '' }}"
                                data-ket="{{ $lokasi->keterangan ?? '' }}"
                                data-tgl="{{ $lokasi->tanggal_dipetakan ? $lokasi->tanggal_dipetakan->translatedFormat('d M Y') : '—' }}"
                                data-sumur='@json($lokasi->sumur_list)'
                                data-rig='@json($lokasi->rig_list)'>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="kode-chip">{{ $lokasi->kode }}</span></td>
                                <td>{{ $lokasi->nama_tempat }}</td>
                                <td>{{ $lokasi->provinsi }}</td>
                                <td class="count-pill"><b>{{ count($lokasi->sumur_list) }}</b> sumur</td>
                                <td class="count-pill"><b>{{ count($lokasi->rig_list) }}</b> rig</td>
                                <td>{{ $lokasi->tanggal_dipetakan ? $lokasi->tanggal_dipetakan->translatedFormat('d M Y') : '—' }}</td>
                                <td>
                                    <div class="row-actions">
                                        <div class="act-btn" onclick="openDetail({{ $lokasi->id }})" title="Lihat detail">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </div>
                                        @if ($canManage)
                                            <div class="act-btn" onclick="openEdit({{ $lokasi->id }})" title="Edit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                                            </div>
                                            <div class="act-btn danger" onclick="openConfirmDelete({{ $lokasi->id }}, '{{ $lokasi->kode }} — {{ addslashes($lokasi->nama_tempat) }}')" title="Hapus">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"><div style="padding:40px;text-align:center;color:var(--text-faint);font-size:13px">Belum ada data lokasi.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($canManage)
        <!-- Modal: Tambah / Edit Lokasi -->
        <div class="overlay" id="overlayForm">
            <div class="modal">
                <div class="modal-head">
                    <h3 id="formTitle">Tambah Lokasi</h3>
                    <button class="modal-close" onclick="closeOverlay('overlayForm')" type="button" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <form id="formLokasi" method="POST" action="{{ route('lokasi.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="form-grid">
                            <div class="field">
                                <label>Kode Lapangan <span class="req">*</span></label>
                                <input id="fKode" name="kode" required placeholder="cth. KRG" style="text-transform:uppercase" maxlength="20">
                                <span class="err" id="errKode">Kode wajib diisi &amp; unik</span>
                            </div>
                            <div class="field">
                                <label>Nama Tempat <span class="req">*</span></label>
                                <input id="fNama" name="nama_tempat" required placeholder="cth. Lapangan Kuang">
                                <span class="err" id="errNama">Nama tempat wajib diisi</span>
                            </div>
                            <div class="field full">
                                <label>Provinsi <span class="req">*</span></label>
                                <select id="fProvinsi" name="provinsi" required>
                                    @foreach ($provinsiList as $prov)
                                        <option value="{{ $prov }}" {{ $prov === 'Sumatera Selatan' ? 'selected' : '' }}>{{ $prov }}</option>
                                    @endforeach
                                </select>
                                <span class="subnote">Menentukan penempatan titik di peta interaktif</span>
                            </div>
                            <div class="field">
                                <label>Kota / Kabupaten</label>
                                <input id="fKota" name="kota" placeholder="cth. Kab. Muara Enim">
                            </div>
                            <div class="field">
                                <label>Kecamatan</label>
                                <input id="fKecamatan" name="kecamatan" placeholder="cth. Kec. Rambang Dangku">
                            </div>
                            <div class="field full">
                                <label>Keterangan</label>
                                <textarea id="fKeterangan" name="keterangan" placeholder="Catatan tambahan tentang lokasi ini…"></textarea>
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
                    <h3>Hapus Lokasi?</h3>
                    <button class="modal-close" onclick="closeOverlay('overlayConfirm')" type="button" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <form id="formDelete" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p style="font-size:13px;color:var(--text-dim);line-height:1.6;margin:0">
                            Kode lapangan <b id="confirmName" style="color:var(--text)"></b> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
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

    <!-- Modal: Detail Lokasi (Accessible to all authenticated users) -->
    <div class="overlay" id="overlayDetail">
        <div class="modal modal-detail">
            <div class="modal-head">
                <h3>Detail Lokasi</h3>
                <button class="modal-close" onclick="closeOverlay('overlayDetail')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-hero">
                    <div class="detail-kode" id="dKodeBox">—</div>
                    <div>
                        <h4 id="dNama">—</h4>
                        <div class="addr" id="dAlamat">—</div>
                    </div>
                </div>
                <div class="detail-grid">
                    <div class="dkv"><div class="k">Kode Lapangan</div><div class="v" id="dKode">—</div></div>
                    <div class="dkv"><div class="k">Tanggal Dipetakan</div><div class="v" id="dTanggal">—</div></div>
                </div>
                <div>
                    <div class="dkv" style="margin-bottom:8px"><div class="k">Sumur di lokasi ini</div></div>
                    <div class="chip-row" id="dSumurChips"></div>
                </div>
                <div>
                    <div class="dkv" style="margin-bottom:8px"><div class="k">Rig yang beroperasi</div></div>
                    <div class="chip-row" id="dRigChips"></div>
                </div>
                <div>
                    <div class="dkv" style="margin-bottom:8px"><div class="k">Keterangan</div></div>
                    <div class="detail-note" id="dKeterangan">—</div>
                </div>
            </div>
            <div class="modal-foot">
                <a class="btn-link" id="btnLihatJadwal" href="#" title="Buka jadwal terfilter ke kode ini">
                    Lihat di Jadwal
                </a>
                <button class="btn-ghost" onclick="closeOverlay('overlayDetail')" type="button">Tutup</button>
                @if ($canManage)
                    <button class="btn-primary" id="btnDetailEdit" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                        Edit
                    </button>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Modal helpers
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

        // Coordinates lookup for Provinces
        const PROVINSI_COORDS = {
            "Aceh": [5.5, 95.3], "Sumatera Utara": [2.5, 99.0], "Sumatera Barat": [-0.9, 100.4],
            "Riau": [0.5, 101.9], "Kepulauan Riau": [3.9, 108.1], "Jambi": [-1.6, 103.6],
            "Sumatera Selatan": [-3.3, 104.0], "Bengkulu": [-3.8, 102.3], "Lampung": [-4.8, 105.0],
            "Kepulauan Bangka Belitung": [-2.7, 106.4], "DKI Jakarta": [-6.2, 106.8],
            "Jawa Barat": [-6.9, 107.6], "Jawa Tengah": [-7.2, 110.1], "DI Yogyakarta": [-7.8, 110.4],
            "Jawa Timur": [-7.5, 112.2], "Banten": [-6.4, 106.1], "Bali": [-8.4, 115.2],
            "Nusa Tenggara Barat": [-8.6, 117.4], "Nusa Tenggara Timur": [-8.7, 121.1],
            "Kalimantan Barat": [0.0, 109.3], "Kalimantan Tengah": [-1.7, 113.3],
            "Kalimantan Selatan": [-3.1, 115.3], "Kalimantan Timur": [0.5, 116.4],
            "Kalimantan Utara": [3.1, 116.9], "Sulawesi Utara": [1.3, 124.8],
            "Sulawesi Tengah": [-1.4, 121.5], "Sulawesi Selatan": [-3.7, 119.9],
            "Sulawesi Tenggara": [-4.1, 122.5], "Gorontalo": [0.7, 122.4],
            "Sulawesi Barat": [-2.6, 119.2], "Maluku": [-3.2, 130.1],
            "Maluku Utara": [0.8, 127.6], "Papua Barat": [-1.3, 133.0], "Papua": [-4.3, 138.5]
        };

        // Specific field coordinates in South Sumatra (WK Zona 4)
        const FIELD_OFFSETS = {
            "MJ": [-3.42, 104.15],
            "RSS": [-3.28, 103.95],
            "TAP": [-3.55, 104.08],
            "BKB": [-3.35, 104.30],
            "BEL": [-3.25, 104.38],
            "KRG": [-3.58, 104.22],
            "TMB": [-3.45, 104.28],
            "TLJT": [-3.48, 104.32],
            "TTB": [-3.62, 103.88],
            "ABB": [-3.38, 103.75],
            "LVT": [-3.50, 103.92],
            "LBA": [-3.52, 103.85],
            "BRG": [-3.65, 104.35],
            "BKT": [-3.20, 104.10]
        };

        // Initialize Leaflet Map
        const map = L.map('leafletMap', {
            zoomControl: true,
            scrollWheelZoom: true,
            attributionControl: true
        }).setView([-3.45, 104.1], 9);

        // Esri ArcGIS Canvas tile layer with theme detection (No API key required)
        const isDark = !window.matchMedia || !window.matchMedia('(prefers-color-scheme: light)').matches;
        const tileBaseUrl = isDark
            ? 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}'
            : 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}';

        const tileRefUrl = isDark
            ? 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Reference/MapServer/tile/{z}/{y}/{x}'
            : 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Reference/MapServer/tile/{z}/{y}/{x}';

        L.tileLayer(tileBaseUrl, {
            attribution: '&copy; <a href="https://www.esri.com/" target="_blank" rel="noopener">Esri</a> &mdash; Esri, DeLorme, NAVTEQ',
            maxZoom: 16
        }).addTo(map);

        L.tileLayer(tileRefUrl, {
            attribution: '',
            maxZoom: 16
        }).addTo(map);

        const markers = {};
        const rows = document.querySelectorAll('#tableBody tr[data-id]');

        rows.forEach((tr, index) => {
            const id = tr.getAttribute('data-id');
            const kode = tr.getAttribute('data-kode');
            const nama = tr.getAttribute('data-nama');
            const provinsi = tr.getAttribute('data-provinsi');
            const sumur = JSON.parse(tr.getAttribute('data-sumur') || '[]');
            const rig = JSON.parse(tr.getAttribute('data-rig') || '[]');

            // Determine coordinates
            let lat, lon;
            if (FIELD_OFFSETS[kode]) {
                lat = FIELD_OFFSETS[kode][0];
                lon = FIELD_OFFSETS[kode][1];
            } else {
                const base = PROVINSI_COORDS[provinsi] || [-3.3, 104.0];
                // Jitter deterministically
                const angle = ((index * 47) % 360) * (Math.PI / 180);
                const r = 0.08 + ((index % 5) * 0.04);
                lat = base[0] + Math.cos(angle) * r;
                lon = base[1] + Math.sin(angle) * r;
            }

            // Create custom Pertamina DivIcon
            const iconHtml = `
                <div class="pertamina-pin-wrap" id="pin-${id}">
                    <span class="pertamina-pin-pulse"></span>
                    <span class="pertamina-pin-dot"></span>
                    <span class="pertamina-pin-label">${kode}</span>
                </div>
            `;

            const customIcon = L.divIcon({
                html: iconHtml,
                className: 'custom-pertamina-div',
                iconSize: [80, 24],
                iconAnchor: [7, 7]
            });

            const marker = L.marker([lat, lon], { icon: customIcon }).addTo(map);

            const popupContent = `
                <div style="min-width:180px;">
                    <div style="font-weight:700; font-size:13.5px; color:var(--text);">${kode} — ${nama}</div>
                    <div style="font-size:11.5px; color:var(--text-dim); margin-top:2px;">${provinsi}</div>
                    <div style="font-size:11px; color:var(--text-faint); margin-top:6px;">
                        Sumur: <b style="color:var(--text);">${sumur.length}</b> &nbsp;·&nbsp; Rig: <b style="color:var(--text);">${rig.length}</b>
                    </div>
                    <div style="margin-top:10px; border-top:1px solid var(--border-soft); padding-top:6px;">
                        <a href="javascript:void(0)" onclick="openDetail(${id})" style="color:var(--red); font-weight:700; text-decoration:none; font-size:11.5px;">Lihat Detail &raquo;</a>
                    </div>
                </div>
            `;
            marker.bindPopup(popupContent);

            marker.on('click', () => {
                highlightRow(id, true);
            });

            markers[id] = marker;
        });

        function setActiveMarker(id) {
            document.querySelectorAll('.pertamina-pin-wrap').forEach(p => p.classList.remove('active'));
            const pin = document.getElementById(`pin-${id}`);
            if (pin) pin.classList.add('active');
        }

        function highlightRow(id, scroll) {
            document.querySelectorAll('#tableBody tr').forEach(tr => {
                tr.classList.toggle('highlight', tr.getAttribute('data-id') === String(id));
            });
            setActiveMarker(id);
            if (scroll) {
                const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
                if (tr) tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // Row hover activates marker
        document.getElementById('tableBody').addEventListener('mouseover', (e) => {
            const tr = e.target.closest('tr[data-id]');
            if (tr) {
                const id = tr.getAttribute('data-id');
                setActiveMarker(id);
            }
        });

        // Map zoom controls
        const btnZoomSumbagsel = document.getElementById('btnZoomSumbagsel');
        const btnZoomFull = document.getElementById('btnZoomFull');

        if (btnZoomSumbagsel) {
            btnZoomSumbagsel.addEventListener('click', () => {
                map.setView([-3.45, 104.1], 9);
                btnZoomSumbagsel.classList.add('on');
                btnZoomFull.classList.remove('on');
            });
        }
        if (btnZoomFull) {
            btnZoomFull.addEventListener('click', () => {
                map.setView([-2.5, 118.0], 5);
                btnZoomFull.classList.add('on');
                btnZoomSumbagsel.classList.remove('on');
            });
        }

        // Search & Filter
        const searchInput = document.getElementById('searchInput');
        const filterProvinsi = document.getElementById('filterProvinsi');
        const resultHint = document.getElementById('resultHint');

        function filterTable() {
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const prov = (filterProvinsi ? filterProvinsi.value : '');
            let visibleCount = 0;

            rows.forEach(tr => {
                const kode = (tr.getAttribute('data-kode') || '').toLowerCase();
                const nama = (tr.getAttribute('data-nama') || '').toLowerCase();
                const trProv = tr.getAttribute('data-provinsi') || '';

                const matchQ = !q || kode.includes(q) || nama.includes(q);
                const matchP = !prov || trProv === prov;

                const isVisible = matchQ && matchP;
                tr.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;

                const id = tr.getAttribute('data-id');
                if (markers[id]) {
                    if (isVisible) {
                        if (!map.hasLayer(markers[id])) markers[id].addTo(map);
                    } else {
                        if (map.hasLayer(markers[id])) map.removeLayer(markers[id]);
                    }
                }
            });

            if (resultHint) {
                resultHint.textContent = `Menampilkan ${visibleCount} dari ${rows.length} kode lapangan`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterTable);
        if (filterProvinsi) filterProvinsi.addEventListener('change', filterTable);

        // Detail Modal
        function openDetail(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const kode = tr.getAttribute('data-kode');
            const nama = tr.getAttribute('data-nama');
            const provinsi = tr.getAttribute('data-provinsi');
            const kota = tr.getAttribute('data-kota');
            const kecamatan = tr.getAttribute('data-kecamatan');
            const ket = tr.getAttribute('data-ket');
            const tgl = tr.getAttribute('data-tgl');
            const sumur = JSON.parse(tr.getAttribute('data-sumur') || '[]');
            const rig = JSON.parse(tr.getAttribute('data-rig') || '[]');

            document.getElementById('dKodeBox').textContent = kode;
            document.getElementById('dKode').textContent = kode;
            document.getElementById('dNama').textContent = nama;

            const addrParts = [kecamatan, kota, provinsi].filter(Boolean);
            document.getElementById('dAlamat').textContent = addrParts.length ? addrParts.join(", ") : provinsi;
            document.getElementById('dTanggal').textContent = tgl;

            const sumurChips = document.getElementById('dSumurChips');
            sumurChips.innerHTML = sumur.length 
                ? sumur.map(s => `<span class="mini-chip">${s}</span>`).join('')
                : '<span class="mini-chip">Belum ada data sumur</span>';

            const rigChips = document.getElementById('dRigChips');
            rigChips.innerHTML = rig.length
                ? rig.map(r => `<span class="mini-chip">${r}</span>`).join('')
                : '<span class="mini-chip">Belum ada rig ditugaskan</span>';

            document.getElementById('dKeterangan').textContent = ket || 'Tidak ada keterangan tambahan.';

            const btnLihatJadwal = document.getElementById('btnLihatJadwal');
            if (btnLihatJadwal) {
                btnLihatJadwal.href = `/jadwal?kode=${encodeURIComponent(kode)}`;
            }

            const btnDetailEdit = document.getElementById('btnDetailEdit');
            if (btnDetailEdit) {
                btnDetailEdit.onclick = function() {
                    closeOverlay('overlayDetail');
                    openEdit(id);
                };
            }

            highlightRow(id, false);
            if (markers[id]) {
                map.panTo(markers[id].getLatLng());
                markers[id].openPopup();
            }

            openOverlay('overlayDetail');
        }

        @if ($canManage)
        // Add & Edit Modals
        const btnAdd = document.getElementById('btnAdd');
        if (btnAdd) {
            btnAdd.addEventListener('click', function() {
                const form = document.getElementById('formLokasi');
                form.action = "{{ route('lokasi.store') }}";
                document.getElementById('formMethod').value = "POST";
                document.getElementById('formTitle').textContent = "Tambah Lokasi";

                document.getElementById('fKode').value = '';
                document.getElementById('fNama').value = '';
                document.getElementById('fProvinsi').value = 'Sumatera Selatan';
                document.getElementById('fKota').value = '';
                document.getElementById('fKecamatan').value = '';
                document.getElementById('fKeterangan').value = '';

                document.querySelectorAll('#overlayForm .field').forEach(f => f.classList.remove('invalid'));
                openOverlay('overlayForm');
            });
        }

        function openEdit(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const form = document.getElementById('formLokasi');
            form.action = `/lokasi/${id}`;
            document.getElementById('formMethod').value = "PUT";
            document.getElementById('formTitle').textContent = "Edit Lokasi";

            document.getElementById('fKode').value = tr.getAttribute('data-kode');
            document.getElementById('fNama').value = tr.getAttribute('data-nama');
            document.getElementById('fProvinsi').value = tr.getAttribute('data-provinsi');
            document.getElementById('fKota').value = tr.getAttribute('data-kota');
            document.getElementById('fKecamatan').value = tr.getAttribute('data-kecamatan');
            document.getElementById('fKeterangan').value = tr.getAttribute('data-ket');

            document.querySelectorAll('#overlayForm .field').forEach(f => f.classList.remove('invalid'));
            closeOverlay('overlayDetail');
            openOverlay('overlayForm');
        }

        // Delete Confirm Modal
        function openConfirmDelete(id, name) {
            const form = document.getElementById('formDelete');
            form.action = `/lokasi/${id}`;
            document.getElementById('confirmName').textContent = name;
            openOverlay('overlayConfirm');
        }
        @endif

        // Deep-link check for ?kode=XXX
        (function checkQuery() {
            const params = new URLSearchParams(window.location.search);
            const kode = params.get('kode');
            if (kode) {
                const matchTr = document.querySelector(`#tableBody tr[data-kode="${kode.toUpperCase()}"]`);
                if (matchTr) {
                    const id = matchTr.getAttribute('data-id');
                    setTimeout(() => {
                        openDetail(id);
                    }, 400);
                }
            }
        })();
    </script>
    @endpush
</x-app-layout>
