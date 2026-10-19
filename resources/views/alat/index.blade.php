<x-app-layout pageTitle="Alat" pageDesc="Kelola data alat &amp; rig operasional">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg>
    </x-slot>

    <style>
        /* ===== kpi status ===== */
        .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .kpi { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; }
        .kpi .top { display: flex; align-items: center; justify-content: space-between; }
        .kpi .lbl { font-size: 12px; color: var(--text-dim); font-weight: 600; }
        .kpi .icon { width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center; background: var(--red-soft); }
        .kpi .icon svg { width: 15px; height: 15px; color: var(--red); }
        .kpi.aktif .icon { background: rgba(62,199,118,.16); } .kpi.aktif .icon svg { color: #4ade80; }
        .kpi.standby .icon { background: rgba(240,169,59,.18); } .kpi.standby .icon svg { color: #f0a93b; }
        .kpi .val { font-size: 32px; font-weight: 700; margin-top: 12px; font-family: 'Space Grotesk', sans-serif; }
        .kpi .foot { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; }

        /* ===== toolbar ===== */
        .sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .sec-head h2 { font-size: 15.5px; }
        .sec-head .hint { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .search { display: flex; align-items: center; gap: 7px; background: var(--panel); border: 1px solid var(--border); border-radius: 9px; padding: 8px 12px; }
        .search svg { width: 14px; height: 14px; color: var(--text-faint); }
        .search input { background: transparent; border: 0; outline: 0; color: var(--text); font-size: 12.5px; width: 170px; font-family: inherit; }
        .select { background: var(--panel); border: 1px solid var(--border); color: var(--text-dim); font-size: 12px; padding: 8px 11px; border-radius: 9px; font-family: inherit; cursor: pointer; outline: 0; }
        .btn-primary { display: flex; align-items: center; gap: 7px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; padding: 9px 15px; border-radius: 9px; cursor: pointer; border: 0; font-family: inherit; transition: background .15s; }
        .btn-primary svg { width: 14px; height: 14px; }
        .btn-primary:hover { background: var(--red-strong); }

        /* ===== table ===== */
        .table-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { position: sticky; top: 0; background: var(--panel-2); text-align: left; font-size: 10.5px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: .05em; padding: 12px 14px; border-bottom: 1px solid var(--border); white-space: nowrap; user-select: none; }
        tbody td { padding: 11px 14px; font-size: 12.5px; border-bottom: 1px solid var(--border-soft); white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--panel-2); }
        .thumb { width: 38px; height: 38px; border-radius: 9px; background: var(--panel-3); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); }
        .thumb svg { width: 18px; height: 18px; color: var(--text-dim); }
        .name-cell .n { font-weight: 700; }
        .name-cell .cat { font-size: 10.5px; color: var(--text-faint); margin-top: 1px; }
        .status-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 99px; }
        .status-chip.aktif { background: rgba(62,199,118,.16); color: #4ade80; }
        .status-chip.standby { background: rgba(240,169,59,.18); color: #f0a93b; }
        .status-chip.bermasalah { background: rgba(234,32,39,.18); color: #ff6b6f; }
        .status-chip .d { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .row-actions { display: flex; align-items: center; gap: 4px; }
        .act-btn { width: 29px; height: 29px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: var(--panel-3); border: 1px solid var(--border-soft); color: var(--text-dim); text-decoration: none; border: 0; }
        .act-btn svg { width: 14px; height: 14px; }
        .act-btn:hover { background: var(--red-soft); color: #fff; }
        .act-btn.danger:hover svg { color: #ff6b6f; }
        .empty-state { padding: 50px 20px; text-align: center; color: var(--text-faint); font-size: 13px; }

        /* ===== modal ===== */
        .overlay { position: fixed; inset: 0; background: rgba(10,8,9,.65); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
        .overlay.show { display: flex; }
        .modal { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 560px; max-height: 88vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .modal.modal-detail { max-width: 640px; }
        .modal.modal-confirm { max-width: 400px; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border-soft); position: sticky; top: 0; background: var(--panel); z-index: 2; }
        .modal-head h3 { font-size: 16px; }
        .modal-close { width: 28px; height: 28px; border-radius: 8px; background: var(--panel-2); display: flex; align-items: center; justify-content: center; cursor: pointer; border: 0; color: var(--text); }
        .modal-close svg { width: 13px; height: 13px; }
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
        .photo-box { display: flex; align-items: center; gap: 12px; background: var(--panel-2); border: 1px dashed var(--border); border-radius: 11px; padding: 12px 14px; }
        .photo-box .ph-icon { width: 44px; height: 44px; border-radius: 10px; background: var(--panel-3); display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
        .photo-box .ph-icon svg { width: 20px; height: 20px; color: var(--text-dim); }
        .photo-box .ph-text { font-size: 11.5px; color: var(--text-faint); line-height: 1.5; }
        .photo-box .ph-text b { color: var(--text-dim); }

        /* detail modal */
        .detail-hero { display: flex; gap: 16px; align-items: center; }
        .detail-photo { width: 84px; height: 84px; border-radius: 14px; background: var(--panel-3); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
        .detail-photo svg { width: 38px; height: 38px; color: var(--text-dim); }
        .detail-hero h4 { font-size: 17px; font-family: 'Space Grotesk', sans-serif; margin: 0; }
        .detail-hero .cat { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
        .dkv .k { font-size: 10.5px; color: var(--text-faint); text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
        .dkv .v { font-size: 13px; margin-top: 4px; font-weight: 600; }
        .detail-note { font-size: 12.5px; color: var(--text-dim); line-height: 1.6; background: var(--panel-2); border-radius: 10px; padding: 12px 14px; }

        .alert-flash { padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; }
        .alert-success { background: rgba(62,199,118,.16); border: 1px solid rgba(62,199,118,.35); color: #4ade80; }
        .alert-error { background: rgba(234,32,39,.18); border: 1px solid rgba(234,32,39,.35); color: #ff6b6f; }

        @media (max-width: 980px) {
            .kpi-row { grid-template-columns: repeat(2, 1fr); }
            .form-grid { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>

    @if (session('success'))
        <div class="alert-flash alert-success" x-data="{ show: true }" x-show="show">
            <span>{{ session('success') }}</span>
            <button @click="show = false" type="button" style="background:transparent; border:0; color:currentColor; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-flash alert-error" x-data="{ show: true }" x-show="show">
            <span>Terdapat kesalahan pengisian form. Silakan periksa kembali data Anda.</span>
            <button @click="show = false" type="button" style="background:transparent; border:0; color:currentColor; cursor:pointer; font-size:16px;">&times;</button>
        </div>
    @endif

    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi">
            <div class="top"><span class="lbl">Total Alat</span><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/></svg></span></div>
            <div class="val" id="kpiTotal">{{ $totalAlat }}</div>
            <div class="foot">Rig &amp; alat pendukung</div>
        </div>
        <div class="kpi aktif">
            <div class="top"><span class="lbl">Aktif</span><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4L19 6"/></svg></span></div>
            <div class="val" id="kpiAktif">{{ $alatAktif }}</div>
            <div class="foot">Beroperasi normal</div>
        </div>
        <div class="kpi standby">
            <div class="top"><span class="lbl">Tidak Aktif</span><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></span></div>
            <div class="val" id="kpiStandby">{{ $alatStandby }}</div>
            <div class="foot">Standby / belum digunakan</div>
        </div>
        <div class="kpi">
            <div class="top"><span class="lbl">Bermasalah</span><span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 18a2 2 0 001.8 3h15a2 2 0 001.8-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg></span></div>
            <div class="val" id="kpiMasalah">{{ $alatBermasalah }}</div>
            <div class="foot">Perlu tindak lanjut</div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="sec-head">
        <div>
            <h2>Daftar Alat</h2>
            <div class="hint" id="resultHint">Menampilkan {{ $alats->count() }} alat</div>
        </div>
        <div class="toolbar">
            <div class="search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                <input id="searchInput" placeholder="Cari nama, merek, no. seri…">
            </div>
            <select class="select" id="filterKategori">
                <option value="">Semua kategori</option>
                <option value="rig">Rig</option>
                <option value="alat_pendukung">Alat Pendukung</option>
            </select>
            <select class="select" id="filterStatus">
                <option value="">Semua status</option>
                <option value="aktif">Aktif</option>
                <option value="standby">Tidak Aktif</option>
                <option value="bermasalah">Bermasalah</option>
            </select>
            @if ($canManage)
                <button class="btn-primary" id="btnAdd" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Alat
                </button>
            @endif
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-wrap">
        <div class="table-scroll">
            <table id="alatTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Alat</th>
                        <th>Merek</th>
                        <th>Model / Tipe</th>
                        <th>No. Seri</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse ($alats as $index => $a)
                        <tr data-id="{{ $a->id }}"
                            data-nama="{{ strtolower($a->nama) }}"
                            data-merek="{{ strtolower($a->merek) }}"
                            data-model="{{ strtolower($a->model ?? '') }}"
                            data-noseri="{{ strtolower($a->no_seri) }}"
                            data-kategori="{{ $a->kategori }}"
                            data-status="{{ $a->status }}"
                            data-tahun="{{ $a->tahun_perolehan ?? '' }}"
                            data-lokasi-id="{{ $a->lokasi_id ?? '' }}"
                            data-lokasi-nama="{{ $a->lokasi ? $a->lokasi->nama_tempat : '' }}"
                            data-lokasi-kode="{{ $a->lokasi ? $a->lokasi->kode : '' }}"
                            data-ket="{{ $a->keterangan ?? '' }}"
                            data-foto="{{ $a->foto ? asset('storage/' . $a->foto) : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="thumb" style="overflow:hidden;">
                                    @if ($a->foto)
                                        <img src="{{ asset('storage/' . $a->foto) }}" alt="{{ $a->nama }}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            @if ($a->kategori === 'rig')
                                                <circle cx="12" cy="12" r="9"/><path d="M12 6c1.6 2.4 3.2 3.6 3.2 6A3.2 3.2 0 0112 15.2 3.2 3.2 0 018.8 12c0-2.4 1.6-3.6 3.2-6z"/>
                                            @else
                                                <rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10.5" r="1.7"/><path d="M21 15l-4.5-4.5a2 2 0 00-2.8 0L5 19"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="name-cell">
                                    <div class="n">{{ $a->nama }}</div>
                                    <div class="cat">{{ $a->kategori === 'rig' ? 'Rig' : 'Alat Pendukung' }}</div>
                                </div>
                            </td>
                            <td>{{ $a->merek ?? '—' }}</td>
                            <td>{{ $a->model ?? '—' }}</td>
                            <td>{{ $a->no_seri ?? '—' }}</td>
                            <td>{{ $a->tahun_perolehan ?? '—' }}</td>
                            <td>
                                @if ($a->status === 'aktif')
                                    <span class="status-chip aktif"><span class="d"></span>Aktif</span>
                                @elseif ($a->status === 'standby')
                                    <span class="status-chip standby"><span class="d"></span>Tidak Aktif</span>
                                @else
                                    <span class="status-chip bermasalah"><span class="d"></span>Bermasalah</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button class="act-btn" type="button" title="Lihat detail" onclick="openDetail({{ $a->id }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    @if ($canManage)
                                        <button class="act-btn" type="button" title="Edit" onclick="openEdit({{ $a->id }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                                        </button>
                                    @endif

                                    @if ($a->lokasi)
                                        <a class="act-btn" href="{{ route('lokasi.index', ['kode' => $a->lokasi->kode]) }}" title="Lihat lokasi saat ini ({{ $a->lokasi->kode }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
                                        </a>
                                    @endif

                                    @if ($canManage)
                                        <button class="act-btn danger" type="button" title="Hapus" onclick="openConfirmDelete({{ $a->id }}, '{{ addslashes($a->nama) }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">Belum ada data alat terdaftar di database.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($canManage)
        <!-- Modal: Tambah/Edit -->
        <div class="overlay" id="overlayForm">
            <div class="modal">
                <div class="modal-head">
                    <h3 id="formTitle">Tambah Alat</h3>
                    <button class="modal-close" onclick="closeOverlay('overlayForm')" type="button" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <form id="formAlat" method="POST" action="{{ route('alat.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="photo-box" style="display:flex; align-items:center; gap:14px; background:var(--panel-2); border:1px solid var(--border-soft); border-radius:12px; padding:12px 14px;">
                            <div class="ph-icon" id="phIconPreview" style="width:48px; height:48px; border-radius:10px; background:var(--panel-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                                <img id="formFotoPreview" src="" alt="Pratinjau Foto" style="display:none; width:100%; height:100%; object-fit:cover;">
                                <svg id="formFotoPlaceholderSvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:22px; height:22px; color:var(--text-dim);"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10.5" r="1.7"/><path d="M21 15l-4.5-4.5a2 2 0 00-2.8 0L5 19"/></svg>
                            </div>
                            <div style="flex:1;">
                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
                                    <label for="fFotoAlat" class="btn-ghost" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; padding:6px 12px;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px; height:13px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span>Upload Foto Alat</span>
                                    </label>
                                    <input type="file" name="foto" id="fFotoAlat" accept="image/jpeg,image/png,image/webp" style="display:none;">
                                    <button type="button" id="btnHapusFotoAlat" style="display:none; background:transparent; border:0; color:#ff6b6f; font-size:11.5px; cursor:pointer; font-weight:600;">Hapus foto</button>
                                </div>
                                <div class="ph-text" style="font-size:11px; color:var(--text-faint); line-height:1.4;">
                                    Format JPG, PNG, atau WEBP (maks. 2MB). Foto akan ditampilkan di tabel dan modal detail.
                                </div>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="field full">
                                <label for="fNama">Nama Alat <span class="req">*</span></label>
                                <input id="fNama" name="nama" required placeholder="cth. PDSI #36.1/SKYTOP650-M">
                            </div>
                            <div class="field">
                                <label for="fKategori">Kategori <span class="req">*</span></label>
                                <select id="fKategori" name="kategori" required>
                                    <option value="rig">Rig</option>
                                    <option value="alat_pendukung">Alat Pendukung</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="fStatus">Status <span class="req">*</span></label>
                                <select id="fStatus" name="status" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="standby">Tidak Aktif</option>
                                    <option value="bermasalah">Bermasalah</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="fMerek">Merek <span class="req">*</span></label>
                                <input id="fMerek" name="merek" required placeholder="cth. National Oilwell Varco">
                            </div>
                            <div class="field">
                                <label for="fModel">Model / Tipe</label>
                                <input id="fModel" name="model" placeholder="cth. NT45-M">
                            </div>
                            <div class="field">
                                <label for="fTahun">Tahun Perolehan</label>
                                <input id="fTahun" name="tahun_perolehan" type="number" min="1980" max="2030" placeholder="cth. 2019">
                            </div>
                            <div class="field">
                                <label for="fNoSeri">Nomor Seri <span class="req">*</span></label>
                                <input id="fNoSeri" name="no_seri" required placeholder="cth. RIG-0016-2019">
                            </div>
                            <div class="field full">
                                <label for="fLokasi">Lokasi Lapangan Bertugas</label>
                                <select id="fLokasi" name="lokasi_id">
                                    <option value="">— Belum ditugaskan (Standby / Pergudangan) —</option>
                                    @foreach ($lokasis as $lok)
                                        <option value="{{ $lok->id }}">{{ $lok->kode }} — {{ $lok->nama_tempat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field full">
                                <label for="fKeterangan">Keterangan</label>
                                <textarea id="fKeterangan" name="keterangan" placeholder="Catatan tambahan tentang alat ini…"></textarea>
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
                    <h3>Hapus Alat?</h3>
                    <button class="modal-close" onclick="closeOverlay('overlayConfirm')" type="button" aria-label="Tutup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <form id="formDelete" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p style="font-size:13px; color:var(--text-dim); line-height:1.6; margin:0">
                            Alat <b id="confirmName" style="color:var(--text)"></b> akan dihapus permanen dari database. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="modal-foot">
                        <button class="btn-ghost" onclick="closeOverlay('overlayConfirm')" type="button">Batal</button>
                        <button class="btn-danger" type="submit">Hapus Alat</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal: Detail Alat (Accessible to everyone) -->
    <div class="overlay" id="overlayDetail">
        <div class="modal modal-detail">
            <div class="modal-head">
                <h3>Detail Alat</h3>
                <button class="modal-close" onclick="closeOverlay('overlayDetail')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-hero">
                    <div class="detail-photo" id="detailPhotoWrap" style="overflow:hidden; position:relative; width:64px; height:64px; border-radius:14px; background:var(--panel-2); display:flex; align-items:center; justify-content:center;">
                        <img id="detailImg" src="" alt="Foto Alat" style="display:none; width:100%; height:100%; object-fit:cover;">
                        <svg id="detailIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" style="width:30px; height:30px;">
                            <circle cx="12" cy="12" r="9"/><path d="M12 6c1.6 2.4 3.2 3.6 3.2 6A3.2 3.2 0 0112 15.2 3.2 3.2 0 018.8 12c0-2.4 1.6-3.6 3.2-6z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 id="dNama">—</h4>
                        <div class="cat" id="dKategori">—</div>
                        <div style="margin-top:8px" id="dStatusChip"></div>
                    </div>
                </div>
                <div class="detail-grid">
                    <div class="dkv"><div class="k">Merek</div><div class="v" id="dMerek">—</div></div>
                    <div class="dkv"><div class="k">Model / Tipe</div><div class="v" id="dModel">—</div></div>
                    <div class="dkv"><div class="k">Nomor Seri</div><div class="v" id="dNoSeri">—</div></div>
                    <div class="dkv"><div class="k">Tahun Perolehan</div><div class="v" id="dTahun">—</div></div>
                    <div class="dkv" style="grid-column: 1/-1;"><div class="k">Lokasi Lapangan</div><div class="v" id="dLokasi">—</div></div>
                </div>
                <div>
                    <div class="dkv" style="margin-bottom:8px"><div class="k">Keterangan</div></div>
                    <div class="detail-note" id="dKeterangan">—</div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-ghost" onclick="closeOverlay('overlayDetail')" type="button">Tutup</button>
                @if ($canManage)
                    <button class="btn-primary" id="btnDetailEdit" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                        Edit Alat
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script>
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

        // Search & Filter
        const searchInput = document.getElementById('searchInput');
        const filterKategori = document.getElementById('filterKategori');
        const filterStatus = document.getElementById('filterStatus');
        const resultHint = document.getElementById('resultHint');

        function filterAlatTable() {
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const kat = (filterKategori ? filterKategori.value : '');
            const st = (filterStatus ? filterStatus.value : '');
            const rows = document.querySelectorAll('#tableBody tr[data-id]');
            let visibleCount = 0;

            rows.forEach(tr => {
                const nama = tr.getAttribute('data-nama') || '';
                const merek = tr.getAttribute('data-merek') || '';
                const noseri = tr.getAttribute('data-noseri') || '';
                const kategori = tr.getAttribute('data-kategori') || '';
                const status = tr.getAttribute('data-status') || '';

                const matchQ = !q || nama.includes(q) || merek.includes(q) || noseri.includes(q);
                const matchKat = !kat || kategori === kat;
                const matchSt = !st || status === st;

                if (matchQ && matchKat && matchSt) {
                    tr.style.display = '';
                    visibleCount++;
                } else {
                    tr.style.display = 'none';
                }
            });

            if (resultHint) {
                resultHint.textContent = `Menampilkan ${visibleCount} dari ${rows.length} alat`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterAlatTable);
        if (filterKategori) filterKategori.addEventListener('change', filterAlatTable);
        if (filterStatus) filterStatus.addEventListener('change', filterAlatTable);

        // Detail Modal
        function openDetail(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const nama = tr.querySelector('.name-cell .n').textContent;
            const kategori = tr.getAttribute('data-kategori');
            const status = tr.getAttribute('data-status');
            const merek = tr.getAttribute('data-merek');
            const model = tr.getAttribute('data-model');
            const noseri = tr.getAttribute('data-noseri');
            const tahun = tr.getAttribute('data-tahun');
            const lokasiNama = tr.getAttribute('data-lokasi-nama');
            const lokasiKode = tr.getAttribute('data-lokasi-kode');
            const ket = tr.getAttribute('data-ket');
            const foto = tr.getAttribute('data-foto');

            document.getElementById('dNama').textContent = nama;
            document.getElementById('dKategori').textContent = kategori === 'rig' ? 'Rig' : 'Alat Pendukung';
            
            let statusHtml = '';
            if (status === 'aktif') statusHtml = '<span class="status-chip aktif"><span class="d"></span>Aktif</span>';
            else if (status === 'standby') statusHtml = '<span class="status-chip standby"><span class="d"></span>Tidak Aktif</span>';
            else statusHtml = '<span class="status-chip bermasalah"><span class="d"></span>Bermasalah</span>';
            document.getElementById('dStatusChip').innerHTML = statusHtml;

            document.getElementById('dMerek').textContent = merek ? merek.toUpperCase() : '—';
            document.getElementById('dModel').textContent = model || '—';
            document.getElementById('dNoSeri').textContent = noseri ? noseri.toUpperCase() : '—';
            document.getElementById('dTahun').textContent = tahun || '—';
            document.getElementById('dLokasi').textContent = lokasiNama ? `${lokasiKode} — ${lokasiNama}` : 'Belum ditugaskan (Standby / Gudang)';
            document.getElementById('dKeterangan').textContent = ket || 'Tidak ada keterangan tambahan.';

            const detailImg = document.getElementById('detailImg');
            const detailIcon = document.getElementById('detailIcon');
            if (foto) {
                detailImg.src = foto;
                detailImg.style.display = 'block';
                detailIcon.style.display = 'none';
            } else {
                detailImg.style.display = 'none';
                detailIcon.style.display = 'block';
                if (kategori === 'rig') {
                    detailIcon.innerHTML = '<circle cx="12" cy="12" r="9"/><path d="M12 6c1.6 2.4 3.2 3.6 3.2 6A3.2 3.2 0 0112 15.2 3.2 3.2 0 018.8 12c0-2.4 1.6-3.6 3.2-6z"/>';
                } else {
                    detailIcon.innerHTML = '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10.5" r="1.7"/><path d="M21 15l-4.5-4.5a2 2 0 00-2.8 0L5 19"/>';
                }
            }

            const btnDetailEdit = document.getElementById('btnDetailEdit');
            if (btnDetailEdit) {
                btnDetailEdit.onclick = function() {
                    closeOverlay('overlayDetail');
                    openEdit(id);
                };
            }

            openOverlay('overlayDetail');
        }

        @if ($canManage)
        // Add / Edit Modal
        const btnAdd = document.getElementById('btnAdd');
        const fFotoAlat = document.getElementById('fFotoAlat');
        const formFotoPreview = document.getElementById('formFotoPreview');
        const formFotoPlaceholderSvg = document.getElementById('formFotoPlaceholderSvg');
        const btnHapusFotoAlat = document.getElementById('btnHapusFotoAlat');

        if (fFotoAlat) {
            fFotoAlat.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        formFotoPreview.src = evt.target.result;
                        formFotoPreview.style.display = 'block';
                        formFotoPlaceholderSvg.style.display = 'none';
                        if (btnHapusFotoAlat) btnHapusFotoAlat.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (btnHapusFotoAlat) {
            btnHapusFotoAlat.addEventListener('click', function() {
                if (fFotoAlat) fFotoAlat.value = '';
                formFotoPreview.src = '';
                formFotoPreview.style.display = 'none';
                formFotoPlaceholderSvg.style.display = 'block';
                btnHapusFotoAlat.style.display = 'none';
            });
        }

        if (btnAdd) {
            btnAdd.addEventListener('click', function() {
                const form = document.getElementById('formAlat');
                form.action = "{{ route('alat.store') }}";
                document.getElementById('formMethod').value = "POST";
                document.getElementById('formTitle').textContent = "Tambah Alat";

                document.getElementById('fNama').value = '';
                document.getElementById('fKategori').value = 'rig';
                document.getElementById('fStatus').value = 'aktif';
                document.getElementById('fMerek').value = '';
                document.getElementById('fModel').value = '';
                document.getElementById('fTahun').value = '';
                document.getElementById('fNoSeri').value = '';
                document.getElementById('fLokasi').value = '';
                document.getElementById('fKeterangan').value = '';

                if (fFotoAlat) fFotoAlat.value = '';
                if (formFotoPreview) {
                    formFotoPreview.src = '';
                    formFotoPreview.style.display = 'none';
                }
                if (formFotoPlaceholderSvg) formFotoPlaceholderSvg.style.display = 'block';
                if (btnHapusFotoAlat) btnHapusFotoAlat.style.display = 'none';

                openOverlay('overlayForm');
            });
        }

        function openEdit(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const form = document.getElementById('formAlat');
            form.action = `/alat/${id}`;
            document.getElementById('formMethod').value = "PUT";
            document.getElementById('formTitle').textContent = "Edit Alat";

            document.getElementById('fNama').value = tr.querySelector('.name-cell .n').textContent;
            document.getElementById('fKategori').value = tr.getAttribute('data-kategori');
            document.getElementById('fStatus').value = tr.getAttribute('data-status');
            document.getElementById('fMerek').value = tr.getAttribute('data-merek');
            document.getElementById('fModel').value = tr.getAttribute('data-model');
            document.getElementById('fTahun').value = tr.getAttribute('data-tahun');
            document.getElementById('fNoSeri').value = tr.getAttribute('data-noseri');
            document.getElementById('fLokasi').value = tr.getAttribute('data-lokasi-id') || '';
            document.getElementById('fKeterangan').value = tr.getAttribute('data-ket');

            const foto = tr.getAttribute('data-foto');
            if (fFotoAlat) fFotoAlat.value = '';
            if (foto) {
                formFotoPreview.src = foto;
                formFotoPreview.style.display = 'block';
                formFotoPlaceholderSvg.style.display = 'none';
                if (btnHapusFotoAlat) btnHapusFotoAlat.style.display = 'inline-block';
            } else {
                formFotoPreview.src = '';
                formFotoPreview.style.display = 'none';
                formFotoPlaceholderSvg.style.display = 'block';
                if (btnHapusFotoAlat) btnHapusFotoAlat.style.display = 'none';
            }

            openOverlay('overlayForm');
        }

        // Delete Confirm Modal
        function openConfirmDelete(id, name) {
            const form = document.getElementById('formDelete');
            form.action = `/alat/${id}`;
            document.getElementById('confirmName').textContent = name;
            openOverlay('overlayConfirm');
        }
        @endif
    </script>
</x-app-layout>
