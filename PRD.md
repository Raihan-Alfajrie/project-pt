# PRD — Pertamina Admin Panel (Rig Monitoring WK Zona 4)
## Versi 2 — "Tahapan Pengembangan Lanjutan"

> Dokumen ini menggantikan PRD tahap 1 (arsip ada di `arsip_tahap_1/`).
> Baca bersama `AGENTS.md`, `DESAIN.md`, `TASK.md`, dan `README.md` (catatan progres tahap 1).

---

## 1. Latar Belakang & Status Saat Ini

Pertamina WK Zona 4 mengelola 16 rig pengeboran dan puluhan alat pendukung di beberapa lapangan.
Pemantauan yang semula manual (spreadsheet Excel) sudah digantikan **web admin dashboard internal berbasis Laravel 13**.

**Tahap 1 sudah selesai** (sumber: `README.md` hasil kerja AI agent sebelumnya): seluruh 11 menu
(Home, About, Dashboard, Alat, Lokasi, Jadwal, Manajemen User, Audit Log, Dokumentasi, FAQ, Profil),
autentikasi Laravel Breeze, RBAC Administrator/Pekerja, dan 82 feature test yang lulus.

**Tahap ini ("Tahapan Pengembangan Lanjutan")** menambahkan satu menu baru dan merapikan kesesuaian
antara desain dan hasil implementasi:

1. **Menu baru "Import / Export"** — memindahkan data dari/ke file Excel, menggantikan kebiasaan
   mengedit spreadsheet manual (sumber data awal proyek ini memang berasal dari file Excel RK Bor).
2. **Rekonsiliasi desain vs implementasi** — `README.md` tahap 1 menunjukkan beberapa perbedaan dari
   desain/PRD awal (lihat §9). Perbedaan itu **tidak boleh diubah sepihak**; dicatat dulu dan diputuskan pemilik produk.

---

## 2. Tujuan Tahap Lanjutan

- Admin dapat **mengunduh** data Alat, Lokasi, Jadwal, dan Audit Log sebagai `.xlsx` / `.csv`.
- Admin dapat **mengunggah** file Excel untuk menambah/memperbarui data Alat, Lokasi, dan Jadwal secara massal,
  dengan validasi per baris, pratinjau sebelum disimpan, dan laporan error.
- Setiap aktivitas import/export tercatat di **riwayat**.
- Tidak merusak fitur, data, dan 82 test yang sudah ada.

---

## 3. Pengguna & Hak Akses

| Peran | Akses ke menu Import / Export |
|---|---|
| **Administrator** | Penuh (export, import, riwayat) |
| **Pekerja** | **Tidak ada akses** — menu tidak tampil di sidebar dan route menolak (sama polanya dengan Manajemen User) |

---

## 4. Lingkup Tahap Lanjutan

### 4.1 Menu #12 — Import / Export
- Letak di sidebar: grup **Manajemen Data**, tepat **setelah Jadwal**. Route yang disarankan: `/import-export`.
- Prototipe final: file HTML `Import / Export — Pertamina Admin Panel.html` di folder `desainku/`
  (prototipe ini berfungsi sungguhan membaca & menulis .xlsx di browser — gunakan sebagai spesifikasi perilaku).
- Satu halaman, **tiga panel bertumpuk**: (1) Export Data, (2) Import Data, (3) Riwayat Import / Export.

### 4.2 Modul yang didukung

| Modul | Export | Import | Kunci pencocokan (untuk mode "perbarui") |
|---|---|---|---|
| Alat | ✅ | ✅ | No. Seri |
| Lokasi | ✅ | ✅ | Kode Lapangan |
| Jadwal | ✅ | ✅ | Rig + Nama Sumur / Pekerjaan |
| Audit Log | ✅ | ❌ (export saja) | — |
| Manajemen User, Dokumentasi, FAQ, Profil | ❌ | ❌ | di luar lingkup |

---

## 5. Requirement Fungsional — Import / Export

