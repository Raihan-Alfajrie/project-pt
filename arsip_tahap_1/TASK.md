# TASK.md — Roadmap Pengerjaan Bertahap

> **ATURAN WAJIB:** Kerjakan **satu fase dalam satu waktu**, sesuai urutan di bawah.
> Setelah menyelesaikan satu fase, **berhenti**, laporkan ringkasan apa yang sudah
> dikerjakan, lalu **tunggu konfirmasi/izin eksplisit** dari user sebelum lanjut ke
> fase berikutnya. Jangan pernah mengerjakan dua fase sekaligus dalam satu sesi kerja
> tanpa diminta. Lihat `AGENTS.md` untuk aturan lengkap.

Setiap fase juga **dimulai dengan pertanyaan** ke user kalau ada hal yang ambigu atau
belum diputuskan (lihat catatan "❓ Hal yang perlu dikonfirmasi" di tiap fase) — jangan
berasumsi sendiri lalu jalan terus.

---

## Fase 0 — Setup Proyek
- Install Laravel 13 baru, konfigurasi `.env` untuk XAMPP (MySQL lokal).
- Install & konfigurasi Tailwind CSS + Alpine.js via Vite (Node.js).
- Import `pertamina_rig_dashboard.sql` sebagai baseline, lalu konversi jadi Laravel
  migration files (bukan pakai file SQL mentah sebagai sumber jalan aplikasi).
- Setup struktur folder dasar (routes, controllers, models, views) sesuai konvensi Laravel.
- ❓ **Konfirmasi dulu:** apakah migration dibuat manual mengikuti persis skema SQL, atau
  boleh agent sesuaikan sedikit untuk best-practice Eloquent (nama pivot table, timestamps, dst)?

**Definition of done:** `php artisan serve` jalan, koneksi DB berhasil, tabel-tabel
kosong (belum ada data) sudah ter-migrate sesuai skema.

---

## Fase 1 — Autentikasi (Breeze) + Halaman Publik
- Install Laravel Breeze (stack Blade).
- Buat halaman **Home** (`/`) dan **About** (`/about`) sesuai `desainku/Home...html`
  dan `desainku/About...html` — layout publik, bukan layout internal.
- Ganti mock Login/Register di prototipe Home dengan form Breeze yang sungguhan berfungsi.
- Setelah login sukses → redirect ke `/dashboard`.
- Jalankan seeder `users` + `user_permissions` dari data di file SQL.
- ❓ **Konfirmasi dulu:** apakah field Register cukup Nama/Email/Password (sesuai form
  mock di prototipe), atau perlu tambahan field lain?

**Definition of done:** User bisa register, login, logout. Halaman Home & About tampil
identik dengan prototipe (secara visual).

---

## Fase 2 — Layout Internal + Dashboard
- Buat `layouts/app.blade.php` (topbar + sidebar internal) sebagai layout dasar semua
  halaman setelah login — jadikan komponen reusable, jangan duplikasi per halaman.
- Implementasikan halaman **Dashboard** (`/dashboard`) sesuai `desainku/Dashboard...html`:
  KPI cards, panel realisasi, Gantt jadwal & progres (read-only di halaman ini).
- Data Dashboard diambil dari tabel `alat`, `jadwal`, `target_rk` (bukan hardcode).

**Definition of done:** Setelah login, user mendarat di Dashboard yang menampilkan
data asli dari database (bukan data dummy JS).

---

## Fase 3 — Modul Alat
- CRUD penuh sesuai `desainku/Alat...html`: tabel, search, filter kategori/status,
  modal tambah/edit/detail/hapus.
- Middleware hak akses: hanya user dengan `user_permissions.alat = true` (atau Administrator)
  yang bisa akses halaman ini / melakukan aksi tulis.
- Tombol "Lihat Lokasi" per rig (link ke halaman Lokasi, sesuai prototipe).

**Definition of done:** CRUD Alat berfungsi penuh terhadap database, hak akses teruji
(Pekerja tanpa izin tidak bisa akses/aksi).

---

