<x-app-layout :title="'Pertamina Admin Panel — Dokumentasi'" pageTitle="Dokumentasi" pageDesc="Keterangan pola &amp; warna yang dipakai di seluruh dashboard">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M4 19.5V4.6c0-.9.7-1.6 1.6-1.6H16l4 4v12.5a1.6 1.6 0 01-1.6 1.6H5.6A1.6 1.6 0 014 19.5z"/><path d="M15 3v4.4c0 .66.54 1.2 1.2 1.2H20"/></svg>
    </x-slot>

    @push('styles')
    <style>
        /* ===== Toolbar ===== */
        .sec-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .sec-head h2 { font-size: 15.5px; margin: 0; }
        .sec-head .hint { font-size: 11.5px; color: var(--text-faint); margin-top: 2px; }
        .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .search { display: flex; align-items: center; gap: 7px; background: var(--panel); border: 1px solid var(--border); border-radius: 9px; padding: 8px 12px; }
        .search svg { width: 14px; height: 14px; color: var(--text-faint); }
        .search input { background: transparent; border: 0; outline: 0; color: var(--text); font-size: 12.5px; width: 190px; font-family: inherit; }
        .btn-primary { display: flex; align-items: center; gap: 7px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; padding: 9px 15px; border-radius: 9px; cursor: pointer; border: 0; font-family: inherit; transition: background .15s; }
        .btn-primary svg { width: 14px; height: 14px; }
        .btn-primary:hover { background: var(--red-strong); }
        .btn-soft { display: flex; align-items: center; gap: 6px; background: var(--panel-2); color: var(--text-dim); font-size: 11.5px; font-weight: 700; padding: 7px 12px; border-radius: 8px; cursor: pointer; border: 1px solid var(--border-soft); font-family: inherit; transition: all .15s; }
        .btn-soft:hover { color: #fff; border-color: var(--red); }
        .btn-soft svg { width: 12px; height: 12px; }

        /* ===== Panel Legend ===== */
        .legend-panel { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .legend-panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; padding: 18px 20px; border-bottom: 1px solid var(--border-soft); }
        .legend-panel-head h3 { font-size: 15px; margin: 0; }
        .legend-panel-head p { font-size: 12px; color: var(--text-faint); margin: 4px 0 0; line-height: 1.5; }
        .panel-actions { display: flex; gap: 6px; flex: 0 0 auto; }
        .icon-btn { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: var(--panel-2); border: 1px solid var(--border-soft); transition: all .15s; }
        .icon-btn svg { width: 14px; height: 14px; color: var(--text-dim); }
        .icon-btn:hover { background: var(--red-soft); }
        .icon-btn.danger:hover svg { color: #ff6b6f; }

        .legend-items { padding: 16px 20px; display: flex; flex-direction: column; gap: 8px; }
        .legend-item { display: flex; align-items: center; gap: 12px; padding: 9px 12px; border-radius: 9px; background: var(--panel-2); }
        .legend-item .swatch { width: 22px; height: 22px; border-radius: 6px; flex: 0 0 auto; }
        .legend-item .swatch.solid { }
        .legend-item .swatch.dashed { background: transparent; border: 2px dashed; }
        .legend-item .swatch.border { background: transparent; border: 2px solid; }
        .legend-item .label { flex: 1; font-size: 12.5px; font-weight: 600; }
        .legend-item .style-tag { font-size: 9.5px; font-weight: 700; color: var(--text-faint); background: var(--panel-3); padding: 3px 8px; border-radius: 6px; }
        .legend-item .item-actions { display: flex; gap: 4px; }
        .empty-panel { padding: 24px; text-align: center; color: var(--text-faint); font-size: 12.5px; }
        .add-item-row { padding: 4px 20px 18px; }

        .empty-doc { padding: 60px 20px; text-align: center; color: var(--text-faint); font-size: 13px; }

        /* ===== Modal ===== */
        .overlay { position: fixed; inset: 0; background: rgba(10,8,9,.6); backdrop-filter: blur(2px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
        .overlay.show { display: flex; }
        .modal { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 460px; max-height: 88vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .modal.modal-confirm { max-width: 400px; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border-soft); }
        .modal-head h3 { font-size: 16px; margin: 0; }
        .modal-close { width: 28px; height: 28px; border-radius: 8px; background: var(--panel-2); display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .modal-close svg { width: 13px; height: 13px; color: var(--text-dim); }
        .modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 16px; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 22px; border-top: 1px solid var(--border-soft); }
        .btn-ghost { padding: 9px 16px; border-radius: 9px; background: var(--panel-2); color: var(--text-dim); font-size: 12.5px; font-weight: 700; cursor: pointer; border: 1px solid var(--border-soft); font-family: inherit; }
        .btn-ghost:hover { color: #fff; }
        .btn-danger { padding: 9px 16px; border-radius: 9px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; cursor: pointer; border: 0; font-family: inherit; }
        .btn-danger:hover { background: var(--red-strong); }

        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 11.5px; font-weight: 700; color: var(--text-dim); }
        .field label .req { color: var(--red); }
        .field input, .field select, .field textarea { background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 9px; padding: 10px 12px; color: var(--text); font-size: 12.5px; font-family: inherit; outline: 0; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--red); }
        .field textarea { resize: vertical; min-height: 60px; }
        .field .err { font-size: 10.5px; color: #ff6b6f; display: none; }
        .field.invalid input, .field.invalid select { border-color: #ff6b6f; }
        .field.invalid .err { display: block; }
        .color-row { display: flex; align-items: center; gap: 10px; }
        .color-row input[type=color] { width: 44px; height: 38px; padding: 2px; cursor: pointer; background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 8px; }
        .color-row input[type=text] { flex: 1; }
        .preview-row { display: flex; align-items: center; gap: 10px; background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 9px; padding: 10px 12px; }
        .preview-row .swatch { width: 22px; height: 22px; border-radius: 6px; flex: 0 0 auto; }
        .preview-row span { font-size: 12px; color: var(--text-faint); }

        .flash-alert { padding: 12px 16px; border-radius: 10px; background: rgba(74,222,128,.14); border: 1px solid rgba(74,222,128,.3); color: #4ade80; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; }
        .flash-alert.error { background: rgba(234,32,39,.14); border-color: rgba(234,32,39,.3); color: #ff6b6f; }
    </style>
    @endpush

    <!-- Flash message -->
    @if (session('success'))
        <div class="flash-alert" id="flashSuccess">
            <span>{{ session('success') }}</span>
            <span style="cursor:pointer;" onclick="this.parentElement.remove()">✕</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="flash-alert error">
            <div>
                @foreach ($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
            <span style="cursor:pointer;" onclick="this.parentElement.remove()">✕</span>
        </div>
    @endif

    <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="sec-head">
            <div>
                <h2>Legenda Dashboard</h2>
                <div class="hint" id="resultHint">{{ count($panelsData) }} panel legend</div>
            </div>
            <div class="toolbar">
                <div class="search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="searchInput" placeholder="Cari item legend…">
                </div>
                @if ($canManage)
                    <button class="btn-primary" id="btnAddPanel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                        Tambah Panel
                    </button>
                @endif
            </div>
        </div>

        <div id="panelsWrap" style="display:flex; flex-direction:column; gap:16px;"></div>
    </div>

    @if ($canManage)
        <!-- Modal Tambah/Edit Panel -->
        <div class="overlay" id="overlayPanel">
            <div class="modal">
                <form id="formPanel" method="POST" action="{{ route('dokumentasi.panels.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="panelFormMethod" value="POST">
                    <div class="modal-head">
                        <h3 id="panelFormTitle">Tambah Panel</h3>
                        <div class="modal-close" data-close="overlayPanel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></div>
                    </div>
                    <div class="modal-body">
                        <div class="field" id="fieldPTitle">
                            <label>Judul Panel <span class="req">*</span></label>
                            <input name="judul" id="pTitle" placeholder="cth. Legend Strategi" required>
                            <span class="err">Wajib diisi</span>
                        </div>
                        <div class="field">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" id="pDesc" placeholder="Deskripsi singkat panel ini…"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-ghost" data-close="overlayPanel">Batal</button>
                        <button type="submit" class="btn-primary" id="btnSavePanel">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tambah/Edit Item -->
        <div class="overlay" id="overlayItem">
            <div class="modal">
                <form id="formItem" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="itemFormMethod" value="POST">
                    <div class="modal-head">
                        <h3 id="itemFormTitle">Tambah Item</h3>
                        <div class="modal-close" data-close="overlayItem"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></div>
                    </div>
                    <div class="modal-body">
                        <div class="field" id="fieldILabel">
                            <label>Label <span class="req">*</span></label>
                            <input name="label" id="iLabel" placeholder="cth. Sudah Tajak" required>
                            <span class="err">Wajib diisi</span>
                        </div>
                        <div class="field">
                            <label>Gaya Tampilan <span class="req">*</span></label>
                            <select name="gaya" id="iStyle" required>
                                <option value="solid">Warna Solid</option>
                                <option value="dashed">Border Putus-putus</option>
                                <option value="border">Border Solid</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Warna</label>
                            <div class="color-row">
                                <input type="color" id="iColorPicker" value="#3ec776">
                                <input type="text" name="warna" id="iColorText" value="#3ec776" required pattern="^#[0-9a-fA-F]{6}$" title="Kode warna HEX valid (#rrggbb)">
                            </div>
                        </div>
                        <div class="field">
                            <label>Pratinjau</label>
                            <div class="preview-row">
                                <div class="swatch" id="iPreviewSwatch"></div>
                                <span id="iPreviewLabel">Label item</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-ghost" data-close="overlayItem">Batal</button>
                        <button type="submit" class="btn-primary" id="btnSaveItem">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus Panel / Item -->
        <div class="overlay" id="overlayConfirm">
            <div class="modal modal-confirm">
                <form id="formConfirmDelete" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-head">
                        <h3 id="confirmTitle">Hapus?</h3>
                        <div class="modal-close" data-close="overlayConfirm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></div>
                    </div>
                    <div class="modal-body">
                        <p style="font-size:13px; color:var(--text-dim); line-height:1.6; margin:0;" id="confirmText"></p>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-ghost" data-close="overlayConfirm">Batal</button>
                        <button type="submit" class="btn-danger" id="btnConfirmDelete">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        const PANELS_DATA = @json($panelsData, JSON_UNESCAPED_SLASHES);
        const CAN_MANAGE = @json($canManage);

        function swatchStyle(it) {
            if (it.style === "solid") return "background:" + it.color;
            if (it.style === "dashed") return "border-color:" + it.color;
            return "border-color:" + it.color;
        }

        function styleLabel(s) {
            return s === "solid" ? "Warna Solid" : (s === "dashed" ? "Border Putus-putus" : "Border Solid");
        }

        function renderPanels() {
            const q = (document.getElementById('searchInput').value || "").toLowerCase().trim();
            const wrap = document.getElementById('panelsWrap');

            if (!PANELS_DATA.length) {
                wrap.innerHTML = '<div class="legend-panel"><div class="empty-doc">Belum ada panel legend.' + (CAN_MANAGE ? ' Klik "Tambah Panel" untuk membuat yang pertama.' : '') + '</div></div>';
                document.getElementById('resultHint').textContent = "0 panel legend";
                return;
            }

            let matchPanelCount = 0;

            const html = PANELS_DATA.map(panel => {
                const titleMatch = panel.title.toLowerCase().includes(q) || (panel.desc && panel.desc.toLowerCase().includes(q));
                const items = panel.items.filter(it => !q || titleMatch || it.label.toLowerCase().includes(q));

                if (q && !titleMatch && items.length === 0) {
                    return '';
                }

                matchPanelCount++;

                const itemsHtml = items.length ? items.map(it => `
                    <div class="legend-item" data-item="${it.id}">
                        <div class="swatch ${it.style}" style="${swatchStyle(it)}"></div>
                        <div class="label">${escapeHtml(it.label)}</div>
                        <div class="style-tag">${styleLabel(it.style)}</div>
                        ${CAN_MANAGE ? `
                            <div class="item-actions">
                                <div class="icon-btn" data-edit-item="${panel.id}:${it.id}" title="Edit item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg></div>
                                <div class="icon-btn danger" data-del-item="${panel.id}:${it.id}" title="Hapus item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg></div>
                            </div>
                        ` : ''}
                    </div>
                `).join('') : '<div class="empty-panel">Tidak ada item yang cocok.</div>';

                return `
                    <div class="legend-panel">
                        <div class="legend-panel-head">
                            <div>
                                <h3>${escapeHtml(panel.title)}</h3>
                                <p>${escapeHtml(panel.desc || '')}</p>
                            </div>
                            ${CAN_MANAGE ? `
                                <div class="panel-actions">
                                    <div class="icon-btn" data-edit-panel="${panel.id}" title="Edit panel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg></div>
                                    <div class="icon-btn danger" data-del-panel="${panel.id}" title="Hapus panel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg></div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="legend-items">${itemsHtml}</div>
                        ${CAN_MANAGE ? `
                            <div class="add-item-row"><button class="btn-soft" data-add-item="${panel.id}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>Tambah Item</button></div>
                        ` : ''}
                    </div>
                `;
            }).join('');

            wrap.innerHTML = html || '<div class="legend-panel"><div class="empty-panel">Tidak ada panel atau item yang cocok dengan pencarian.</div></div>';
            document.getElementById('resultHint').textContent = matchPanelCount + " panel legend";
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function openOverlay(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('show');
        }

        function closeOverlay(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('show');
        }

        document.querySelectorAll('[data-close]').forEach(el => {
            el.addEventListener('click', () => closeOverlay(el.dataset.close));
        });

        document.querySelectorAll('.overlay').forEach(ov => {
            ov.addEventListener('click', (e) => {
                if (e.target === ov) closeOverlay(ov.id);
            });
        });

        if (CAN_MANAGE) {
            // ---- Panel CRUD ----
            const btnAddPanel = document.getElementById('btnAddPanel');
            if (btnAddPanel) {
                btnAddPanel.addEventListener('click', () => {
                    const form = document.getElementById('formPanel');
                    form.action = "{{ route('dokumentasi.panels.store') }}";
                    document.getElementById('panelFormMethod').value = "POST";
                    document.getElementById('panelFormTitle').textContent = "Tambah Panel";
                    document.getElementById('pTitle').value = "";
                    document.getElementById('pDesc').value = "";
                    document.getElementById('fieldPTitle').classList.remove('invalid');
                    openOverlay('overlayPanel');
                });
            }

            function openEditPanel(id) {
                const panel = PANELS_DATA.find(p => p.id === id);
                if (!panel) return;
                const form = document.getElementById('formPanel');
                form.action = `/dokumentasi/panels/${id}`;
                document.getElementById('panelFormMethod').value = "PUT";
                document.getElementById('panelFormTitle').textContent = "Edit Panel";
                document.getElementById('pTitle').value = panel.title;
                document.getElementById('pDesc').value = panel.desc || "";
                document.getElementById('fieldPTitle').classList.remove('invalid');
                openOverlay('overlayPanel');
            }

            function openDeletePanel(id) {
                const panel = PANELS_DATA.find(p => p.id === id);
                if (!panel) return;
                const form = document.getElementById('formConfirmDelete');
                form.action = `/dokumentasi/panels/${id}`;
                document.getElementById('confirmTitle').textContent = "Hapus Panel?";
                document.getElementById('confirmText').innerHTML = `Panel <b style="color:var(--text)">${escapeHtml(panel.title)}</b> beserta ${panel.items.length} item di dalamnya akan dihapus permanen.`;
                openOverlay('overlayConfirm');
            }

            // ---- Item CRUD ----
            function syncColorInputs(fromPicker) {
                const picker = document.getElementById('iColorPicker');
                const text = document.getElementById('iColorText');
                if (fromPicker) {
                    text.value = picker.value;
                } else {
                    if (/^#[0-9a-fA-F]{6}$/.test(text.value)) {
                        picker.value = text.value;
                    }
                }
                updatePreview();
            }

            function updatePreview() {
                const style = document.getElementById('iStyle').value;
                const color = document.getElementById('iColorText').value || "#888888";
                const label = document.getElementById('iLabel').value || "Label item";
                const sw = document.getElementById('iPreviewSwatch');
                sw.className = "swatch " + style;
                sw.style.cssText = style === "solid" ? "background:" + color : "border-color:" + color;
                document.getElementById('iPreviewLabel').textContent = label;
            }

            document.getElementById('iColorPicker')?.addEventListener('input', () => syncColorInputs(true));
            document.getElementById('iColorText')?.addEventListener('input', () => syncColorInputs(false));
            document.getElementById('iStyle')?.addEventListener('change', updatePreview);
            document.getElementById('iLabel')?.addEventListener('input', updatePreview);

            function openAddItem(panelId) {
                const form = document.getElementById('formItem');
                form.action = `/dokumentasi/panels/${panelId}/items`;
                document.getElementById('itemFormMethod').value = "POST";
                document.getElementById('itemFormTitle').textContent = "Tambah Item";
                document.getElementById('iLabel').value = "";
                document.getElementById('iStyle').value = "solid";
                document.getElementById('iColorPicker').value = "#3ec776";
                document.getElementById('iColorText').value = "#3ec776";
                document.getElementById('fieldILabel').classList.remove('invalid');
                updatePreview();
                openOverlay('overlayItem');
            }

            function openEditItem(panelId, itemId) {
                const panel = PANELS_DATA.find(p => p.id === panelId);
                if (!panel) return;
                const it = panel.items.find(x => x.id === itemId);
                if (!it) return;

                const form = document.getElementById('formItem');
                form.action = `/dokumentasi/items/${itemId}`;
                document.getElementById('itemFormMethod').value = "PUT";
                document.getElementById('itemFormTitle').textContent = "Edit Item";
                document.getElementById('iLabel').value = it.label;
                document.getElementById('iStyle').value = it.style;
                document.getElementById('iColorPicker').value = it.color;
                document.getElementById('iColorText').value = it.color;
                document.getElementById('fieldILabel').classList.remove('invalid');
                updatePreview();
                openOverlay('overlayItem');
            }

            function openDeleteItem(panelId, itemId) {
                const panel = PANELS_DATA.find(p => p.id === panelId);
                if (!panel) return;
                const it = panel.items.find(x => x.id === itemId);
                if (!it) return;

                const form = document.getElementById('formConfirmDelete');
                form.action = `/dokumentasi/items/${itemId}`;
                document.getElementById('confirmTitle').textContent = "Hapus Item?";
                document.getElementById('confirmText').innerHTML = `Item <b style="color:var(--text)">${escapeHtml(it.label)}</b> akan dihapus dari panel ${escapeHtml(panel.title)}.`;
                openOverlay('overlayConfirm');
            }

            // Delegated events for dynamic elements
            document.getElementById('panelsWrap').addEventListener('click', (e) => {
                const editPanel = e.target.closest('[data-edit-panel]');
                const delPanel = e.target.closest('[data-del-panel]');
                const addItem = e.target.closest('[data-add-item]');
                const editItem = e.target.closest('[data-edit-item]');
                const delItem = e.target.closest('[data-del-item]');

                if (editPanel) openEditPanel(parseInt(editPanel.dataset.editPanel));
                else if (delPanel) openDeletePanel(parseInt(delPanel.dataset.delPanel));
                else if (addItem) openAddItem(parseInt(addItem.dataset.addItem));
                else if (editItem) {
                    const [pid, iid] = editItem.dataset.editItem.split(':').map(Number);
                    openEditItem(pid, iid);
                } else if (delItem) {
                    const [pid, iid] = delItem.dataset.delItem.split(':').map(Number);
                    openDeleteItem(pid, iid);
                }
            });
        }

        document.getElementById('searchInput').addEventListener('input', renderPanels);

        // Initial render
        renderPanels();
    </script>
    @endpush
</x-app-layout>