### 5.1 Export (Panel 1)
- **FR-EX-01** Admin memilih satu modul dari kartu: Alat, Lokasi, Jadwal, Audit Log (kartu menampilkan jumlah baris).
- **FR-EX-02** Filter menyesuaikan modul: Alat (Kategori, Status) · Lokasi (Provinsi) · Jadwal (Status, Kategori) · Audit Log (Aksi, Modul).
- **FR-EX-03** Pilihan format: `.xlsx` atau `.csv`.
- **FR-EX-04** Teks hitungan "N baris akan diekspor dari <Modul>" selalu mengikuti filter; tombol **Unduh** nonaktif jika N = 0.
- **FR-EX-05** Nama file: `<modul>_<YYYY-MM-DD>.<ext>` (`alat_`, `lokasi_`, `jadwal_`, `audit_log_`).
- **FR-EX-06** CSV berenkode UTF-8 dengan BOM (agar terbaca benar di Excel). XLSX: lebar kolom menyesuaikan isi, nama sheet = nama modul.
- **FR-EX-07** Judul kolom export **identik** dengan judul kolom template import (supaya hasil export bisa diimpor kembali — "round-trip").
- **FR-EX-08** Setiap export sukses menambah satu entri di Riwayat.
- **FR-EX-09 (keamanan)** Sel teks yang diawali `=`, `+`, `-`, `@` (potensi formula injection) harus dinetralkan saat export.

### 5.2 Import (Panel 2) — alur 3 langkah
- **FR-IM-01 Langkah 1** Pilih modul (Alat / Lokasi / Jadwal); tampil daftar kolom (wajib bertanda `*`); tombol **Unduh template Excel**.
- **FR-IM-02** Template berisi sheet `Template` (hanya baris judul kolom) dan sheet `Petunjuk` (Kolom | Wajib | Keterangan).
- **FR-IM-03 Langkah 2** Unggah file lewat drag & drop atau pilih file. Format: `.xlsx`, `.csv` (prototipe juga menerima `.xls` — lihat Hal Terbuka §10).
- **FR-IM-04** Pencocokan judul kolom **tidak peka huruf besar/kecil dan mengabaikan tanda baca/spasi**, urutan kolom bebas, serta mendukung alias (tabel §6).
- **FR-IM-05** Jika kolom wajib tidak ditemukan → seluruh file ditolak dengan pesan yang menyebut kolom yang kurang. File kosong → pesan khusus.
- **FR-IM-06 Langkah 3 (pratinjau)** Setiap baris diklasifikasikan: **Baru**, **Perbarui**, **Dilewati**, atau **Error**, disertai nomor baris Excel (baris judul = 1, data mulai 2) dan pesan.
- **FR-IM-07** Ringkasan: Total / Baru / Perbarui / Dilewati / Error.
- **FR-IM-08** Dua mode untuk data yang sudah ada: (a) *Tambah baru saja, lewati yang sudah ada*; (b) *Tambah baru & perbarui yang sudah ada*. Default (a). Mengganti mode memperbarui klasifikasi pratinjau.
- **FR-IM-09** Duplikat di dalam file yang sama (kunci sama) → baris kedua dan seterusnya **Error**: "Duplikat di dalam file (sama dengan baris N)".
- **FR-IM-10** Pratinjau menampilkan maksimal 500 baris pertama (dengan catatan), tetapi **seluruh baris tetap diproses** saat konfirmasi.
- **FR-IM-11** Tombol **Konfirmasi Import (N baris)** hanya aktif jika ada baris Baru/Perbarui. Baris Error **tidak** diimpor (impor parsial) dan baris Dilewati tidak diubah.
- **FR-IM-12** Tombol **Unduh laporan error** (aktif jika ada error): `.xlsx` sheet `Error` berisi kolom "Baris Excel", "Pesan Error", lalu seluruh kolom asli baris tersebut.
- **FR-IM-13** Setelah konfirmasi: pesan sukses "Import selesai: X ditambahkan, Y diperbarui, Z baris error dilewati." dan form kembali ke kondisi awal.
- **FR-IM-14 (keamanan)** File dibaca sebagai **data**; rumus/makro tidak pernah dieksekusi (ambil nilai hasil saja).
- **FR-IM-15 (integritas)** Penyimpanan ke database memakai transaksi; jika terjadi kegagalan di tengah proses, seluruh perubahan batch dibatalkan dan status riwayat = **Gagal**.

