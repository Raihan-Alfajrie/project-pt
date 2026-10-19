<x-app-layout :title="'Pertamina Admin Panel — Audit Log'" pageTitle="Audit Log" pageDesc="Riwayat aktivitas user &amp; admin">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M6 3h9l5 5v13H6z"/><path d="M9 12h6M9 16h6M9 8h2"/></svg>
    </x-slot>

    @push('styles')
    <style>
        /* ===== Toolbar & Table ===== */
        .sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .sec-head h2 { font-size: 15.5px; margin: 0; }
        .sec-head .hint { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .search { display: flex; align-items: center; gap: 7px; background: var(--panel); border: 1px solid var(--border); border-radius: 9px; padding: 8px 12px; }
        .search svg { width: 14px; height: 14px; color: var(--text-faint); }
        .search input { background: transparent; border: 0; outline: 0; color: var(--text); font-size: 12.5px; width: 190px; font-family: inherit; }
        .select { background: var(--panel); border: 1px solid var(--border); color: var(--text-dim); font-size: 12px; padding: 8px 11px; border-radius: 9px; font-family: inherit; cursor: pointer; outline: 0; }

        .table-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .table-scroll { overflow-x: auto; max-height: 70vh; overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { position: sticky; top: 0; background: var(--panel-2); text-align: left; font-size: 10.5px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: .05em; padding: 12px 14px; border-bottom: 1px solid var(--border); white-space: nowrap; z-index: 2; user-select: none; }
        tbody td { padding: 11px 14px; font-size: 12.5px; border-bottom: 1px solid var(--border-soft); white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--panel-2); }
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .mini-avatar { width: 30px; height: 30px; border-radius: 8px; background: var(--panel-3); border: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: center; font-size: 10.5px; font-weight: 700; color: var(--text-dim); font-family: 'Space Grotesk', sans-serif; flex: 0 0 auto; }
        .user-cell .n { font-weight: 700; }
        .user-cell .u { font-size: 10.5px; color: var(--text-faint); font-family: 'IBM Plex Mono', monospace; }
        .role-chip { font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 99px; text-transform: capitalize; }
        .role-chip.admin { background: rgba(234,32,39,.18); color: #ff6b6f; }
        .role-chip.pekerja { background: rgba(75,155,240,.16); color: #7ab4f5; }
        .waktu-cell { font-family: 'IBM Plex Mono', monospace; font-size: 11.5px; color: var(--text-dim); white-space: nowrap; }
        .aksi-tag { display: inline-flex; align-items: center; gap: 6px; font-size: 10.5px; font-weight: 700; padding: 4px 9px; border-radius: 7px; white-space: nowrap; text-transform: capitalize; }
        .aksi-tag.login { background: rgba(74,222,128,.14); color: #4ade80; }
        .aksi-tag.logout { background: var(--panel-3); color: var(--text-faint); }
        .aksi-tag.tambah { background: rgba(75,155,240,.14); color: #7ab4f5; }
        .aksi-tag.edit { background: rgba(240,169,59,.16); color: #f0a93b; }
        .aksi-tag.hapus { background: rgba(234,32,39,.16); color: #ff6b6f; }
        .aksi-tag .d { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
        .ket-cell { color: var(--text-dim); max-width: 420px; white-space: normal; line-height: 1.5; }
        .modul-badge { font-size: 10px; font-weight: 700; color: var(--text-faint); background: var(--panel-3); padding: 2px 8px; border-radius: 6px; margin-left: 6px; }
    </style>
    @endpush

    <!-- Main Content Area -->
    <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="sec-head">
            <div>
                <h2>Riwayat Aktivitas</h2>
                <div class="hint" id="resultHint">Menampilkan {{ count($logsData) }} aktivitas</div>
            </div>
            <div class="toolbar">
                <div class="search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="searchInput" placeholder="Cari username / email / keterangan…">
                </div>
                <select class="select" id="filterAksi">
                    <option value="">Semua aksi</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="tambah">Tambah</option>
                    <option value="edit">Edit</option>
                    <option value="hapus">Hapus</option>
                </select>
                <select class="select" id="filterModul">
                    <option value="">Semua modul</option>
                    <option value="Alat">Alat</option>
                    <option value="Lokasi">Lokasi</option>
                    <option value="Jadwal">Jadwal</option>
                    <option value="User">Akun User</option>
                    <option value="Sistem">Sistem</option>
                </select>
                <select class="select" id="filterJabatan">
                    <option value="">Semua jabatan</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Pekerja">Pekerja</option>
                </select>
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
                            <th>Tanggal / Waktu</th>
                            <th>Aksi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach ($logsData as $l)
                            <tr data-id="{{ $l['id'] }}"
                                data-u="{{ $l['u'] }}"
                                data-un="{{ $l['un'] }}"
                                data-email="{{ $l['email'] }}"
                                data-jab="{{ $l['jab'] }}"
                                data-aksi="{{ $l['aksi'] }}"
                                data-modul="{{ $l['modul'] }}"
                                data-ket="{{ $l['ket'] }}">
                                <td>
                                    <div class="user-cell">
                                        <div class="mini-avatar">{{ $l['initials'] }}</div>
                                        <div>
                                            <div class="n">{{ $l['u'] }}</div>
                                            <div class="u">@{{ $l['un'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $l['email'] }}</td>
                                <td>
                                    <span class="role-chip {{ strtolower($l['jab']) === 'administrator' ? 'admin' : 'pekerja' }}">
                                        {{ $l['jab'] }}
                                    </span>
                                </td>
                                <td class="waktu-cell">{{ $l['waktu'] }}</td>
                                <td>
                                    <span class="aksi-tag {{ $l['aksi'] }}">
                                        <span class="d"></span>{{ ucfirst($l['aksi']) }}
                                    </span>
                                    @if ($l['modul'] !== 'Sistem')
                                        <span class="modul-badge">{{ $l['modul'] }}</span>
                                    @endif
                                </td>
                                <td class="ket-cell">{{ $l['ket'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const LOG = @json($logsData);

        const searchInput = document.getElementById('searchInput');
        const filterAksi = document.getElementById('filterAksi');
        const filterModul = document.getElementById('filterModul');
        const filterJabatan = document.getElementById('filterJabatan');
        const resultHint = document.getElementById('resultHint');
        const rows = document.querySelectorAll('#tableBody tr[data-id]');

        function filterAuditTable() {
            const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const aksi = (filterAksi ? filterAksi.value : '');
            const modul = (filterModul ? filterModul.value : '');
            const jab = (filterJabatan ? filterJabatan.value : '');
            let visibleCount = 0;

            rows.forEach(tr => {
                const u = (tr.getAttribute('data-u') || '').toLowerCase();
                const un = (tr.getAttribute('data-un') || '').toLowerCase();
                const email = (tr.getAttribute('data-email') || '').toLowerCase();
                const ket = (tr.getAttribute('data-ket') || '').toLowerCase();
                const trAksi = tr.getAttribute('data-aksi') || '';
                const trModul = tr.getAttribute('data-modul') || '';
                const trJab = tr.getAttribute('data-jab') || '';

                const matchQ = !q || u.includes(q) || un.includes(q) || email.includes(q) || ket.includes(q);
                const matchA = !aksi || trAksi === aksi;
                const matchM = !modul || trModul === modul;
                const matchJ = !jab || trJab === jab;

                if (matchQ && matchA && matchM && matchJ) {
                    tr.style.display = '';
                    visibleCount++;
                } else {
                    tr.style.display = 'none';
                }
            });

            if (resultHint) {
                resultHint.textContent = `Menampilkan ${visibleCount} dari ${rows.length} aktivitas`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterAuditTable);
        if (filterAksi) filterAksi.addEventListener('change', filterAuditTable);
        if (filterModul) filterModul.addEventListener('change', filterAuditTable);
        if (filterJabatan) filterJabatan.addEventListener('change', filterAuditTable);
    </script>
    @endpush
</x-app-layout>
