<x-app-layout :title="'Pertamina Admin Panel — Notifikasi'" pageTitle="Notifikasi" pageDesc="Pusat pemberitahuan &amp; peringatan operasional">
    <x-slot name="pageIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
    </x-slot>

    @push('styles')
    <style>
        .notif-page-wrap {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 1040px;
            margin: 0 auto;
            width: 100%;
        }
        .notif-page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 16px 20px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }
        .notif-filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .pill-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .pill-btn {
            padding: 6px 13px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            color: var(--text-dim);
            background: var(--panel);
            border: 1px solid var(--border-soft);
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pill-btn:hover {
            color: var(--text);
            border-color: var(--border);
        }
        .pill-btn.active {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
            font-weight: 700;
        }
        .notif-list-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .page-notif-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-soft);
            text-decoration: none;
            color: inherit;
            transition: background .15s;
        }
        .page-notif-item:last-child {
            border-bottom: 0;
        }
        .page-notif-item:hover {
            background: var(--panel-2);
        }
        .page-notif-item.unread {
            background: rgba(234,32,39,.05);
        }
        .page-notif-item.unread:hover {
            background: rgba(234,32,39,.09);
        }
        .page-notif-ic {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .page-notif-ic svg {
            width: 20px;
            height: 20px;
        }
        .page-notif-ic.danger { background: rgba(234,32,39,.18); color: #ea2027; }
        .page-notif-ic.warning { background: rgba(245,158,11,.18); color: #f59e0b; }
        .page-notif-ic.success { background: rgba(16,185,129,.18); color: #10b981; }
        .page-notif-ic.info { background: rgba(59,130,246,.18); color: #3b82f6; }
        .page-notif-body {
            flex: 1;
            min-width: 0;
        }
        .page-notif-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 4px;
        }
        .page-notif-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }
        .page-notif-msg {
            font-size: 13px;
            color: var(--text-dim);
            line-height: 1.45;
            margin-bottom: 8px;
        }
        .page-notif-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11.5px;
            color: var(--text-faint);
            flex-wrap: wrap;
        }
        .page-notif-meta .tag {
            font-weight: 600;
            color: var(--text-dim);
            background: var(--panel-3);
            padding: 2px 8px;
            border-radius: 6px;
        }
        .action-btn-mark {
            font-size: 12px;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 8px;
            background: var(--panel-2);
            color: var(--text);
            border: 1px solid var(--border-soft);
            cursor: pointer;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .action-btn-mark:hover {
            background: var(--panel-3);
            border-color: var(--border);
        }
    </style>
    @endpush

    <div class="notif-page-wrap" x-data="{
        async markAll() {
            const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
            const res = await fetch('{{ route('notifications.mark-all-read') }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '' }
            });
            if (res.ok) {
                window.location.reload();
            }
        },
        async markRead(id, url) {
            const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
            await fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '' }
            });
            if (url && url !== '#' && url !== '') {
                window.location.href = url;
            } else {
                window.location.reload();
            }
        }
    }">
        <!-- Top Toolbar -->
        <div class="notif-page-head">
            <div>
                <h2 style="font-size:16px; font-weight:700; margin:0 0 2px;">Daftar Pemberitahuan</h2>
                <div style="font-size:12px; color:var(--text-dim);">
                    @if($unreadCount > 0)
                        Terdapat <strong style="color:var(--red);">{{ $unreadCount }}</strong> notifikasi belum dibaca.
                    @else
                        Semua notifikasi telah dibaca.
                    @endif
                </div>
            </div>
            @if($unreadCount > 0)
                <button type="button" class="action-btn-mark" @click="markAll()">
                    <svg style="width:14px; height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Tandai Semua Sudah Dibaca
                </button>
            @endif
        </div>

        <!-- Filter Bar -->
        <div class="notif-filter-bar">
            <div class="pill-group">
                <a href="{{ route('notifications.page', ['filter' => 'all', 'modul' => $activeModul]) }}" class="pill-btn {{ $activeFilter === 'all' ? 'active' : '' }}">
                    Semua
                </a>
                <a href="{{ route('notifications.page', ['filter' => 'unread', 'modul' => $activeModul]) }}" class="pill-btn {{ $activeFilter === 'unread' ? 'active' : '' }}">
                    Belum Dibaca ({{ $unreadCount }})
                </a>
            </div>

            <div class="pill-group">
                <a href="{{ route('notifications.page', ['filter' => $activeFilter, 'modul' => 'all']) }}" class="pill-btn {{ $activeModul === 'all' ? 'active' : '' }}">
                    Semua Modul
                </a>
                <a href="{{ route('notifications.page', ['filter' => $activeFilter, 'modul' => 'alat']) }}" class="pill-btn {{ $activeModul === 'alat' ? 'active' : '' }}">
                    Alat
                </a>
                <a href="{{ route('notifications.page', ['filter' => $activeFilter, 'modul' => 'jadwal']) }}" class="pill-btn {{ $activeModul === 'jadwal' ? 'active' : '' }}">
                    Jadwal
                </a>
                <a href="{{ route('notifications.page', ['filter' => $activeFilter, 'modul' => 'import_export']) }}" class="pill-btn {{ $activeModul === 'import_export' ? 'active' : '' }}">
                    Import / Export
                </a>
                <a href="{{ route('notifications.page', ['filter' => $activeFilter, 'modul' => 'user']) }}" class="pill-btn {{ $activeModul === 'user' ? 'active' : '' }}">
                    User
                </a>
            </div>
        </div>

        <!-- Notification List -->
        <div class="notif-list-card">
            @forelse($notifications as $notif)
                <div class="page-notif-item {{ !$notif->is_read ? 'unread' : '' }}" @click="markRead('{{ $notif->id }}', '{{ $notif->url }}')">
                    <div class="page-notif-ic {{ $notif->tipe }}">
                        @if($notif->icon === 'tool')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                        @elseif($notif->icon === 'alert-triangle')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        @elseif($notif->icon === 'calendar')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        @elseif($notif->icon === 'file-spreadsheet')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 13h8M8 17h8M10 9h4"/></svg>
                        @elseif($notif->icon === 'user-check')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                        @endif
                    </div>
                    <div class="page-notif-body">
                        <div class="page-notif-head">
                            <span class="page-notif-title">{{ $notif->judul }}</span>
                            @if(!$notif->is_read)
                                <span style="width:8px; height:8px; border-radius:50%; background:var(--red); display:inline-block;"></span>
                            @endif
                        </div>
                        <div class="page-notif-msg">{{ $notif->pesan }}</div>
                        <div class="page-notif-meta">
                            <span class="tag">{{ $notif->modul_label }}</span>
                            <span>&bull;</span>
                            <span>{{ $notif->waktu_display }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:48px 20px; text-align:center; color:var(--text-dim);">
                    <svg style="width:40px; height:40px; margin:0 auto 10px; color:var(--text-faint); display:block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
                    <div style="font-size:14px; font-weight:700; color:var(--text); margin-bottom:4px;">Tidak Ada Notifikasi</div>
                    <div style="font-size:12px;">Tidak ditemukan pemberitahuan pada filter yang dipilih.</div>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div style="margin-top:12px;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