### 5.3 Riwayat (Panel 3)
- **FR-RW-01** Tabel: Waktu · User · Jenis (Import/Export) · Modul · Nama File · Baris · Status.
- **FR-RW-02** Kolom Baris: `jumlah_berhasil / total` jika berbeda, selain itu satu angka.
- **FR-RW-03** Status: **Sukses** (tanpa error), **Sebagian** (ada baris error yang dilewati), **Gagal** (proses dibatalkan karena error sistem).
- **FR-RW-04** Urutan terbaru di atas. Prototipe menampilkan paling banyak 50 entri terakhir (apakah perlu paginasi → Hal Terbuka §10).

---

## 6. Spesifikasi Kolom & Validasi per Modul

Judul kolom di bawah adalah **judul resmi** (dipakai di template & export). "Alias" = judul lain yang tetap diterima saat import.

### Alat — kunci: `No. Seri` (tidak peka huruf besar/kecil)
| Kolom | Wajib | Aturan | Alias |
|---|---|---|---|
| Nama | ✅ | teks | Nama Alat |
| Kategori | ✅ | `Rig` atau `Alat Pendukung` | — |
| Merek | ✅ | teks | — |
| Model / Tipe | — | teks | Model, Tipe |
| Tahun Perolehan | — | bilangan bulat 1980–2026; kosong diperbolehkan | Tahun |
| No. Seri | ✅ | unik | Nomor Seri |
| Status | — | `Aktif` / `Tidak Aktif` / `Bermasalah`; kosong = `Aktif`; alias nilai `Standby` = `Tidak Aktif` | — |
| Keterangan | — | teks | Ket |

### Lokasi — kunci: `Kode Lapangan` (disimpan huruf besar)
| Kolom | Wajib | Aturan | Alias |
|---|---|---|---|
| Kode Lapangan | ✅ | unik | Kode |
| Nama Tempat | ✅ | teks | Nama |
| Provinsi | ✅ | harus salah satu dari 34 provinsi (tidak peka huruf besar/kecil → disimpan dengan penulisan resmi) | — |
| Kota / Kabupaten | — | teks | Kota, Kabupaten |
| Kecamatan | — | teks | — |
| Keterangan | — | teks | Ket |

### Jadwal — kunci: `Rig` + `Nama Sumur / Pekerjaan` (tidak peka huruf besar/kecil)
| Kolom | Wajib | Aturan | Alias |
|---|---|---|---|
| Rig | ✅ | harus sama dengan nama alat berkategori **Rig** di data Alat | Nama Rig |
| Nama Sumur / Pekerjaan | ✅ | teks | Nama Sumur, Sumur, Pekerjaan, Label |
| Kategori | ✅ | harus cocok dengan salah satu item legenda "Kesiapan Operasional" di menu Dokumentasi | — |
| Status | — | `Selesai` / `Menunggu` / `Progres` / `Kendala` (Menunggu = Progres = "Menunggu / Progres"); kosong = `Menunggu / Progres` | — |
| Bulan Mulai | ✅ | `2026-06` (disarankan) atau `Jun-26`; nama bulan Indonesia diterima (Mei, Agu, Okt, Des); sel bertipe tanggal Excel diterima; harus dalam rentang jendela Gantt | Bulan, Mulai |
| Durasi (bulan) | — | bilangan bulat ≥ 1; kosong = 1; bulan mulai + durasi tidak boleh melewati akhir jendela Gantt | Durasi |
| Catatan | — | teks | Keterangan |

