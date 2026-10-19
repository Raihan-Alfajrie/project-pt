<x-app-layout :title="'Pertamina Admin Panel — Manajemen User'" pageTitle="Manajemen User" pageDesc="Kelola akun, status, dan hak akses pengguna">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><circle cx="12" cy="8" r="3.4"/><path d="M4 20c1.6-4 5-6 8-6s6.4 2 8 6"/></svg>
    </x-slot>

    @push('styles')
    <style>
        /* ===== Summary KPI cards ===== */
        .kpi-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .kpi { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; }
        .kpi .top { display: flex; align-items: center; justify-content: space-between; }
        .kpi .lbl { font-size: 12.5px; color: var(--text-dim); font-weight: 600; }
        .kpi .icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; background: var(--red-soft); }
        .kpi .icon svg { width: 16px; height: 16px; color: var(--red); }
        .kpi .val { font-size: 34px; font-weight: 700; margin-top: 12px; font-family: 'Space Grotesk', sans-serif; }
        .kpi-split { display: flex; align-items: center; gap: 14px; margin-top: 10px; }
        .kpi-split .item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-dim); }
        .kpi-split .d { width: 8px; height: 8px; border-radius: 50%; }
        .kpi-split .d.on { background: #4ade80; box-shadow: 0 0 0 3px rgba(74,222,128,.18); }
        .kpi-split .d.off { background: var(--text-faint); }

        /* ===== Toolbar & Table ===== */
        .sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .sec-head h2 { font-size: 15.5px; }
        .sec-head .hint { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .search { display: flex; align-items: center; gap: 7px; background: var(--panel); border: 1px solid var(--border); border-radius: 9px; padding: 8px 12px; }
        .search svg { width: 14px; height: 14px; color: var(--text-faint); }
        .search input { background: transparent; border: 0; outline: 0; color: var(--text); font-size: 12.5px; width: 180px; font-family: inherit; }
        .select { background: var(--panel); border: 1px solid var(--border); color: var(--text-dim); font-size: 12px; padding: 8px 11px; border-radius: 9px; font-family: inherit; cursor: pointer; outline: 0; }
        .btn-primary { display: flex; align-items: center; gap: 7px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; padding: 9px 15px; border-radius: 9px; cursor: pointer; border: 0; font-family: inherit; transition: background .15s; }
        .btn-primary svg { width: 14px; height: 14px; }
        .btn-primary:hover { background: var(--red-strong); }

        .table-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { position: sticky; top: 0; background: var(--panel-2); text-align: left; font-size: 10.5px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: .05em; padding: 12px 14px; border-bottom: 1px solid var(--border); white-space: nowrap; user-select: none; }
        tbody td { padding: 11px 14px; font-size: 12.5px; border-bottom: 1px solid var(--border-soft); white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--panel-2); }
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .mini-avatar { width: 32px; height: 32px; border-radius: 9px; background: var(--panel-3); border: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--text-dim); font-family: 'Space Grotesk', sans-serif; flex: 0 0 auto; }
        .user-cell .n { font-weight: 700; }
        .user-cell .u { font-size: 10.5px; color: var(--text-faint); font-family: 'IBM Plex Mono', monospace; }
        .role-chip { font-size: 11px; font-weight: 700; padding: 4px 9px; border-radius: 99px; text-transform: capitalize; }
        .role-chip.admin { background: rgba(234,32,39,.18); color: #ff6b6f; }
        .role-chip.pekerja { background: rgba(75,155,240,.16); color: #7ab4f5; }
        .status-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 99px; text-transform: capitalize; }
        .status-chip.on { background: rgba(74,222,128,.16); color: #4ade80; }
        .status-chip.off { background: var(--panel-3); color: var(--text-faint); }
        .status-chip .d { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .access-dots { display: flex; gap: 5px; }
        .access-dots span { font-size: 9.5px; font-weight: 700; padding: 3px 7px; border-radius: 6px; background: var(--panel-3); color: var(--text-faint); }
        .access-dots span.on { background: rgba(240,169,59,.18); color: #f0a93b; }
        .row-actions { display: flex; align-items: center; gap: 4px; }
        .act-btn { width: 29px; height: 29px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: var(--panel-3); border: 1px solid var(--border-soft); }
        .act-btn svg { width: 14px; height: 14px; color: var(--text-dim); }
        .act-btn:hover { background: var(--red-soft); }
        .act-btn.danger:hover svg { color: #ff6b6f; }

        /* ===== Modals ===== */
        .overlay { position: fixed; inset: 0; background: rgba(10,8,9,.6); backdrop-filter: blur(2px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
        .overlay.show { display: flex; }
        .modal { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 500px; max-height: 88vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .modal.modal-detail { max-width: 560px; }
        .modal.modal-confirm { max-width: 400px; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border-soft); position: sticky; top: 0; background: var(--panel); z-index: 2; }
        .modal-head h3 { font-size: 16px; margin: 0; }
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
        .field input, .field select { background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 9px; padding: 10px 12px; color: var(--text); font-size: 12.5px; font-family: inherit; outline: 0; }
        .field input:focus, .field select:focus { border-color: var(--red); }
        .field .err { font-size: 10.5px; color: #ff6b6f; display: none; }
        .field.invalid input, .field.invalid select { border-color: #ff6b6f; }
        .field.invalid .err { display: block; }
        .field .subnote { font-size: 10.5px; color: var(--text-faint); }

        .access-list { display: flex; flex-direction: column; gap: 10px; }
        .access-item { display: flex; align-items: center; justify-content: space-between; background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 10px; padding: 13px 15px; }
        .access-item .lbl { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; }
        .access-item .lbl .ic { width: 28px; height: 28px; border-radius: 8px; background: var(--panel-3); display: flex; align-items: center; justify-content: center; }
        .access-item .lbl .ic svg { width: 14px; height: 14px; color: var(--text-dim); }
        
        /* Interactive Switch component */
        .switch-wrap { display: flex; align-items: center; cursor: pointer; }
        .switch-wrap input { display: none; }
        .switch-track { position: relative; width: 40px; height: 22px; border-radius: 99px; background: var(--panel-3); transition: background .15s; }
        .switch-thumb { position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; border-radius: 50%; background: var(--text-faint); transition: transform .15s, background .15s; }
        .switch-wrap input:checked + .switch-track { background: rgba(240,169,59,.3); }
        .switch-wrap input:checked + .switch-track .switch-thumb { transform: translateX(18px); background: #f0a93b; }

        .detail-hero { display: flex; align-items: center; gap: 14px; }
        .detail-hero .big-avatar { width: 56px; height: 56px; border-radius: 14px; background: var(--panel-3); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; color: var(--text-dim); font-family: 'Space Grotesk', sans-serif; flex: 0 0 auto; }
        .detail-hero h4 { font-size: 16.5px; font-family: 'Space Grotesk', sans-serif; margin: 0; }
        .detail-hero .u { font-size: 12px; color: var(--text-faint); font-family: 'IBM Plex Mono', monospace; margin-top: 2px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
        .dkv .k { font-size: 10.5px; color: var(--text-faint); text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
        .dkv .v { font-size: 13px; margin-top: 4px; font-weight: 600; }
        .chip-row { display: flex; flex-wrap: wrap; gap: 6px; }
        .mini-chip { font-size: 11px; font-weight: 600; background: var(--panel-3); color: var(--text-dim); padding: 4px 9px; border-radius: 99px; border: 1px solid var(--border-soft); }
        .mini-chip.on { color: #f0a93b; border-color: rgba(240,169,59,.3); }

        .alert-banner { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13px; background:rgba(46,204,113,.15); border:1px solid rgba(46,204,113,.4); color:#2ecc71; margin-bottom:12px; }
        .alert-banner.error { background:rgba(234,32,39,.15); border-color:rgba(234,32,39,.4); color:#ff6b6f; }

        @media (max-width: 980px){
            .kpi-row { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
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

        <!-- Panel 1: KPI Summary Cards -->
        <div class="kpi-row">
            <div class="kpi">
                <div class="top">
                    <span class="lbl">Total Admin</span>
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 6 6.5 1-4.7 4.6L18 20l-6-3.4L6 20l1.2-6.4L2.5 9l6.5-1z"/></svg>
                    </span>
                </div>
                <div class="val" id="kpiAdminTotal">{{ $adminTotal }}</div>
                <div class="kpi-split">
                    <div class="item"><span class="d on"></span><span id="kpiAdminOn">{{ $adminOn }}</span> online</div>
                    <div class="item"><span class="d off"></span><span id="kpiAdminOff">{{ $adminOff }}</span> offline</div>
                </div>
            </div>
            <div class="kpi">
                <div class="top">
                    <span class="lbl">Total User (Pekerja)</span>
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3.4"/><path d="M2 20c1.4-3.6 4.4-5.4 7-5.4s5.6 1.8 7 5.4"/><circle cx="17.5" cy="9" r="2.6"/><path d="M15.5 14.2c2.6.4 4.6 2 5.7 5.3"/></svg>
                    </span>
                </div>
                <div class="val" id="kpiUserTotal">{{ $userTotal }}</div>
                <div class="kpi-split">
                    <div class="item"><span class="d on"></span><span id="kpiUserOn">{{ $userOn }}</span> online</div>
                    <div class="item"><span class="d off"></span><span id="kpiUserOff">{{ $userOff }}</span> offline</div>
                </div>
            </div>
        </div>

        <!-- Panel 2: Table Section -->
        <div class="sec-head">
            <div>
                <h2>Daftar User</h2>
                <div class="hint" id="resultHint">Menampilkan {{ $users->count() }} user</div>
            </div>
            <div class="toolbar">
                <div class="search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="searchInput" placeholder="Cari nama / username / email…">
                </div>
                <select class="select" id="filterJabatan">
                    <option value="">Semua jabatan</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Pekerja">Pekerja</option>
                </select>
                <select class="select" id="filterStatus">
                    <option value="">Semua status</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
                <button class="btn-primary" id="btnAdd" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah User
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Gmail</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Hak Akses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach ($usersData as $u)
                            <tr data-id="{{ $u['id'] }}"
                                data-nama="{{ $u['nama'] }}"
                                data-username="{{ $u['username'] }}"
                                data-email="{{ $u['email'] }}"
                                data-jabatan="{{ $u['jabatan'] }}"
                                data-status="{{ $u['status'] }}"
                                data-akses-alat="{{ $u['akses']['alat'] ? '1' : '0' }}"
                                data-akses-lokasi="{{ $u['akses']['lokasi'] ? '1' : '0' }}"
                                data-akses-jadwal="{{ $u['akses']['jadwal'] ? '1' : '0' }}">
                                <td>
                                    <div class="user-cell">
                                        <div class="mini-avatar">{{ $u['initials'] }}</div>
                                        <div>
                                            <div class="n">{{ $u['nama'] }}</div>
                                            <div class="u">@{{ $u['username'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $u['email'] }}</td>
                                <td>
                                    <span class="role-chip {{ strtolower($u['jabatan']) === 'administrator' ? 'admin' : 'pekerja' }}">
                                        {{ $u['jabatan'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-chip {{ $u['status'] === 'online' ? 'on' : 'off' }}">
                                        <span class="d"></span>{{ ucfirst($u['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="access-dots">
                                        <span class="{{ $u['akses']['alat'] ? 'on' : '' }}">Alat</span>
                                        <span class="{{ $u['akses']['lokasi'] ? 'on' : '' }}">Lokasi</span>
                                        <span class="{{ $u['akses']['jadwal'] ? 'on' : '' }}">Jadwal</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <div class="act-btn" onclick="openDetail({{ $u['id'] }})" title="Lihat detail">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </div>
                                        <div class="act-btn" onclick="openEdit({{ $u['id'] }})" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                                        </div>
                                        <div class="act-btn" onclick="openAkses({{ $u['id'] }})" title="Atur hak akses">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M19.4 13.5c.1-.5.1-1 0-1.5l1.6-1.3-1.5-2.6-1.9.5a7 7 0 00-1.3-.75L15.9 5h-3l-.4 2.35a7 7 0 00-1.3.75l-1.9-.5-1.5 2.6L9.4 12c-.1.5-.1 1 0 1.5l-1.6 1.3 1.5 2.6 1.9-.5c.4.3.85.55 1.3.75L13 20.5h3l.4-2.35a7 7 0 001.3-.75l1.9.5 1.5-2.6z"/></svg>
                                        </div>
                                        @if (auth()->id() !== $u['id'])
                                            <div class="act-btn danger" onclick="openConfirmDelete({{ $u['id'] }}, '{{ addslashes($u['nama']) }}', '{{ $u['username'] }}')" title="Hapus">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="overlay" id="overlayFormAdd">
        <div class="modal">
            <div class="modal-head">
                <h3>Tambah User</h3>
                <button class="modal-close" onclick="closeOverlay('overlayFormAdd')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <form id="formAddUser" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field full">
                            <label>Nama Lengkap <span class="req">*</span></label>
                            <input name="nama" required placeholder="cth. Budi Santoso">
                        </div>
                        <div class="field">
                            <label>Username <span class="req">*</span></label>
                            <input name="username" required placeholder="cth. budi.santoso" style="text-transform:lowercase">
                        </div>
                        <div class="field">
                            <label>Jabatan <span class="req">*</span></label>
                            <select name="jabatan" required>
                                <option value="pekerja" selected>Pekerja</option>
                                <option value="administrator">Administrator</option>
                            </select>
                        </div>
                        <div class="field full">
                            <label>Gmail / Email <span class="req">*</span></label>
                            <input name="email" type="email" required placeholder="nama@gmail.com">
                        </div>
                        <div class="field">
                            <label>Password <span class="req">*</span></label>
                            <input name="password" type="password" required placeholder="Minimal 6 karakter" minlength="6">
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                <option value="online">Online</option>
                                <option value="offline" selected>Offline</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn-ghost" onclick="closeOverlay('overlayFormAdd')" type="button">Batal</button>
                    <button class="btn-primary" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="overlay" id="overlayFormEdit">
        <div class="modal">
            <div class="modal-head">
                <h3>Edit User</h3>
                <button class="modal-close" onclick="closeOverlay('overlayFormEdit')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <form id="formEditUser" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="field full">
                            <label>Nama Lengkap <span class="req">*</span></label>
                            <input id="eNama" name="nama" required placeholder="cth. Budi Santoso">
                        </div>
                        <div class="field">
                            <label>Username <span class="req">*</span></label>
                            <input id="eUsername" name="username" required placeholder="cth. budi.santoso" style="text-transform:lowercase">
                        </div>
                        <div class="field">
                            <label>Jabatan <span class="req">*</span></label>
                            <select id="eJabatan" name="jabatan" required>
                                <option value="pekerja">Pekerja</option>
                                <option value="administrator">Administrator</option>
                            </select>
                        </div>
                        <div class="field full">
                            <label>Gmail / Email <span class="req">*</span></label>
                            <input id="eEmail" name="email" type="email" required placeholder="nama@gmail.com">
                        </div>
                        <div class="field">
                            <label>Password Baru</label>
                            <input id="ePassword" name="password" type="password" placeholder="Kosongkan jika tidak diubah" minlength="6">
                            <span class="subnote">Hanya isi jika ingin mengubah password</span>
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select id="eStatus" name="status">
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn-ghost" onclick="closeOverlay('overlayFormEdit')" type="button">Batal</button>
                    <button class="btn-primary" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Atur Hak Akses -->
    <div class="overlay" id="overlayAkses">
        <div class="modal">
            <div class="modal-head">
                <h3>Atur Hak Akses</h3>
                <button class="modal-close" onclick="closeOverlay('overlayAkses')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <form id="formAkses" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="hint" style="font-size:12px; color:var(--text-faint)" id="aksesUserLabel">Mengatur akses untuk —</div>
                    <div class="access-list">
                        <div class="access-item">
                            <div class="lbl">
                                <div class="ic">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg>
                                </div>
                                <div>
                                    <div>Kelola Alat</div>
                                    <div style="font-size:10.5px; color:var(--text-faint); font-weight:400;">Tambah, edit, dan hapus rig / alat</div>
                                </div>
                            </div>
                            <label class="switch-wrap">
                                <input type="checkbox" name="alat" id="chkAlat" value="1">
                                <div class="switch-track"><div class="switch-thumb"></div></div>
                            </label>
                        </div>

                        <div class="access-item">
                            <div class="lbl">
                                <div class="ic">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg>
                                </div>
                                <div>
                                    <div>Kelola Lokasi</div>
                                    <div style="font-size:10.5px; color:var(--text-faint); font-weight:400;">Tambah, edit, dan hapus kode lapangan</div>
                                </div>
                            </div>
                            <label class="switch-wrap">
                                <input type="checkbox" name="lokasi" id="chkLokasi" value="1">
                                <div class="switch-track"><div class="switch-thumb"></div></div>
                            </label>
                        </div>

                        <div class="access-item">
                            <div class="lbl">
                                <div class="ic">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                                </div>
                                <div>
                                    <div>Kelola Jadwal</div>
                                    <div style="font-size:10.5px; color:var(--text-faint); font-weight:400;">Tambah, edit, dan hapus pekerjaan Gantt</div>
                                </div>
                            </div>
                            <label class="switch-wrap">
                                <input type="checkbox" name="jadwal" id="chkJadwal" value="1">
                                <div class="switch-track"><div class="switch-thumb"></div></div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button class="btn-ghost" onclick="closeOverlay('overlayAkses')" type="button">Batal</button>
                    <button class="btn-primary" type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                        Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail User -->
    <div class="overlay" id="overlayDetail">
        <div class="modal modal-detail">
            <div class="modal-head">
                <h3>Detail User</h3>
                <button class="modal-close" onclick="closeOverlay('overlayDetail')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-hero">
                    <div class="big-avatar" id="dAvatar">—</div>
                    <div>
                        <h4 id="dNama">—</h4>
                        <div class="u" id="dUsername">@—</div>
                    </div>
                </div>
                <div class="detail-grid">
                    <div class="dkv"><div class="k">Gmail / Email</div><div class="v" id="dEmail">—</div></div>
                    <div class="dkv"><div class="k">Jabatan</div><div class="v" id="dJabatan">—</div></div>
                    <div class="dkv"><div class="k">Status</div><div class="v" id="dStatus">—</div></div>
                </div>
                <div>
                    <div class="dkv" style="margin-bottom:8px"><div class="k">Hak Akses Modul Operasional</div></div>
                    <div class="chip-row" id="dAkses"></div>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn-ghost" onclick="closeOverlay('overlayDetail')" type="button">Tutup</button>
                <button class="btn-primary" id="btnDetailEdit" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                    Edit
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="overlay" id="overlayConfirm">
        <div class="modal modal-confirm">
            <div class="modal-head">
                <h3>Hapus User?</h3>
                <button class="modal-close" onclick="closeOverlay('overlayConfirm')" type="button" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <form id="formDelete" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p style="font-size:13px; color:var(--text-dim); line-height:1.6; margin:0">
                        User <b id="confirmName" style="color:var(--text)"></b> akan dihapus permanen dari database. Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-foot">
                    <button class="btn-ghost" onclick="closeOverlay('overlayConfirm')" type="button">Batal</button>
                    <button class="btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
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

        // Add User Button
        const btnAdd = document.getElementById('btnAdd');
        if (btnAdd) {
            btnAdd.addEventListener('click', () => {
                document.getElementById('formAddUser').reset();
                openOverlay('overlayFormAdd');
            });
        }

        // Live Search and Filter
        const searchInput = document.getElementById('searchInput');
        const filterJabatan = document.getElementById('filterJabatan');
        const filterStatus = document.getElementById('filterStatus');
        const resultHint = document.getElementById('resultHint');
        const rows = document.querySelectorAll('#tableBody tr[data-id]');

        function filterUsersTable() {
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const jab = (filterJabatan ? filterJabatan.value : '');
            const st = (filterStatus ? filterStatus.value : '');
            let visibleCount = 0;

            rows.forEach(tr => {
                const nama = (tr.getAttribute('data-nama') || '').toLowerCase();
                const username = (tr.getAttribute('data-username') || '').toLowerCase();
                const email = (tr.getAttribute('data-email') || '').toLowerCase();
                const trJab = tr.getAttribute('data-jabatan') || '';
                const trSt = tr.getAttribute('data-status') || '';

                const matchQ = !q || nama.includes(q) || username.includes(q) || email.includes(q);
                const matchJ = !jab || trJab === jab;
                const matchS = !st || trSt === st;

                if (matchQ && matchJ && matchS) {
                    tr.style.display = '';
                    visibleCount++;
                } else {
                    tr.style.display = 'none';
                }
            });

            if (resultHint) {
                resultHint.textContent = `Menampilkan ${visibleCount} dari ${rows.length} user`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterUsersTable);
        if (filterJabatan) filterJabatan.addEventListener('change', filterUsersTable);
        if (filterStatus) filterStatus.addEventListener('change', filterUsersTable);

        // Edit User
        function openEdit(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const form = document.getElementById('formEditUser');
            form.action = `/users/${id}`;

            document.getElementById('eNama').value = tr.getAttribute('data-nama');
            document.getElementById('eUsername').value = tr.getAttribute('data-username');
            document.getElementById('eEmail').value = tr.getAttribute('data-email');
            document.getElementById('eJabatan').value = tr.getAttribute('data-jabatan').toLowerCase();
            document.getElementById('eStatus').value = tr.getAttribute('data-status');
            document.getElementById('ePassword').value = '';

            closeOverlay('overlayDetail');
            openOverlay('overlayFormEdit');
        }

        // Atur Hak Akses
        function openAkses(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const nama = tr.getAttribute('data-nama');
            const username = tr.getAttribute('data-username');
            const aksesAlat = tr.getAttribute('data-akses-alat') === '1';
            const aksesLokasi = tr.getAttribute('data-akses-lokasi') === '1';
            const aksesJadwal = tr.getAttribute('data-akses-jadwal') === '1';

            document.getElementById('aksesUserLabel').textContent = `Mengatur hak akses untuk ${nama} (@${username})`;
            document.getElementById('chkAlat').checked = aksesAlat;
            document.getElementById('chkLokasi').checked = aksesLokasi;
            document.getElementById('chkJadwal').checked = aksesJadwal;

            const form = document.getElementById('formAkses');
            form.action = `/users/${id}/permissions`;

            openOverlay('overlayAkses');
        }

        // Detail User
        function openDetail(id) {
            const tr = document.querySelector(`#tableBody tr[data-id="${id}"]`);
            if (!tr) return;

            const nama = tr.getAttribute('data-nama');
            const username = tr.getAttribute('data-username');
            const email = tr.getAttribute('data-email');
            const jabatan = tr.getAttribute('data-jabatan');
            const status = tr.getAttribute('data-status');
            const aksesAlat = tr.getAttribute('data-akses-alat') === '1';
            const aksesLokasi = tr.getAttribute('data-akses-lokasi') === '1';
            const aksesJadwal = tr.getAttribute('data-akses-jadwal') === '1';

            const initials = tr.querySelector('.mini-avatar').textContent;

            document.getElementById('dAvatar').textContent = initials;
            document.getElementById('dNama').textContent = nama;
            document.getElementById('dUsername').textContent = '@' + username;
            document.getElementById('dEmail').textContent = email;

            const roleClass = jabatan.toLowerCase() === 'administrator' ? 'admin' : 'pekerja';
            document.getElementById('dJabatan').innerHTML = `<span class="role-chip ${roleClass}">${jabatan}</span>`;

            const statusClass = status === 'online' ? 'on' : 'off';
            document.getElementById('dStatus').innerHTML = `<span class="status-chip ${statusClass}"><span class="d"></span>${status === 'online' ? 'Online' : 'Offline'}</span>`;

            const aksesChips = [
                { name: 'Kelola Alat', on: aksesAlat },
                { name: 'Kelola Lokasi', on: aksesLokasi },
                { name: 'Kelola Jadwal', on: aksesJadwal }
            ];

            document.getElementById('dAkses').innerHTML = aksesChips.map(a => `
                <span class="mini-chip ${a.on ? 'on' : ''}">
                    ${a.name} ${a.on ? '✓' : '—'}
                </span>
            `).join('');

            const btnDetailEdit = document.getElementById('btnDetailEdit');
            if (btnDetailEdit) {
                btnDetailEdit.onclick = () => {
                    closeOverlay('overlayDetail');
                    openEdit(id);
                };
            }

            openOverlay('overlayDetail');
        }

        // Delete Confirm
        function openConfirmDelete(id, nama, username) {
            const form = document.getElementById('formDelete');
            form.action = `/users/${id}`;
            document.getElementById('confirmName').textContent = `${nama} (@${username})`;
            openOverlay('overlayConfirm');
        }
    </script>
    @endpush
</x-app-layout>
