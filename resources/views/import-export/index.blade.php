<x-app-layout :title="'Pertamina Admin Panel — Import / Export'" pageTitle="Import / Export" pageDesc="Unduh &amp; unggah data dalam format Excel">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M12 3v12M8 11l4 4 4-4"/><path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
    </x-slot>

    @push('styles')
    <style>
        .impex-wrap {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* ===== Panel Common ===== */
        .panel {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-soft);
            flex-wrap: wrap;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .panel-title .ic {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--red-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            color: var(--red);
        }

        .panel-title .ic svg {
            width: 18px;
            height: 18px;
        }

        .panel-title h2 {
            font-size: 16px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            margin: 0;
        }

        .panel-title p {
            margin: 2px 0 0;
            font-size: 11.5px;
            color: var(--text-faint);
        }

        .panel-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .lbl {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 10px;
        }

        /* ===== Buttons & Controls ===== */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--red);
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 9px;
            cursor: pointer;
            border: 0;
            font-family: inherit;
            transition: background .15s ease;
        }

        .btn-primary:hover {
            background: var(--red-strong);
        }

        .btn-primary[disabled] {
            opacity: .45;
            cursor: not-allowed;
            background: var(--red);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 15px;
            border-radius: 9px;
            background: var(--panel-2);
            color: var(--text-dim);
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid var(--border-soft);
            font-family: inherit;
            transition: all .15s ease;
        }

        .btn-ghost:hover {
            color: #fff;
            border-color: var(--red);
        }

        .select {
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            color: var(--text);
            font-size: 12.5px;
            padding: 9px 12px;
            border-radius: 9px;
            font-family: inherit;
            cursor: pointer;
            min-width: 160px;
            outline: 0;
        }

        .select:focus {
            border-color: var(--red);
        }

        /* ===== Module Grid ===== */
        .mod-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        @media (max-width: 900px) {
            .mod-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .mod-card {
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all .15s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mod-card:hover {
            border-color: var(--border);
        }

        .mod-card:focus-visible,
        .radio:focus-visible,
        .dropzone:focus-visible,
        .seg button:focus-visible {
            outline: 2px solid var(--red);
            outline-offset: 2px;
        }

        .mod-card.on {
            border-color: var(--red);
            box-shadow: 0 0 0 1px var(--red);
        }

        .mod-card .ic {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--panel-3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            color: var(--text-dim);
        }

        .mod-card .ic svg {
            width: 17px;
            height: 17px;
        }

        .mod-card.on .ic {
            background: var(--red-soft);
            color: var(--red);
        }

        .mod-card .n {
            font-size: 13.5px;
            font-weight: 700;
        }

        .mod-card .c {
            font-size: 11px;
            color: var(--text-faint);
            margin-top: 2px;
            font-family: 'IBM Plex Mono', monospace;
        }

        /* ===== Filters & Format ===== */
        .filters {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .fgroup {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .fgroup label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-dim);
        }

        .seg {
            display: inline-flex;
            background: var(--panel-2);
            border-radius: 9px;
            padding: 3px;
            gap: 2px;
            border: 1px solid var(--border-soft);
        }

        .seg button {
            border: 0;
            background: transparent;
            color: var(--text-faint);
            font-size: 12px;
            font-weight: 700;
            padding: 8px 15px;
            border-radius: 7px;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s ease;
        }

        .seg button.on {
            background: var(--red);
            color: #fff;
        }

        .export-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            border-top: 1px dashed var(--border-soft);
            padding-top: 16px;
            margin-top: 4px;
        }

        .count-line {
            font-size: 13px;
            color: var(--text-dim);
        }

        .count-line b {
            color: var(--text);
            font-family: 'IBM Plex Mono', monospace;
        }

        /* ===== Stepper ===== */
        .stepper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-faint);
        }

        .step .num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--panel-3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            font-family: 'IBM Plex Mono', monospace;
        }

        .step.active {
            color: var(--text);
        }

        .step.active .num {
            background: var(--red);
            color: #fff;
        }

        .step.done {
            color: var(--text-dim);
        }

        .step.done .num {
            background: rgba(74,222,128,.2);
            color: var(--green);
        }

        .step-sep {
            width: 24px;
            height: 1px;
            background: var(--border-soft);
        }

        /* ===== Import Steps Grid ===== */
        .step-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 16px;
        }

        @media (max-width: 860px) {
            .step-grid {
                grid-template-columns: 1fr;
            }
        }

        .step-card {
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .step-card h3 {
            font-size: 13.5px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
        }

        .chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .mini-chip {
            font-size: 11px;
            font-weight: 600;
            background: var(--panel-3);
            color: var(--text-dim);
            padding: 4px 9px;
            border-radius: 99px;
            border: 1px solid var(--border-soft);
        }

        .mini-chip.req {
            color: var(--amber);
            border-color: rgba(240,169,59,.35);
        }

        .dropzone {
            border: 2px dashed var(--border);
            border-radius: 12px;
            padding: 32px 18px;
            text-align: center;
            cursor: pointer;
            transition: all .15s ease;
            background: var(--panel);
        }

        .dropzone:hover, .dropzone.drag {
            background: var(--red-soft);
            border-color: var(--red);
        }

        .dropzone svg {
            width: 32px;
            height: 32px;
            color: var(--red);
            margin: 0 auto 10px;
        }

        .dropzone .t {
            font-size: 13.5px;
            font-weight: 700;
        }

        .dropzone .s {
            font-size: 11.5px;
            color: var(--text-faint);
            margin-top: 4px;
        }

        .file-note {
            font-size: 11.5px;
            color: var(--text-faint);
            line-height: 1.5;
        }

        /* ===== Table Styling (History & Preview) ===== */
        .table-wrap {
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            overflow: hidden;
            background: var(--panel-2);
        }

        .table-scroll {
            overflow-x: auto;
            max-height: 380px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            position: sticky;
            top: 0;
            background: var(--panel-3);
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 11px 14px;
            border-bottom: 1px solid var(--border-soft);
            white-space: nowrap;
            z-index: 2;
        }

        tbody td {
            padding: 10px 14px;
            font-size: 12.5px;
            border-bottom: 1px solid var(--border-soft);
            white-space: nowrap;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: var(--panel-3);
        }

        .mono {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 99px;
        }

        .pill .d {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .pill.imp {
            background: rgba(122,180,245,.16);
            color: #7ab4f5;
        }

        .pill.exp {
            background: rgba(192,132,252,.16);
            color: #c084fc;
        }

        .pill.sukses {
            background: rgba(74,222,128,.16);
            color: #4ade80;
        }

        .pill.sebagian {
            background: rgba(240,169,59,.18);
            color: #f0a93b;
        }

        .pill.gagal {
            background: rgba(234,32,39,.18);
            color: #ff6b6f;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-faint);
            font-size: 12.5px;
        }

        .badge-admin {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(255,255,255,.2);
            color: #fff;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* ===== Preview & Summary Styles ===== */
        .sum-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .sum-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 99px;
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            color: var(--text-dim);
        }

        .sum-chip b {
            color: var(--text);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
        }

        .sum-chip .d {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .radio-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .radio {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--panel-2);
            border: 1px solid var(--border-soft);
            cursor: pointer;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-dim);
            transition: all .15s ease;
        }

        .radio.on {
            border-color: var(--red);
            color: var(--text);
        }

        .radio .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid var(--text-faint);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radio.on .dot {
            border-color: var(--red);
        }

        .radio.on .dot::after {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--red);
        }

        td.msg {
            white-space: normal;
            min-width: 220px;
            max-width: 360px;
            color: var(--text-dim);
            font-size: 12px;
        }

        td.msg.bad {
            color: #ff9a9d;
        }

        .pill.new {
            background: rgba(74,222,128,.16);
            color: #4ade80;
        }

        .pill.update {
            background: rgba(122,180,245,.16);
            color: #7ab4f5;
        }

        .pill.skip {
            background: var(--panel-3);
            color: var(--text-faint);
        }

        .pill.error {
            background: rgba(234,32,39,.18);
            color: #ff6b6f;
        }

        .action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .action-row .right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .alert.err {
            background: rgba(234,32,39,.15);
            border: 1px solid rgba(234,32,39,.35);
            color: #ff9a9d;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            line-height: 1.5;
            margin-top: 10px;
        }

        .toast {
            position: fixed;
            left: 50%;
            bottom: calc(24px + env(safe-area-inset-bottom, 0px));
            transform: translateX(-50%) translateY(20px);
            background: var(--panel-3);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 12.5px;
            font-weight: 600;
            padding: 12px 18px;
            border-radius: 11px;
            box-shadow: 0 12px 36px rgba(0,0,0,.45);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s, transform .2s;
            z-index: 200;
            max-width: 90vw;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .toast.err {
            border-color: #ff6b6f;
        }

        .toast.ok {
            border-color: var(--green);
        }
    </style>
    @endpush

    <div class="impex-wrap" x-data="impexFoundationApp()">
        {{-- PANEL 1: EXPORT DATA --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <div class="ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242M12 12v9m-4-4 4 4 4-4"/></svg>
                    </div>
                    <div>
                        <h2>Export Data</h2>
                        <p>Unduh data ke file Excel (.xlsx) atau CSV</p>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div>
                    <div class="lbl">1. Pilih Data</div>
                    <div class="mod-grid">
                        <div class="mod-card" :class="{ 'on': exMod === 'alat' }" @click="selectExMod('alat')" tabindex="0" role="button" @keydown.enter="selectExMod('alat')" @keydown.space.prevent="selectExMod('alat')">
                            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 21V9l8-6 8 6v12"/><path d="M9 21v-7h6v7"/></svg></div>
                            <div><div class="n">Alat</div><div class="c">{{ $counts['alat'] }} baris</div></div>
                        </div>
                        <div class="mod-card" :class="{ 'on': exMod === 'lokasi' }" @click="selectExMod('lokasi')" tabindex="0" role="button" @keydown.enter="selectExMod('lokasi')" @keydown.space.prevent="selectExMod('lokasi')">
                            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-7-6.2-7-11a7 7 0 1114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.4"/></svg></div>
                            <div><div class="n">Lokasi</div><div class="c">{{ $counts['lokasi'] }} baris</div></div>
                        </div>
                        <div class="mod-card" :class="{ 'on': exMod === 'jadwal' }" @click="selectExMod('jadwal')" tabindex="0" role="button" @keydown.enter="selectExMod('jadwal')" @keydown.space.prevent="selectExMod('jadwal')">
                            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg></div>
                            <div><div class="n">Jadwal</div><div class="c">{{ $counts['jadwal'] }} baris</div></div>
                        </div>
                        <div class="mod-card" :class="{ 'on': exMod === 'audit' }" @click="selectExMod('audit')" tabindex="0" role="button" @keydown.enter="selectExMod('audit')" @keydown.space.prevent="selectExMod('audit')">
                            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M6 3h9l5 5v13H6z"/><path d="M9 12h6M9 16h6M9 8h2"/></svg></div>
                            <div><div class="n">Audit Log</div><div class="c">{{ $counts['audit'] }} baris</div></div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="lbl">2. Filter &amp; Format</div>
                    <div class="filters">
                        {{-- Filter Alat --}}
                        <template x-if="exMod === 'alat'">
                            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                                <div class="fgroup">
                                    <label>Kategori</label>
                                    <select class="select" x-model="exFilter.kategori" @change="fetchCount()">
                                        <option value="">Semua Kategori</option>
                                        <option value="Rig">Rig</option>
                                        <option value="Alat Pendukung">Alat Pendukung</option>
                                    </select>
                                </div>
                                <div class="fgroup">
                                    <label>Status</label>
                                    <select class="select" x-model="exFilter.status" @change="fetchCount()">
                                        <option value="">Semua Status</option>
                                        <option value="Aktif">Aktif</option>
                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                        <option value="Bermasalah">Bermasalah</option>
                                    </select>
                                </div>
                            </div>
                        </template>

                        {{-- Filter Lokasi --}}
                        <template x-if="exMod === 'lokasi'">
                            <div class="fgroup">
                                <label>Provinsi</label>
                                <select class="select" x-model="exFilter.provinsi" @change="fetchCount()">
                                    <option value="">Semua Provinsi</option>
                                    @foreach($filters['lokasi'][0]['opts'] as $prov)
                                    <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>

                        {{-- Filter Jadwal --}}
                        <template x-if="exMod === 'jadwal'">
                            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                                <div class="fgroup">
                                    <label>Status</label>
                                    <select class="select" x-model="exFilter.status" @change="fetchCount()">
                                        <option value="">Semua Status</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Menunggu / Progres">Menunggu / Progres</option>
                                        <option value="Kendala">Kendala</option>
                                    </select>
                                </div>
                                <div class="fgroup">
                                    <label>Kategori</label>
                                    <select class="select" x-model="exFilter.kategori" @change="fetchCount()">
                                        <option value="">Semua Kategori</option>
                                        @foreach($filters['jadwal'][1]['opts'] as $kat)
                                        <option value="{{ $kat }}">{{ $kat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </template>

                        {{-- Filter Audit Log --}}
                        <template x-if="exMod === 'audit'">
                            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                                <div class="fgroup">
                                    <label>Aksi</label>
                                    <select class="select" x-model="exFilter.aksi" @change="fetchCount()">
                                        <option value="">Semua Aksi</option>
                                        <option value="Login">Login</option>
                                        <option value="Logout">Logout</option>
                                        <option value="Tambah">Tambah</option>
                                        <option value="Edit">Edit</option>
                                        <option value="Hapus">Hapus</option>
                                    </select>
                                </div>
                                <div class="fgroup">
                                    <label>Modul</label>
                                    <select class="select" x-model="exFilter.modul" @change="fetchCount()">
                                        <option value="">Semua Modul</option>
                                        <option value="Alat">Alat</option>
                                        <option value="Lokasi">Lokasi</option>
                                        <option value="Jadwal">Jadwal</option>
                                        <option value="User">User</option>
                                        <option value="Sistem">Sistem</option>
                                    </select>
                                </div>
                            </div>
                        </template>

                        <div class="fgroup">
                            <label>Format File</label>
                            <div class="seg">
                                <button type="button" :class="{ 'on': exFmt === 'xlsx' }" @click="exFmt = 'xlsx'">.xlsx</button>
                                <button type="button" :class="{ 'on': exFmt === 'csv' }" @click="exFmt = 'csv'">.csv</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="export-foot">
                    <div class="count-line">
                        <span x-html="countText"></span>
                    </div>
                    <button type="button" class="btn-primary" :disabled="currentCount === 0 || isExporting" @click="submitExport()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3"/></svg>
                        <span x-text="isExporting ? 'Menyiapkan...' : 'Unduh File'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- PANEL 2: IMPORT DATA --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <div class="ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242M12 12v9m-4-8 4-4 4 4"/></svg>
                    </div>
                    <div>
                        <h2>Import Data</h2>
                        <p>Unggah file Excel, periksa hasil validasi, lalu konfirmasi</p>
                    </div>
                </div>
                <div class="stepper">
                    <div class="step done"><span class="num">✓</span>Pilih modul &amp; template</div>
                    <span class="step-sep"></span>
                    <div class="step" :class="previewData ? 'done' : 'active'"><span class="num" x-text="previewData ? '✓' : '2'"></span>Unggah file</div>
                    <span class="step-sep"></span>
                    <div class="step" :class="previewData ? 'active' : ''"><span class="num">3</span>Pratinjau &amp; konfirmasi</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="step-grid">
                    {{-- Langkah 1 --}}
                    <div class="step-card">
                        <h3>Langkah 1 — Pilih modul &amp; template</h3>
                        <div>
                            <div class="seg" style="width:100%; display:flex;">
                                <button type="button" style="flex:1;" :class="{ 'on': imMod === 'alat' }" @click="selectImMod('alat')">Alat</button>
                                <button type="button" style="flex:1;" :class="{ 'on': imMod === 'lokasi' }" @click="selectImMod('lokasi')">Lokasi</button>
                                <button type="button" style="flex:1;" :class="{ 'on': imMod === 'jadwal' }" @click="selectImMod('jadwal')">Jadwal</button>
                            </div>
                        </div>

                        <div>
                            <div class="lbl" style="margin-bottom:6px;">Kolom di file:</div>
                            <div class="chip-row">
                                <template x-for="c in currentModCols" :key="c.name">
                                    <span class="mini-chip" :class="{ 'req': c.req }" :title="c.hint" x-text="c.name + (c.req ? ' *' : '')"></span>
                                </template>
                            </div>
                        </div>

                        <div class="file-note">
                            Kolom bertanda <b>*</b> wajib diisi. Urutan kolom bebas, nama kolom tidak peka huruf besar/kecil. Kunci pencocokan: <b x-text="currentKeyName"></b>.
                        </div>

                        <div>
                            <button type="button" class="btn-ghost" @click="downloadTemplate()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3"/></svg>
                                <span>Unduh template Excel</span>
                            </button>
                        </div>
                    </div>

                    {{-- Langkah 2 --}}
                    <div class="step-card">
                        <h3>Langkah 2 — Unggah file</h3>
                        <div class="dropzone" :class="{ 'drag': isDragging }"
                             tabindex="0" role="button"
                             @click="$refs.fileInput.click()"
                             @keydown.enter="$refs.fileInput.click()"
                             @keydown.space.prevent="$refs.fileInput.click()"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="isDragging = false; handleFileDrop($event)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242M12 12v9m-4-8 4-4 4 4"/></svg>
                            <div class="t" x-text="isUploading ? 'Memproses dan memvalidasi file...' : 'Tarik file ke sini, atau klik untuk memilih'"></div>
                            <div class="s">Format .xlsx, .xls, atau .csv (maks 5 MB / 1.000 baris)</div>
                        </div>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".xlsx,.xls,.csv" style="display:none">
                        <div x-show="uploadError" class="alert err" x-html="uploadError"></div>
                        <div class="file-note">
                            File akan diverifikasi per baris secara aman tanpa mengeksekusi makro atau rumus otomatis.
                        </div>
                    </div>
                </div>

                {{-- Langkah 3: Pratinjau & Konfirmasi --}}
                <div x-show="previewData !== null" style="display:flex; flex-direction:column; gap:16px; margin-top:20px;">
                    <div style="border-top:1px dashed var(--border-soft)"></div>
                    <div>
                        <div class="lbl">Langkah 3 — Pratinjau &amp; konfirmasi <span class="mono" style="text-transform:none; letter-spacing:0; margin-left:6px;" x-text="'· ' + (previewData ? previewData.filename : '')"></span></div>
                        <div class="sum-row">
                            <div class="sum-chip">Total <b x-text="previewTotal"></b></div>
                            <div class="sum-chip"><span class="d" style="background:var(--green)"></span>Baru <b x-text="summaryCounts.new"></b></div>
                            <div class="sum-chip"><span class="d" style="background:var(--blue)"></span>Perbarui <b x-text="summaryCounts.update"></b></div>
                            <div class="sum-chip"><span class="d" style="background:var(--text-faint)"></span>Dilewati <b x-text="summaryCounts.skip"></b></div>
                            <div class="sum-chip"><span class="d" style="background:#ff6b6f"></span>Error <b x-text="summaryCounts.error"></b></div>
                        </div>
                    </div>

                    <div>
                        <div class="lbl">Jika data sudah ada (berdasarkan <span x-text="currentKeyName"></span>)</div>
                        <div class="radio-row">
                            <div class="radio" :class="{ 'on': imMode === 'skip' }" @click="imMode = 'skip'" tabindex="0" role="radio" :aria-checked="imMode === 'skip'" @keydown.enter="imMode = 'skip'" @keydown.space.prevent="imMode = 'skip'">
                                <span class="dot"></span>Tambah data baru saja, lewati yang sudah ada
                            </div>
                            <div class="radio" :class="{ 'on': imMode === 'update' }" @click="imMode = 'update'" tabindex="0" role="radio" :aria-checked="imMode === 'update'" @keydown.enter="imMode = 'update'" @keydown.space.prevent="imMode = 'update'">
                                <span class="dot"></span>Tambah data baru &amp; perbarui yang sudah ada
                            </div>
                        </div>
                    </div>

                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Baris</th>
                                    <th>Status</th>
                                    <template x-for="col in previewCols" :key="col.k">
                                        <th x-text="col.h"></th>
                                    </template>
                                    <th>Pesan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="r in displayRows" :key="r.rowNo">
                                    <tr>
                                        <td class="mono" x-text="r.rowNo"></td>
                                        <td>
                                            <span class="pill" :class="rowClass(r)">
                                                <span class="d"></span>
                                                <span x-text="rowStatusLabel(r)"></span>
                                            </span>
                                        </td>
                                        <template x-for="col in previewCols" :key="col.k">
                                            <td x-text="r.rec[col.k] || ''"></td>
                                        </template>
                                        <td class="msg" :class="{ 'bad': (r.errs && r.errs.length > 0) }" x-text="rowMessage(r)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="file-note" x-show="previewData && previewData.rows && previewData.rows.length > 500" x-text="'Menampilkan 500 baris pertama dari ' + (previewData ? previewData.rows.length : 0) + '. Seluruh baris tetap diproses saat konfirmasi.'"></div>

                    <div class="action-row">
                        <button type="button" class="btn-ghost" :disabled="summaryCounts.error === 0 || isDownloadingErr" @click="downloadErrorReport()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5 5 5-5m-5 5V3"/></svg>
                            <span>Unduh laporan error</span>
                        </button>
                        <div class="right">
                            <button type="button" class="btn-ghost" @click="cancelImport()">Batal</button>
                            <button type="button" class="btn-primary" :disabled="importableCount === 0 || isConfirming" @click="confirmImport()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                                <span x-text="isConfirming ? 'Menyimpan...' : (importableCount > 0 ? ('Konfirmasi Import (' + importableCount + ' baris)') : 'Tidak ada baris yang bisa diimpor')"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PANEL 3: RIWAYAT IMPORT / EXPORT --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <div class="ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <h2>Riwayat Import / Export</h2>
                        <p>Catatan setiap unduhan dan unggahan data operasional</p>
                    </div>
                </div>
            </div>
            <div class="panel-body" style="padding:0;">
                <div class="table-wrap" style="border:0; border-radius:0;">
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Jenis</th>
                                    <th>Modul</th>
                                    <th>Nama File</th>
                                    <th>Baris</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayats as $rw)
                                <tr>
                                    <td class="mono">{{ $rw->waktu_display }}</td>
                                    <td>{{ $rw->user?->nama ?? $rw->user?->name ?? 'Administrator' }}</td>
                                    <td>
                                        <span class="pill {{ $rw->jenis === 'import' ? 'imp' : 'exp' }}">
                                            <span class="d"></span>
                                            {{ $rw->jenis_label }}
                                        </span>
                                    </td>
                                    <td>{{ $rw->modul_label }}</td>
                                    <td class="mono">{{ $rw->nama_file }}</td>
                                    <td class="mono">{{ $rw->baris_display }}</td>
                                    <td>
                                        <span class="pill {{ $rw->status }}">
                                            <span class="d"></span>
                                            {{ $rw->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">Belum ada riwayat import / export.</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOAST NOTIFICATION --}}
        <div class="toast" :class="{ 'show': toastMsg !== '', 'err': toastKind === 'err', 'ok': toastKind === 'ok' }" x-text="toastMsg"></div>
    </div>

    @push('scripts')
    <script>
        function impexFoundationApp() {
            const counts = @json($counts);
            const colsDef = {
                alat: [
                    { name: 'Nama', req: true, hint: 'Nama rig / alat' },
                    { name: 'Kategori', req: true, hint: 'Rig atau Alat Pendukung' },
                    { name: 'Merek', req: true, hint: 'Merek / pabrikan' },
                    { name: 'Model / Tipe', req: false, hint: 'Model atau tipe' },
                    { name: 'Tahun Perolehan', req: false, hint: 'Angka 1980–2026' },
                    { name: 'No. Seri', req: true, hint: 'Harus unik, sebagai kunci' },
                    { name: 'Status', req: false, hint: 'Aktif / Tidak Aktif / Bermasalah' },
                    { name: 'Keterangan', req: false, hint: 'Catatan bebas' }
                ],
                lokasi: [
                    { name: 'Kode Lapangan', req: true, hint: 'Unik, sebagai kunci' },
                    { name: 'Nama Tempat', req: true, hint: 'Nama lapangan / tempat' },
                    { name: 'Provinsi', req: true, hint: 'Nama provinsi resmi' },
                    { name: 'Kota / Kabupaten', req: false, hint: 'Boleh kosong' },
                    { name: 'Kecamatan', req: false, hint: 'Boleh kosong' },
                    { name: 'Keterangan', req: false, hint: 'Catatan bebas' }
                ],
                jadwal: [
                    { name: 'Rig', req: true, hint: 'Harus sama dengan nama rig di data Alat' },
                    { name: 'Nama Sumur / Pekerjaan', req: true, hint: 'Kunci bersama Rig' },
                    { name: 'Kategori', req: true, hint: 'Salah satu fase di menu Dokumentasi' },
                    { name: 'Status', req: false, hint: 'Selesai / Menunggu / Progres / Kendala' },
                    { name: 'Bulan Mulai', req: true, hint: 'Format 2026-06 atau Jun-26' },
                    { name: 'Durasi (bulan)', req: false, hint: 'Bilangan bulat 1–19' },
                    { name: 'Catatan', req: false, hint: 'Catatan bebas' }
                ]
            };

            const keyNames = {
                alat: 'No. Seri',
                lokasi: 'Kode Lapangan',
                jadwal: 'Rig + Nama Sumur / Pekerjaan'
            };

            return {
                exMod: 'alat',
                exFmt: 'xlsx',
                exFilter: {},
                currentCount: counts['alat'] || 0,
                isExporting: false,
                imMod: 'alat',
                imMode: 'skip',
                isDragging: false,
                isUploading: false,
                uploadError: null,
                previewData: null,
                isDownloadingErr: false,
                isConfirming: false,
                toastMsg: '',
                toastKind: 'ok',
                _toastTimer: null,

                get currentModCols() {
                    return colsDef[this.imMod] || [];
                },

                get currentKeyName() {
                    return keyNames[this.imMod] || '';
                },

                get countText() {
                    const modName = {
                        alat: 'Alat',
                        lokasi: 'Lokasi',
                        jadwal: 'Jadwal',
                        audit: 'Audit Log'
                    }[this.exMod] || '';
                    return `<b>${this.currentCount}</b> baris akan diekspor dari ${modName}`;
                },

                selectExMod(m) {
                    this.exMod = m;
                    this.exFilter = {};
                    this.currentCount = counts[m] || 0;
                    this.fetchCount();
                },

                selectImMod(m) {
                    if (this.imMod === m) return;
                    this.imMod = m;
                    this.cancelImport();
                },

                downloadTemplate() {
                    window.location.href = '{{ route('import-export.template') }}?modul=' + encodeURIComponent(this.imMod);
                },

                handleFileDrop(e) {
                    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                        this.uploadFile(e.dataTransfer.files[0]);
                    }
                },

                handleFileSelect(e) {
                    if (e.target && e.target.files && e.target.files[0]) {
                        this.uploadFile(e.target.files[0]);
                        e.target.value = '';
                    }
                },

                async uploadFile(file) {
                    this.uploadError = null;
                    const parts = file.name.split('.');
                    const ext = parts.length > 1 ? parts.pop().toLowerCase() : '';
                    if (!['xlsx', 'xls', 'csv'].includes(ext)) {
                        this.uploadError = 'File tidak bisa dibaca sebagai Excel/CSV. Pastikan formatnya .xlsx, .xls, atau .csv.';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        this.uploadError = 'Ukuran file melebihi batas maksimum 5 MB.';
                        return;
                    }

                    this.isUploading = true;

                    try {
                        const fd = new FormData();
                        fd.append('file', file);
                        fd.append('modul', this.imMod);

                        const res = await fetch('{{ route('import-export.upload-preview') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: fd
                        });

                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            this.uploadError = data.message || 'Gagal memproses file.';
                        } else {
                            this.previewData = data;
                            this.imMode = 'skip';
                        }
                    } catch (err) {
                        this.uploadError = 'Terjadi kesalahan jaringan saat mengunggah file.';
                    } finally {
                        this.isUploading = false;
                    }
                },

                rowClass(r) {
                    if (r.errs && r.errs.length > 0) return 'error';
                    if (r.existsInDb) return this.imMode === 'update' ? 'update' : 'skip';
                    return 'new';
                },

                rowStatusLabel(r) {
                    const c = this.rowClass(r);
                    return { new: 'Baru', update: 'Perbarui', skip: 'Dilewati', error: 'Error' }[c] || c;
                },

                rowMessage(r) {
                    const c = this.rowClass(r);
                    if (c === 'error') return (r.errs || []).join('; ');
                    if (c === 'skip') return 'Sudah ada — dilewati';
                    if (c === 'update') return 'Sudah ada — akan diperbarui';
                    return 'OK, akan ditambahkan';
                },

                get summaryCounts() {
                    if (!this.previewData || !this.previewData.rows) {
                        return { total: 0, new: 0, update: 0, skip: 0, error: 0 };
                    }
                    const cnt = { total: this.previewData.rows.length, new: 0, update: 0, skip: 0, error: 0 };
                    for (const r of this.previewData.rows) {
                        const c = this.rowClass(r);
                        cnt[c]++;
                    }
                    return cnt;
                },

                get previewTotal() {
                    return this.previewData && this.previewData.rows ? this.previewData.rows.length : 0;
                },

                get importableCount() {
                    const s = this.summaryCounts;
                    return s.new + s.update;
                },

                get previewCols() {
                    if (!this.previewData || !this.previewData.previewCols || !this.previewData.cols) return [];
                    const map = {};
                    for (const c of this.previewData.cols) {
                        map[c.k] = c;
                    }
                    return this.previewData.previewCols.map(k => map[k]).filter(Boolean);
                },

                get displayRows() {
                    return this.previewData && this.previewData.rows ? this.previewData.rows.slice(0, 500) : [];
                },

                cancelImport() {
                    this.previewData = null;
                    this.uploadError = null;
                    this.imMode = 'skip';
                },

                downloadErrorReport() {
                    if (!this.previewData || this.summaryCounts.error === 0) return;
                    this.isDownloadingErr = true;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('import-export.error-report') }}';
                    form.style.display = 'none';

                    const csrf = document.createElement('input');
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const tok = document.createElement('input');
                    tok.name = 'token';
                    tok.value = this.previewData.token;
                    form.appendChild(tok);

                    document.body.appendChild(form);
                    form.submit();

                    setTimeout(() => {
                        form.remove();
                        this.isDownloadingErr = false;
                    }, 1000);
                },

                showToast(msg, kind = 'ok') {
                    this.toastMsg = msg;
                    this.toastKind = kind;
                    if (this._toastTimer) clearTimeout(this._toastTimer);
                    this._toastTimer = setTimeout(() => {
                        this.toastMsg = '';
                    }, 3600);
                },

                async confirmImport() {
                    if (this.importableCount === 0 || this.isConfirming || !this.previewData) return;
                    this.isConfirming = true;

                    try {
                        const res = await fetch('{{ route('import-export.confirm') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                token: this.previewData.token,
                                mode: this.imMode
                            })
                        });

                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            this.showToast(data.message || 'Gagal menyimpan data import.', 'err');
                        } else {
                            this.showToast(data.message, 'ok');
                            this.cancelImport();
                            // Refresh counts and history after 1.5s
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    } catch (err) {
                        this.showToast('Terjadi kesalahan jaringan saat mengonfirmasi import.', 'err');
                    } finally {
                        this.isConfirming = false;
                    }
                },

                async fetchCount() {
                    try {
                        const res = await fetch('{{ route('import-export.count') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                modul: this.exMod,
                                filters: this.exFilter
                            })
                        });
                        const data = await res.json();
                        if (data && data.success) {
                            this.currentCount = data.count;
                        }
                    } catch (err) {
                        console.error('Gagal memperbarui hitungan baris:', err);
                    }
                },

                submitExport() {
                    if (this.currentCount === 0 || this.isExporting) return;
                    this.isExporting = true;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('import-export.export.post') }}';
                    form.style.display = 'none';

                    const csrf = document.createElement('input');
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const mod = document.createElement('input');
                    mod.name = 'modul';
                    mod.value = this.exMod;
                    form.appendChild(mod);

                    const fmt = document.createElement('input');
                    fmt.name = 'format';
                    fmt.value = this.exFmt;
                    form.appendChild(fmt);

                    for (const [k, v] of Object.entries(this.exFilter)) {
                        if (v) {
                            const fi = document.createElement('input');
                            fi.name = `filters[${k}]`;
                            fi.value = v;
                            form.appendChild(fi);
                        }
                    }

                    document.body.appendChild(form);
                    form.submit();

                    setTimeout(() => {
                        form.remove();
                        this.isExporting = false;
                        setTimeout(() => window.location.reload(), 1500);
                    }, 1000);
                },

                notifyUpcoming(msg) {
                    alert(msg);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
