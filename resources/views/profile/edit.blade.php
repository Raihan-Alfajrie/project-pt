<x-app-layout :title="'Pertamina Admin Panel — Profil'" pageTitle="Profil" pageDesc="Pengaturan akun & sesi aktif">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20c1.8-4.2 5.4-6.4 7.5-6.4s5.7 2.2 7.5 6.4"/></svg>
    </x-slot>

    @push('styles')
    <style>
        /* ===== Profile Panels ===== */
        .panel { background: var(--panel); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px 24px; }
        .panel h2 { font-size: 16px; margin: 0; }
        .panel .sub { font-size: 12px; color: var(--text-faint); margin-top: 3px; }

        .profile-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 18px; flex-wrap: wrap; }
        .profile-id { display: flex; align-items: center; gap: 16px; }
        .big-avatar { width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(155deg, var(--red), var(--red-strong)); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; color: #fff; font-family: 'Space Grotesk', sans-serif; flex: 0 0 auto; text-transform: uppercase; }
        .profile-id h3 { font-size: 18px; margin: 0; }
        .profile-id .email { font-size: 12.5px; color: var(--text-faint); margin-top: 3px; }
        .verified-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; color: var(--green, #4ade80); background: rgba(74,222,128,.14); padding: 4px 10px; border-radius: 99px; margin-top: 8px; }
        .verified-badge svg { width: 11px; height: 11px; }
        .profile-actions { display: flex; flex-direction: column; gap: 8px; min-width: 125px; }
        .btn-logout { display: flex; align-items: center; justify-content: center; gap: 7px; background: rgba(234,32,39,.12); border: 1px solid rgba(234,32,39,.35); color: #ff6b6f; font-size: 12.5px; font-weight: 700; padding: 8px 15px; border-radius: 9px; cursor: pointer; font-family: inherit; transition: all .15s; width: 100%; text-decoration: none; }
        .btn-logout:hover { background: var(--red); border-color: var(--red); color: #fff; }
        .btn-logout svg { width: 14px; height: 14px; }
        .btn-edit { display: flex; align-items: center; justify-content: center; gap: 7px; background: var(--panel-2); border: 1px solid var(--border-soft); color: var(--text); font-size: 12.5px; font-weight: 700; padding: 8px 15px; border-radius: 9px; cursor: pointer; font-family: inherit; transition: all .15s; width: 100%; }
        .btn-edit:hover { border-color: var(--red); color: #fff; }
        .btn-edit svg { width: 14px; height: 14px; }

        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 24px; margin-top: 18px; }
        .dkv .k { font-size: 11px; color: var(--text-faint); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
        .dkv .v { font-size: 13.5px; margin-top: 4px; font-weight: 600; }
        .dkv .v.status { color: var(--amber, #f0a93b); }

        .session-card { margin-top: 16px; display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 12px; padding: 16px 18px; flex-wrap: wrap; }
        .session-left { display: flex; align-items: flex-start; gap: 12px; }
        .session-left .ic { width: 36px; height: 36px; border-radius: 9px; background: var(--panel-3); display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
        .session-left .ic svg { width: 17px; height: 17px; color: var(--text-dim); }
        .session-title { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 13px; font-weight: 700; }
        .session-badge { font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 99px; }
        .session-badge.device { background: var(--panel-3); color: var(--text-dim); }
        .session-badge.active { background: rgba(74,222,128,.16); color: var(--green, #4ade80); }
        .session-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 24px; margin-top: 10px; font-size: 11.5px; color: var(--text-faint); }
        .session-meta b { color: var(--text-dim); font-weight: 600; }

        .empty-connected { margin-top: 14px; font-size: 12.5px; color: var(--text-faint); line-height: 1.6; background: var(--panel-2); border-radius: 10px; padding: 16px 18px; }
        .panel-icon-title { display: flex; align-items: center; gap: 9px; }
        .panel-icon-title .ic { width: 30px; height: 30px; border-radius: 8px; background: var(--red-soft); display: flex; align-items: center; justify-content: center; }
        .panel-icon-title .ic svg { width: 15px; height: 15px; color: var(--red); }

        /* ===== Modal Edit ===== */
        .overlay { position: fixed; inset: 0; background: rgba(10,8,9,.6); backdrop-filter: blur(2px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
        .overlay.show { display: flex; }
        .modal { background: var(--panel); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 480px; max-height: 88vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--border-soft); }
        .modal-head h3 { font-size: 16px; margin: 0; }
        .modal-close { width: 28px; height: 28px; border-radius: 8px; background: var(--panel-2); display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .modal-close svg { width: 13px; height: 13px; color: var(--text-dim); }
        .modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 16px; }
        .modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 22px; border-top: 1px solid var(--border-soft); }
        .btn-ghost { padding: 9px 16px; border-radius: 9px; background: var(--panel-2); color: var(--text-dim); font-size: 12.5px; font-weight: 700; cursor: pointer; border: 1px solid var(--border-soft); font-family: inherit; }
        .btn-ghost:hover { color: #fff; }
        .btn-primary { display: flex; align-items: center; gap: 7px; background: var(--red); color: #fff; font-size: 12.5px; font-weight: 700; padding: 9px 15px; border-radius: 9px; cursor: pointer; border: 0; font-family: inherit; transition: background .15s; }
        .btn-primary:hover { background: var(--red-strong); }
        .btn-primary svg { width: 14px; height: 14px; }

        .avatar-edit-row { display: flex; align-items: center; gap: 14px; }
        .avatar-edit-row .big-avatar { width: 56px; height: 56px; font-size: 19px; }
        .avatar-note { font-size: 11.5px; color: var(--text-faint); line-height: 1.5; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 11.5px; font-weight: 700; color: var(--text-dim); }
        .field input { background: var(--panel-2); border: 1px solid var(--border-soft); border-radius: 9px; padding: 10px 12px; color: var(--text); font-size: 12.5px; font-family: inherit; outline: 0; }
        .field input:focus { border-color: var(--red); }
        .field .err { font-size: 10.5px; color: #ff6b6f; display: none; }
        .field.invalid input { border-color: #ff6b6f; }
        .field.invalid .err { display: block; }

        .flash-alert { padding: 12px 16px; border-radius: 10px; background: rgba(74,222,128,.14); border: 1px solid rgba(74,222,128,.3); color: #4ade80; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; }
        .flash-alert.error { background: rgba(234,32,39,.14); border-color: rgba(234,32,39,.3); color: #ff6b6f; }
    </style>
    @endpush

    <div style="width: 100%; max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
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
        <!-- Panel Profil Akun -->
        <div class="panel">
            <h2>Profil Akun</h2>
            <div class="sub">Informasi akun yang sedang digunakan untuk Pertamina Admin Panel.</div>
            <div class="profile-head">
                <div class="profile-id">
                    <div class="big-avatar" id="headAvatar" style="overflow:hidden;">
                        @if ($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama ?? $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                        @else
                            {{ $user->initials }}
                        @endif
                    </div>
                    <div>
                        <h3 id="headNama">{{ $user->nama ?? $user->name }}</h3>
                        <div class="email" id="headEmail">{{ $user->email }}</div>
                        <div class="verified-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                            Verified
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout" id="btnLogout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Logout
                        </button>
                    </form>
                    <button class="btn-edit" id="btnEdit" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"/></svg>
                        Edit Profil
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel Detail Akun -->
        <div class="panel">
            <h2>Detail Akun</h2>
            <div class="detail-grid">
                <div class="dkv">
                    <div class="k">Nama</div>
                    <div class="v" id="dNama">{{ $user->nama ?? $user->name }}</div>
                </div>
                <div class="dkv">
                    <div class="k">Email</div>
                    <div class="v" id="dEmail">{{ $user->email }}</div>
                </div>
                <div class="dkv">
                    <div class="k">Login Terakhir</div>
                    <div class="v" id="dLogin">{{ $lastLogin }}</div>
                </div>
                <div class="dkv">
                    <div class="k">Akun Dibuat</div>
                    <div class="v" id="dDibuat">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y, H:i') : '—' }}</div>
                </div>
                <div class="dkv">
                    <div class="k">Status</div>
                    <div class="v status">{{ ucfirst($user->status ?? 'online') }}</div>
                </div>
                <div class="dkv">
                    <div class="k">Peran</div>
                    <div class="v">{{ ucfirst($user->jabatan) }}</div>
                </div>
            </div>
        </div>

        <!-- Panel Sesi Aktif -->
        <div class="panel">
            <div class="panel-icon-title">
                <div class="ic">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="16" height="12" rx="2"/><path d="M2 20h20M9 20v-4h6v4"/></svg>
                </div>
                <h2>Sesi Aktif</h2>
            </div>
            <div class="sub" style="margin-left:39px">Perangkat yang sedang memiliki akses ke akun ini.</div>
            <div class="session-card">
                <div class="session-left">
                    <div class="ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="12" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <div>
                        <div class="session-title">
                            {{ $userAgent }}
                            <span class="session-badge device">Perangkat ini</span>
                            <span class="session-badge active">Aktif</span>
                        </div>
                        <div class="session-meta">
                            <div>IP terakhir: <b>{{ $ipAddress }}</b></div>
                            <div>Terakhir aktif: <b>{{ now()->translatedFormat('d M Y, H:i') }}</b></div>
                            <div>Dibuat: <b>{{ $user->created_at ? $user->created_at->translatedFormat('d M Y, H:i') : now()->translatedFormat('d M Y, H:i') }}</b></div>
                            <div>Kedaluwarsa: <b>{{ now()->addDays(7)->translatedFormat('d M Y, H:i') }}</b></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Perangkat & Aplikasi Terhubung -->
        <div class="panel">
            <div class="panel-icon-title">
                <div class="ic">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                </div>
                <h2>Perangkat &amp; Aplikasi Terhubung</h2>
            </div>
            <div class="empty-connected">Belum ada perangkat atau aplikasi eksternal yang terhubung ke akun ini.</div>
        </div>
    </div>

    <!-- Modal Edit Profil -->
    <div class="overlay" id="overlayEdit">
        <div class="modal">
            <form id="formEditProfile" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-head">
                    <h3>Edit Profil</h3>
                    <div class="modal-close" data-close="overlayEdit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></div>
                </div>
                <div class="modal-body">
                    <div class="avatar-edit-row">
                        <div class="big-avatar" id="editAvatarPreview" style="overflow:hidden; position:relative;">
                            @if ($user->foto_profil)
                                <img id="avatarImgPreview" src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->nama ?? $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                                <span id="avatarInitialsPreview" style="display:none;">{{ $user->initials }}</span>
                            @else
                                <img id="avatarImgPreview" src="" alt="" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:inherit;">
                                <span id="avatarInitialsPreview">{{ $user->initials }}</span>
                            @endif
                        </div>
                        <div style="flex:1">
                            <label for="fFotoProfil" class="btn-ghost" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; padding:7px 12px; margin-bottom:6px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px; height:14px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span>Pilih Foto Baru</span>
                            </label>
                            <input type="file" name="foto_profil" id="fFotoProfil" accept="image/jpeg,image/png,image/webp" style="display:none;">
                            @if ($user->foto_profil)
                                <label style="display:inline-flex; align-items:center; gap:6px; font-size:11.5px; color:#ff6b6f; margin-left:8px; cursor:pointer;">
                                    <input type="checkbox" name="hapus_foto" value="1" id="chkHapusFoto"> Hapus foto
                                </label>
                            @endif
                            <div class="avatar-note">Format: JPG, PNG, WEBP (maks. 2MB). Jika foto tidak dipilih, inisial nama digunakan otomatis.</div>
                        </div>
                    </div>
                    <div class="field" id="fieldFNama">
                        <label>Nama Lengkap <span style="color:var(--red)">*</span></label>
                        <input name="nama" id="fNama" value="{{ old('nama', $user->nama ?? $user->name) }}" placeholder="Nama Anda" required>
                        <span class="err">Nama wajib diisi</span>
                    </div>
                    <div class="field" id="fieldFEmail">
                        <label>Email <span style="color:var(--red)">*</span></label>
                        <input name="email" id="fEmail" type="email" value="{{ old('email', $user->email) }}" placeholder="nama@perusahaan.com" required>
                        <span class="err">Email wajib diisi</span>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" data-close="overlayEdit">Batal</button>
                    <button type="submit" class="btn-primary" id="btnSaveProfile">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4"><path d="M5 12l4 4L19 6"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function computeInitials(name) {
            const p = (name || 'User').trim().split(/\s+/);
            const i1 = p[0] ? p[0][0] : 'U';
            const i2 = p[1] ? p[1][0] : '';
            return (i1 + i2).toUpperCase();
        }

        const btnEdit = document.getElementById('btnEdit');
        const overlayEdit = document.getElementById('overlayEdit');
        const fNama = document.getElementById('fNama');
        const avatarImgPreview = document.getElementById('avatarImgPreview');
        const avatarInitialsPreview = document.getElementById('avatarInitialsPreview');
        const fFotoProfil = document.getElementById('fFotoProfil');
        const chkHapusFoto = document.getElementById('chkHapusFoto');

        if (btnEdit && overlayEdit) {
            btnEdit.addEventListener('click', () => {
                overlayEdit.classList.add('show');
            });
        }

        document.querySelectorAll('[data-close]').forEach(el => {
            el.addEventListener('click', () => {
                const target = document.getElementById(el.dataset.close);
                if (target) target.classList.remove('show');
            });
        });

        if (overlayEdit) {
            overlayEdit.addEventListener('click', (e) => {
                if (e.target === overlayEdit) overlayEdit.classList.remove('show');
            });
        }

        if (fFotoProfil) {
            fFotoProfil.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (avatarImgPreview) {
                            avatarImgPreview.src = event.target.result;
                            avatarImgPreview.style.display = 'block';
                        }
                        if (avatarInitialsPreview) {
                            avatarInitialsPreview.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                    if (chkHapusFoto) chkHapusFoto.checked = false;
                }
            });
        }

        if (chkHapusFoto) {
            chkHapusFoto.addEventListener('change', () => {
                if (chkHapusFoto.checked) {
                    if (avatarImgPreview) avatarImgPreview.style.display = 'none';
                    if (avatarInitialsPreview) {
                        avatarInitialsPreview.style.display = 'block';
                        avatarInitialsPreview.textContent = computeInitials(fNama.value);
                    }
                    if (fFotoProfil) fFotoProfil.value = '';
                }
            });
        }

        if (fNama && avatarInitialsPreview) {
            fNama.addEventListener('input', () => {
                if (!avatarImgPreview || avatarImgPreview.style.display === 'none' || !avatarImgPreview.src) {
                    avatarInitialsPreview.textContent = computeInitials(fNama.value);
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