> **Peringatan rentang bulan:** prototipe memakai jendela **Jun-26 s/d Des-27**, sedangkan `README.md` tahap 1
> menyebut Gantt aplikasi memakai **Jan 2026 – Jul 2027**. Jendela yang dipakai validasi import **harus sama dengan jendela Gantt aplikasi** → tanyakan ke user (§10).

### Audit Log — hanya export
Kolom: Username · Nama · Gmail · Jabatan · Tanggal / Waktu · Aksi · Modul · Keterangan.

---

## 7. Requirement Non-Fungsional

- **Stack tetap:** Laravel 13, Blade + Alpine.js + Tailwind CSS, MySQL (XAMPP), Node.js/Vite, Laravel Breeze.
- **Library Excel:** butuh library baru. Agent wajib **mengusulkan** pilihan (mis. Laravel Excel atau PhpSpreadsheet langsung), memeriksa kompatibilitas dengan Laravel 13 dan versi PHP proyek serta ekstensi PHP yang dibutuhkan di XAMPP, lalu **menunggu persetujuan** sebelum `composer require`.
- **Keamanan unggahan:** validasi sisi server (ekstensi, MIME, ukuran maksimum, jumlah baris maksimum — nilai batas ditanyakan ke user), file sementara tidak boleh dapat diakses publik dan harus dibersihkan, otorisasi dan CSRF mengikuti pola yang sudah ada.
- **Performa:** file besar dibaca bertahap (chunk) agar tidak kehabisan memori di XAMPP; batas realistis ditentukan bersama user.
- **Bahasa antarmuka:** Indonesia, teks pesan mengikuti prototipe (lihat `DESAIN.md` §7).
- **Pengujian:** setiap fase menambah feature test; `php artisan test` harus tetap lulus penuh (baseline 82 test).
- **Tidak merusak data:** tidak ada perintah yang menghapus/mereset data user tanpa izin eksplisit (lihat `AGENTS.md`).

---

## 8. Perubahan Data yang Diperlukan (USULAN — butuh persetujuan sebelum dibuat)

- Tabel baru untuk riwayat, misalnya `import_export_logs`: `id`, `user_id` (FK, null jika user dihapus), `jenis` (import/export), `modul`, `nama_file`, `baris_berhasil`, `baris_total`, `status` (sukses/sebagian/gagal), `created_at`. Nama & kolom final **ditentukan bersama user**.
- Jika aktivitas import/export juga harus tercatat di **Audit Log** (yang sekarang sudah live), kemungkinan perlu nilai modul/aksi baru → **keputusan user** (§10). Jangan menambah nilai ENUM/migrasi sebelum diputuskan.
- **Migrasi yang sudah pernah dijalankan tidak boleh diedit**; semua perubahan lewat migrasi baru.

---

## 9. Perbedaan yang Terlihat antara README Tahap 1 dan Desain/PRD Awal

Ini **kandidat** yang harus diverifikasi agent di Fase L0 (dengan membaca kode, bukan menebak) dan diputuskan user satu per satu.
Bisa jadi sebagian sudah disetujui user di sesi sebelumnya — **jangan dihapus atau diubah sebelum ada keputusan**.

