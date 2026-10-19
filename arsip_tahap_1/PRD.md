# PRD — Pertamina Admin Panel (Rig Monitoring WK Zona 4)

> Dokumen ini adalah bagian dari paket hand-off untuk AI coding agent.
> Baca bersama `DESAIN.md`, `TASK.md`, dan `AGENTS.md` sebelum mulai bekerja.

## 1. Latar Belakang

Pertamina WK Zona 4 mengelola 16 rig pengeboran dan puluhan alat pendukung yang tersebar di beberapa lapangan (Sumatera Selatan). Saat ini pemantauan jadwal, status alat, dan lokasi dikerjakan lewat spreadsheet Excel manual (chart Gantt, tabel status). Proyek ini mengubahnya jadi **web admin dashboard internal** berbasis Laravel.

Desain UI/UX dan struktur database untuk aplikasi ini **sudah dirancang penuh** dalam bentuk prototipe HTML statis dan file SQL — tugas agent adalah mengimplementasikannya jadi aplikasi Laravel yang berfungsi, **bukan mendesain ulang dari nol**.

## 2. Tujuan Produk

- Menggantikan pemantauan manual berbasis Excel dengan dashboard web yang bisa diakses tim operasional.
- Menyediakan satu sumber kebenaran (single source of truth) untuk data alat, lokasi, dan jadwal pemboran.
- Mendukung multi-user dengan hak akses berjenjang (Administrator vs Pekerja).
- Mencatat riwayat aktivitas (audit log) untuk akuntabilitas perubahan data.

## 3. Target Pengguna

| Peran | Deskripsi | Akses |
|---|---|---|
| **Administrator** | Mengelola seluruh data & user | Semua modul (Alat, Lokasi, Jadwal) + Manajemen User |
| **Pekerja** | Staf operasional lapangan | Modul tertentu sesuai hak akses yang diberikan admin (toggle per modul: Alat/Lokasi/Jadwal) |

## 4. Lingkup Produk (Scope)

Aplikasi terdiri dari **11 halaman/menu**, sudah punya prototipe HTML lengkap di folder `desainku/`:

| # | Menu | Fungsi Utama |
|---|---|---|
| 1 | **Home** | Landing page publik (sebelum login), hero + fitur + CTA login/register |
| 2 | **About** | Halaman publik, "Coming Soon" |
| 3 | **Dashboard** | Ringkasan status rig (KPI), Gantt jadwal & progres, realisasi tajak/completed |
| 4 | **Alat** | CRUD data rig & alat pendukung, status, riwayat, foto (placeholder ikon dulu) |
| 5 | **Lokasi** | CRUD kode lapangan, peta ilustratif, detail sumur & rig per lokasi |
| 6 | **Jadwal** | CRUD item pekerjaan sumur (Gantt), kategori fase kerja, status Selesai/Progres/Kendala |
| 7 | **Manajemen User** | CRUD akun user, atur hak akses per modul, status online/offline |
| 8 | **Audit Log** | Tabel riwayat aktivitas (read-only untuk versi awal) |
| 9 | **Dokumentasi** | CRUD panel & item legend (warna/pola yang dipakai di seluruh dashboard) |
| 10 | **FAQ** | "Coming Soon" |
| 11 | **Profil** | Data akun sendiri, edit nama/email, sesi aktif (read-only) |

Detail lengkap tiap halaman (layout, komponen, interaksi) ada di `DESAIN.md`.

## 5. Requirement Fungsional Ringkas

- **Autentikasi**: Login & Register sungguhan (Laravel Breeze), menggantikan mock di prototipe Home. Setelah login, user masuk ke Dashboard.
- **Otorisasi**: Middleware berbasis hak akses per modul (tabel `user_permissions`) — Pekerja hanya bisa akses modul yang diizinkan; Administrator selalu penuh.
- **CRUD**: Alat, Lokasi, Jadwal, Manajemen User, Dokumentasi (panel+item) — semua dengan validasi, konfirmasi hapus, dan pesan sukses/gagal.
- **Audit Log**: minimal read-only dulu (data bisa tetap seed/manual). Live-wiring otomatis (tiap CRUD di modul lain otomatis mencatat log) adalah **fitur lanjutan, TIDAK dikerjakan kecuali diminta eksplisit** — lihat `AGENTS.md` soal larangan menambah fitur di luar desain.
- **Relasi data**: mengikuti `pertamina_rig_dashboard.sql` — jadwal terhubung ke alat (rig), lokasi (lapangan), dan dokumentasi_item (kategori/warna).

## 6. Requirement Non-Fungsional

- **Stack**: Laravel 13, Blade + Alpine.js + Tailwind CSS, MySQL (XAMPP), Node.js (build asset via Vite).
- **Environment**: pengembangan lokal di XAMPP (Apache + MySQL), database `pertamina_rig_dashboard`.
- **Desain visual**: mengikuti persis palet warna & komponen di `DESAIN.md` — merah `#EA2027`, panel maroon gelap, dukung dark/light lewat `prefers-color-scheme` seperti di prototipe.
- **Responsif**: mengikuti breakpoint yang sudah ada di CSS prototipe (sidebar collapse di bawah 980px, dsb).
- **Tidak perlu** real-time/websocket, tidak perlu mobile app — cukup web responsive.

## 7. Di Luar Lingkup (Out of Scope) untuk Versi Awal

- Upload foto asli (alat, profil) — UI masih pakai placeholder ikon/inisial, sesuai prototipe.
- Audit log live-wired otomatis dari semua modul.
- Hak akses granular per aksi (Tambah/Edit/Hapus terpisah) — cukup toggle per modul (on/off) sesuai desain.
- Halaman About & FAQ selain "Coming Soon".
- Peta Lokasi dengan GPS/tile server sungguhan (tetap peta ilustratif SVG seperti prototipe).

## 8. Kriteria Sukses

- Semua 11 halaman berfungsi sesuai prototipe HTML (tampilan & interaksi utama tidak berubah).
- Data tersimpan permanen di MySQL, bukan localStorage seperti prototipe.
- Login/Register berjalan dengan Laravel Breeze.
- Hak akses per modul berfungsi (Pekerja tanpa akses tidak bisa CRUD modul terkait).
- Tidak ada fitur yang menyimpang dari desain tanpa persetujuan eksplisit dari pemilik produk.
