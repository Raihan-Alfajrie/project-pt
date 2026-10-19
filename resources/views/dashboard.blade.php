<x-app-layout pageTitle="Dashboard" pageDesc="Pantau status, jadwal dan progres">
    <style>
        /* ===== panel 1: status banner ===== */
        .banner {
            background: linear-gradient(135deg, var(--panel-3), var(--panel));
            border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 24px; position: relative; overflow: hidden;
        }
        .banner::before {
            content: ""; position: absolute; inset: 0;
            background-image: radial-gradient(rgba(234,32,39,.35) 1.4px, transparent 1.4px);
            background-size: 14px 14px; opacity: .5; pointer-events: none;
        }
        .banner-top { display: flex; align-items: center; gap: 10px; position: relative; }
        .banner-top h2 { font-size: 20px; }
        .status-badge { font-size: 11px; font-weight: 700; background: #fff; color: var(--red-strong); padding: 4px 11px; border-radius: 99px; letter-spacing: .03em; }
        .banner p { position: relative; margin: 8px 0 0; font-size: 13.5px; color: var(--text-dim); }
        .banner .close { position: absolute; top: 18px; right: 20px; width: 26px; height: 26px; border-radius: 8px; background: rgba(255,255,255,.1); display: flex; align-items: center; justify-content: center; cursor: pointer; border: 0; color: #fff; }
        .banner .close svg { width: 13px; height: 13px; }

        /* ===== panel 2: date bar ===== */
        .datebar { display: flex; align-items: center; gap: 10px; background: var(--panel); border: 1px solid var(--border); border-radius: 11px; padding: 11px 18px; font-size: 12.5px; color: var(--text-dim); }
        .datebar svg { width: 15px; height: 15px; color: var(--red); flex: 0 0 auto; }
        .datebar .refresh-btn { margin-left: auto; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #fff; background: var(--red); padding: 6px 12px; border-radius: 8px; cursor: pointer; border: 0; text-decoration: none; }
        .datebar .refresh-btn:hover { background: var(--red-strong); }
        .datebar .refresh-btn svg { width: 12px; height: 12px; color: #fff; }

        /* ===== panel 3: kpi cards ===== */
        .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .kpi { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; }
        .kpi .top { display: flex; align-items: center; justify-content: space-between; }
        .kpi .lbl { font-size: 12px; color: var(--text-dim); font-weight: 600; }
        .kpi .icon { width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center; background: var(--red-soft); }
        .kpi .icon svg { width: 15px; height: 15px; color: var(--red); }
        .kpi.aktif .icon { background: rgba(62,199,118,.16); }
        .kpi.aktif .icon svg { color: #4ade80; }
        .kpi.standby .icon { background: rgba(240,169,59,.18); }
        .kpi.standby .icon svg { color: #f0a93b; }
        .kpi .val { font-size: 32px; font-weight: 700; margin-top: 12px; font-family: 'Space Grotesk', sans-serif; }
        .kpi .foot { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; }

        /* ===== section headers & toolbar ===== */
        .sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
        .sec-head h2 { font-size: 15.5px; }
        .sec-head .hint { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .search { display: flex; align-items: center; gap: 7px; background: var(--panel); border: 1px solid var(--border); border-radius: 9px; padding: 8px 12px; }
        .search svg { width: 14px; height: 14px; color: var(--text-faint); }
        .search input { background: transparent; border: 0; outline: 0; color: var(--text); font-size: 12.5px; width: 160px; font-family: inherit; }
        .select { background: var(--panel); border: 1px solid var(--border); color: var(--text-dim); font-size: 12px; padding: 8px 11px; border-radius: 9px; font-family: inherit; cursor: pointer; outline: 0; }

        /* ===== panel 4: gantt ===== */
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

        .block { position: absolute; top: 8px; bottom: 8px; border-radius: 7px; padding: 0 8px; display: flex; align-items: center; font-size: 10px; font-weight: 700; color: #221417; overflow: hidden; white-space: nowrap; cursor: pointer; border: 1px solid rgba(0,0,0,.15); transition: transform .1s, box-shadow .1s; }
        .block:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.4); z-index: 4; }
        .block.dark { color: #f6f5f4; }
        .b-fid4 { outline: 2px solid var(--red); outline-offset: -3px; }

        .legend-hint { display: flex; align-items: center; gap: 7px; font-size: 11.5px; color: var(--text-faint); padding: 10px 18px; border-top: 1px solid var(--border-soft); }
        .legend-hint svg { width: 13px; height: 13px; color: var(--red); }
        .legend-hint b { color: var(--text-dim); }

        /* ===== panel 5: table ===== */
        .table-wrap { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead th { position: sticky; top: 0; background: var(--panel-2); text-align: left; font-size: 10.5px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: .05em; padding: 12px 16px; border-bottom: 1px solid var(--border); white-space: nowrap; }
        tbody td { padding: 13px 16px; font-size: 12.5px; border-bottom: 1px solid var(--border-soft); white-space: nowrap; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--panel-2); }
        .rig-cell { display: flex; flex-direction: column; gap: 2px; }
        .rig-cell .n { font-weight: 700; }
        .rig-cell .s { font-size: 10.5px; color: var(--text-faint); }
        .status-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; padding: 5px 10px; border-radius: 99px; }
        .status-chip.aktif { background: rgba(62,199,118,.16); color: #4ade80; }
        .status-chip.standby { background: rgba(240,169,59,.18); color: #f0a93b; }
        .status-chip.bermasalah { background: rgba(234,32,39,.18); color: #ff6b6f; }
        .status-chip .d { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .prog-mini { display: flex; align-items: center; gap: 8px; }
        .prog-track-mini { width: 70px; height: 6px; border-radius: 99px; background: var(--panel-2); overflow: hidden; }
        .prog-fill-mini { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--red-strong), var(--red)); }
        .prog-mini span { font-size: 11px; color: var(--text-dim); font-family: 'IBM Plex Mono', monospace; }
        .table-scroll { overflow-x: auto; }

        /* ===== Tooltip ===== */
        .tt { position: fixed; z-index: 60; background: var(--panel-3); border: 1px solid var(--border); border-radius: 10px; padding: 12px 14px; font-size: 12px; color: var(--text); max-width: 260px; box-shadow: 0 10px 30px rgba(0,0,0,.5); display: none; pointer-events: none; }
        .tt.show { display: block; }
        .tt .tt-t { font-weight: 700; font-size: 12.5px; margin-bottom: 4px; }
        .tt .tt-s { color: var(--text-dim); font-size: 11px; margin-bottom: 6px; }
        .tt .tt-n { color: var(--text-dim); line-height: 1.5; font-size: 11.5px; }
        .tt .tt-tag { display: inline-block; margin-top: 7px; font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 99px; background: var(--panel-2); color: var(--text-dim); }

        @media (max-width: 980px) {
            .kpi-row { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    <!-- Panel 1: Status Banner -->
    <div class="banner" x-data="{ showBanner: true }" x-show="showBanner">
        <div class="banner-top">
            <h2>Maintenance</h2>
            <span class="status-badge">INFO</span>
        </div>
        <p>Hai, saat ini semua sistem berjalan dengan aman, ayo lanjutkan manajemen datanya.</p>
        <button class="close" @click="showBanner = false" type="button" aria-label="Tutup Banner">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    <!-- Panel 2: Tanggal Update -->
    <div class="datebar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-2.6-6.36"/><path d="M21 4v5h-5"/></svg>
        <span>Data diperbarui <b>30 Juli 2026</b></span>
        <button class="refresh-btn" type="button" onclick="window.location.reload()">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M21 12a9 9 0 11-2.6-6.36"/><path d="M21 4v5h-5"/></svg>
            Refresh
        </button>
    </div>

    <!-- Panel 3: KPI Cards (Real Data) -->
    <div class="kpi-row">
        <div class="kpi">
            <div class="top">
                <span class="lbl">Total Alat</span>
                <span class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
                </span>
            </div>
            <div class="val">{{ $totalRig }}</div>
            <div class="foot">Rig terpasang di Zona 4</div>
        </div>

        <div class="kpi aktif">
            <div class="top">
                <span class="lbl">Alat Aktif</span>
                <span class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l4 4L19 6"/></svg>
                </span>
            </div>
            <div class="val">{{ $rigAktif }}</div>
            <div class="foot">{{ $rigAktifPercent }}% dari total alat</div>
        </div>

        <div class="kpi standby">
            <div class="top">
                <span class="lbl">Alat Tidak Aktif</span>
                <span class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                </span>
            </div>
            <div class="val">{{ $rigStandby }}</div>
            <div class="foot">Standby / proses pengadaan</div>
        </div>

        <div class="kpi">
            <div class="top">
                <span class="lbl">Alat Bermasalah</span>
                <span class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01M10.3 3.9L2.7 18a2 2 0 001.8 3h15a2 2 0 001.8-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                </span>
            </div>
            <div class="val">{{ $rigBermasalah }}</div>
            <div class="foot">Kontrak habis, perlu re-tender</div>
        </div>
    </div>

    <!-- Panel 4: Jadwal & Progres (Gantt Chart Real Data) -->
    <div class="sec-head">
        <div>
            <h2>Jadwal &amp; Progres</h2>
            <div class="hint">Klik/hover blok untuk detail sumur · scroll untuk melihat Jun 2026 – Des 2027</div>
        </div>
        <div class="toolbar">
            <div class="search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                <input id="rigSearch" type="text" placeholder="Cari rig atau sumur…">
            </div>
        </div>
    </div>

    <div class="gantt-wrap">
        <div class="gantt-scroll">
            <div class="gantt" id="ganttGrid">
                <!-- Header -->
                <div class="g-head">
                    <div class="corner">Rig</div>
                    @foreach ($months as $m)
                        <div class="{{ $m['is_now'] ? 'now' : '' }}">{{ $m['label'] }}</div>
                    @endforeach
                    <div class="plancol">Plan</div>
                    <div class="plancol">Prognose</div>
                </div>

                <!-- Rows from DB -->
                @foreach ($ganttRigs as $rig)
                    <div class="g-row" data-rig-name="{{ strtolower($rig['nama']) }}" data-rig-wells="{{ strtolower(implode(' ', array_column($rig['blocks']->toArray(), 'well'))) }}">
                        <div class="g-rig">
                            <div class="name" title="{{ $rig['nama'] }}">{{ $rig['nama'] }}</div>
                            <div class="num">Rig {{ $rig['no'] }}</div>
                        </div>

                        @for ($i = 0; $i < 19; $i++)
                            <div class="g-cell {{ $i === 1 ? 'now-col' : '' }}" data-col-index="{{ $i }}">
                                @foreach ($rig['blocks'] as $block)
                                    @if ($block['startIndex'] == $i)
                                        <div class="block {{ $block['isDark'] ? 'dark' : '' }}"
                                             style="width: calc({{ $block['duration'] * 92 - 6 }}px); left: 3px; background-color: {{ $block['warna'] }}; {{ $block['gaya'] === 'dashed' ? 'border: 2px dashed rgba(255,255,255,0.4);' : '' }} {{ $block['gaya'] === 'border' ? 'border: 2px solid var(--red);' : '' }}"
                                             data-title="{{ $block['label'] }}"
                                             data-sub="{{ $rig['nama'] }} · {{ $block['well'] }}"
                                             data-note="{{ $block['catatan'] }}"
                                             data-tag="{{ ucfirst($block['status']) }}"
                                             onmouseenter="showTip(event, this)"
                                             onmousemove="moveTip(event)"
                                             onmouseleave="hideTip()">
                                            <span>{{ $block['label'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endfor

                        <div class="g-plan plan">{{ $rig['plan'] }}</div>
                        <div class="g-plan prognose">{{ $rig['prognose'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="legend-hint">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5V4.6c0-.9.7-1.6 1.6-1.6H16l4 4v12.5a1.6 1.6 0 01-1.6 1.6H5.6A1.6 1.6 0 014 19.5z"/></svg>
            Keterangan lengkap warna & pola blok tersedia di menu <b>Dokumentasi</b>
        </div>
    </div>

    <!-- Panel 5: Daftar Alat (Table Real Data) -->
    <div class="sec-head">
        <div>
            <h2>Daftar Alat</h2>
            <div class="hint">{{ $totalRig }} rig — status, lokasi, sumur aktif dan progres saat ini</div>
        </div>
        <div class="toolbar">
            <div class="search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                <input id="tableSearch" type="text" placeholder="Cari nama rig…">
            </div>
            <select class="select" id="statusFilterTable">
                <option value="">Semua status</option>
                <option value="aktif">Aktif</option>
                <option value="standby">Tidak Aktif</option>
                <option value="bermasalah">Bermasalah</option>
            </select>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table-scroll">
            <table id="rigTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Rig</th>
                        <th>Status</th>
                        <th>Zona / Lokasi</th>
                        <th>Sumur Aktif</th>
                        <th>Progres</th>
                        <th>Update Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ganttRigs as $rig)
                        <tr data-name="{{ strtolower($rig['nama']) }}" data-status="{{ $rig['status'] }}">
                            <td>{{ $rig['no'] }}</td>
                            <td>
                                <div class="rig-cell">
                                    <span class="n">{{ $rig['nama'] }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($rig['status'] === 'aktif')
                                    <span class="status-chip aktif"><span class="d"></span>Aktif</span>
                                @elseif ($rig['status'] === 'standby')
                                    <span class="status-chip standby"><span class="d"></span>Tidak Aktif</span>
                                @else
                                    <span class="status-chip bermasalah"><span class="d"></span>Bermasalah</span>
                                @endif
                            </td>
                            <td>{{ $rig['zona'] }}</td>
                            <td>{{ $rig['sumurAktif'] }}</td>
                            <td>
                                <div class="prog-mini">
                                    <div class="prog-track-mini">
                                        <div class="prog-fill-mini" style="width: {{ $rig['progres'] }}%"></div>
                                    </div>
                                    <span>{{ $rig['progres'] }}%</span>
                                </div>
                            </td>
                            <td>30 Jul 2026</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tooltip Element -->
    <div class="tt" id="tooltip">
        <div class="tt-t" id="ttTitle"></div>
        <div class="tt-s" id="ttSub"></div>
        <div class="tt-n" id="ttNote"></div>
        <span class="tt-tag" id="ttTag"></span>
    </div>

    <script>
        // Tooltip logic
        const tip = document.getElementById('tooltip');
        const ttTitle = document.getElementById('ttTitle');
        const ttSub = document.getElementById('ttSub');
        const ttNote = document.getElementById('ttNote');
        const ttTag = document.getElementById('ttTag');

        function showTip(e, el) {
            ttTitle.textContent = el.getAttribute('data-title') || '';
            ttSub.textContent = el.getAttribute('data-sub') || '';
            ttNote.textContent = el.getAttribute('data-note') || '';
            ttTag.textContent = el.getAttribute('data-tag') || '';
            tip.classList.add('show');
            moveTip(e);
        }

        function moveTip(e) {
            const x = Math.min(e.clientX + 14, window.innerWidth - 275);
            const y = Math.min(e.clientY + 14, window.innerHeight - 150);
            tip.style.left = x + 'px';
            tip.style.top = y + 'px';
        }

        function hideTip() {
            tip.classList.remove('show');
        }

        // Live Filtering for Gantt Chart
        const rigSearch = document.getElementById('rigSearch');
        if (rigSearch) {
            rigSearch.addEventListener('input', function(e) {
                const val = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.g-row');
                rows.forEach(row => {
                    const name = row.getAttribute('data-rig-name') || '';
                    const wells = row.getAttribute('data-rig-wells') || '';
                    const match = !val || name.includes(val) || wells.includes(val);
                    row.style.display = match ? 'contents' : 'none';
                });
            });
        }

        // Live Filtering for Table
        const tableSearch = document.getElementById('tableSearch');
        const statusFilter = document.getElementById('statusFilterTable');

        function filterTableRows() {
            const searchVal = (tableSearch ? tableSearch.value : '').toLowerCase().trim();
            const statusVal = statusFilter ? statusFilter.value : '';
            const rows = document.querySelectorAll('#rigTable tbody tr');

            rows.forEach(tr => {
                const name = tr.getAttribute('data-name') || '';
                const status = tr.getAttribute('data-status') || '';
                const matchName = !searchVal || name.includes(searchVal);
                const matchStatus = !statusVal || status === statusVal;

                tr.style.display = (matchName && matchStatus) ? '' : 'none';
            });
        }

        if (tableSearch) tableSearch.addEventListener('input', filterTableRows);
        if (statusFilter) statusFilter.addEventListener('change', filterTableRows);
    </script>
</x-app-layout>