| # | Topik | Desain / PRD awal | Menurut README tahap 1 |
|---|---|---|---|
| 1 | Peta Lokasi | SVG skematik buatan sendiri | Leaflet.js, pin berdenyut menurut "status lapangan" |
| 2 | Foto Alat & Profil | Di luar lingkup (placeholder ikon/inisial) | Upload foto sungguhan (≤ 2 MB) |
| 3 | Audit Log | Read-only dulu (data seed) | Live otomatis dari semua modul |
| 4 | FAQ | "Coming Soon" | Dinamis + CRUD (tabel `faqs`, 11 seed, 4 kategori) |
| 5 | Jumlah tabel | 11 | 12 (`faqs` bertambah) |
| 6 | KPI Dashboard | Total Alat / Aktif / Tidak Aktif / Bermasalah + progres | Total Rig / Rig Aktif / Tajak 2026 / Completed 2026 |
| 7 | KPI Alat | Total / Aktif / Tidak Aktif / Bermasalah | Total Alat / Operasi / Siap Operasi / Perbaikan (+ filter merek) |
| 8 | KPI Jadwal | Selesai / Menunggu-Progres / Kendala | Total Jadwal / Operasi Berjalan / Selesai |
| 9 | Jendela Gantt | Jun-26 s/d Des-27 (19 bulan) | Jan 2026 – Jul 2027 (19 bulan) + garis "Hari Ini" |
| 10 | Hak akses | Toggle per modul (alat/lokasi/jadwal) | Disebut "Granular Permission" — cek apakah masih per modul |
| 11 | Dokumentasi | Tidak menyebut pembatasan peran | "Eksklusif Administrator" |
| 12 | Nilai modul Audit Log | alat/lokasi/jadwal/user/sistem | ditambah Dokumentasi, FAQ |
| 13 | Daftar user default di README | 10 user | hanya 6 yang dicantumkan (kemungkinan sekadar ringkasan) |

---

## 10. Hal Terbuka (keputusan pemilik produk — agent WAJIB bertanya)

1. Library Excel mana yang dipakai, dan versi PHP/ekstensi XAMPP yang tersedia?
2. Skema tabel riwayat (nama & kolom) disetujui?
3. Apakah import/export juga dicatat di Audit Log? Jika ya, satu entri ringkasan per operasi atau per baris?
4. Jendela bulan untuk validasi Jadwal: Jun-26–Des-27 (prototipe) atau Jan-26–Jul-27 (aplikasi sekarang), atau diturunkan dinamis dari pengaturan Gantt?
5. Nilai Status Alat & Kategori Jadwal yang valid mengikuti **aplikasi sekarang** (README menyebut istilah "Operasi / Siap Operasi / Perbaikan") atau mengikuti prototipe?
6. Apakah Lokasi memiliki kolom tambahan di aplikasi (mis. koordinat/status untuk Leaflet) yang ikut diimpor/diekspor?
7. Batas ukuran file dan jumlah baris maksimum per import.
8. Apakah `.xls` (format lama) tetap diterima, atau cukup `.xlsx` dan `.csv`?
9. Alur teknis konfirmasi: file disimpan sementara di server lalu dikonfirmasi lewat token, atau diunggah ulang saat konfirmasi?
10. Apakah Riwayat perlu paginasi / filter (tidak ada di prototipe)?
11. Hasil rekonsiliasi §9 (butir 1–13): pertahankan / kembalikan ke desain / perbarui dokumen.

---

## 11. Di Luar Lingkup Tahap Lanjutan

- Import untuk Manajemen User, Dokumentasi, FAQ, Profil; import foto.
- Import Audit Log.
- Penjadwalan otomatis / queue background / progress bar real-time (kecuali user meminta).
- Sinkronisasi langsung dengan layanan eksternal.
- Tombol "Reset data contoh" pada prototipe — itu **khusus prototipe**, jangan dibawa ke aplikasi.

---

## 12. Kriteria Sukses

- Menu Import / Export tampil dan berperilaku sesuai prototipe, hanya untuk Administrator.
- Export 4 modul (xlsx & csv) dengan filter berfungsi; hasil export bisa diimpor kembali tanpa error.
- Import 3 modul lengkap: template, validasi per baris, pratinjau, mode skip/update, laporan error, transaksi, riwayat.
- Tidak ada fitur di luar prototipe yang ditambahkan tanpa izin; keputusan §10 terdokumentasi.
- Seluruh test lama tetap lulus, ditambah test baru untuk fitur Import / Export.
- `README.md` diperbarui (fitur, library, cara pakai, jumlah test).