## Fase 4 — Modul Lokasi
- CRUD sesuai `desainku/Lokasi...html`: peta SVG ilustratif + tabel kode lapangan.
- Reproduksi logika peta (proyeksi provinsi → koordinat SVG) dari JS di prototipe.
- Deep-link `?kode=XXX` dari Alat tetap berfungsi.
- ❓ **Konfirmasi dulu:** peta SVG di prototipe itu digambar manual (bukan library) —
  pastikan dulu ke user apakah tetap dipertahankan sebagai SVG statis, atau user mau
  ganti pendekatan (mis. pakai Leaflet) sebelum agent investasi waktu reimplementasi.

**Definition of done:** CRUD Lokasi berfungsi, peta menampilkan titik sesuai data DB.

---

## Fase 5 — Modul Jadwal
- CRUD sesuai `desainku/Jadwal...html`: 3 KPI status (klik untuk filter), Gantt interaktif,
  modal tambah/edit/detail/hapus per item.
- `kategori_id` mengambil pilihan dari tabel `dokumentasi_item` (bukan hardcode), sesuai
  catatan di `DESAIN.md` §5.
- Deep-link `?kode=` dari Lokasi tetap berfungsi (filter Gantt by lapangan).

**Definition of done:** CRUD Jadwal berfungsi penuh, Gantt render dari data DB.

---

## Fase 6 — Manajemen User & Hak Akses
- CRUD sesuai `desainku/Manajemen User...html`: 2 KPI card (Admin/User + online-offline),
  tabel user, modal tambah/edit/atur-hak-akses/detail/hapus.
- Halaman ini hanya bisa diakses Administrator (Pekerja tidak boleh, apa pun hak aksesnya).
- Toggle hak akses menulis ke tabel `user_permissions`.

**Definition of done:** Administrator bisa kelola user & hak akses lewat UI, Pekerja
tidak bisa mengakses halaman ini sama sekali.

---

## Fase 7 — Audit Log
- Tampilkan tabel `audit_log` sesuai `desainku/Audit Log...html` (search + filter aksi/modul/jabatan).
- **Untuk versi awal: read-only**, tidak perlu live-wiring otomatis dari modul lain
  (sesuai PRD §7 Out of Scope) — KECUALI user secara eksplisit meminta fitur itu
  ditambahkan di fase ini.
- ❓ **Konfirmasi dulu:** apakah tetap read-only dulu, atau user sekarang mau sekalian
  live-wiring (agent perlu tahu ini SEBELUM mulai, karena mengubah pendekatan banyak
  controller lain).

**Definition of done:** Halaman Audit Log tampil dari data DB (seed atau live, sesuai
keputusan user), search/filter berfungsi.

---

## Fase 8 — Dokumentasi (Legend CRUD)
- CRUD sesuai `desainku/Dokumentasi...html`: CRUD panel (level atas) + CRUD item per
  panel (label, gaya tampilan, warna, color picker bebas).
- Data kategori di modul Jadwal (Fase 5) bergantung pada tabel ini — pastikan urutan
  ini tidak merusak referensi FK yang sudah dipakai di Jadwal.

**Definition of done:** CRUD Dokumentasi berfungsi, perubahan di sini konsisten
tercermin di dropdown kategori Jadwal.

---

## Fase 9 — FAQ & Profil
- Halaman **FAQ**: statis "Coming Soon", layout internal (bukan publik).
- Halaman **Profil**: data akun sendiri (nama, email, jabatan, status), tombol Edit
  (nama & email saja, sesuai prototipe — foto masih avatar inisial), panel Sesi Aktif
  (boleh read-only dulu), panel Perangkat & Aplikasi Terhubung (kosong, sesuai desain).

**Definition of done:** Kedua halaman tampil & berfungsi sesuai prototipe.

---

## Fase 10 — Polish & Handoff Akhir
- Review responsif di breakpoint sesuai CSS prototipe.
- Cek semua link antar-menu (sidebar, topbar, deep-link) berfungsi dengan route Laravel
  yang benar (bukan lagi link ke file HTML statis atau artifact URL).
- Tulis singkat `README.md` proyek: cara install, migrate, seed, run.
- Laporkan ke user: fitur apa saja yang sudah selesai, apa yang masih pending/dikompromikan,
  dan rekomendasi langkah lanjutan (mis. live audit log, upload foto asli).

**Definition of done:** Aplikasi bisa di-clone, `composer install`, `npm install`,
`php artisan migrate --seed`, `npm run dev`, `php artisan serve` → langsung jalan.
