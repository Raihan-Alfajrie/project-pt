# Pertamina Admin Panel — Rig Monitoring WK Zona 4

Platform pemantauan rig pemboran dan kerja ulang (workover) internal untuk wilayah kerja Pertamina Hulu Rokan (PHR) Zona 4. Sistem ini memantau status alat/rig, lokasi lapangan kerja, jadwal operasional sumur, manajemen hak akses pengguna, log audit, dokumentasi legenda warna, serta modul komprehensif **Import & Export Data Excel/CSV**.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 13 (PHP 8.2+)
- **Frontend**: Blade Components + Alpine.js + Tailwind CSS (via Vite)
- **Spreadsheet Engine**: `phpoffice/phpspreadsheet` (v5.10.0) — Export XLSX, CSV (UTF-8 BOM), Import & Multi-sheet Template Parsing
- **Maps**: Leaflet.js (Peta Lapangan WK Zona 4)
- **Database**: MySQL 8.x / MariaDB (XAMPP compatible) & SQLite (in-memory test suite)
- **Autentikasi**: Laravel Breeze (Blade Stack) dengan Custom RBAC (Administrator vs Pekerja) & Granular Permission

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### 1. Prasyarat Sistem
- PHP >= 8.2 (ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `gd`, `intl`, `zip`)
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL / MariaDB via XAMPP

> **Catatan Ekstensi PHP**: Ekstensi `gd`, `intl`, dan `zip` wajib aktif pada instalasi PHP/XAMPP untuk kalkulasi ukuran kolom otomatis, dukungan unicode, dan kompresi berkas `.xlsx` oleh PhpSpreadsheet.

### 2. Clone Repository & Install Dependencies
```bash
# Masuk ke folder proyek
cd project-aku

# Install dependency backend
composer install

# Install dependency frontend
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env` (atau sesuaikan `.env` yang sudah ada):
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan pengaturan database di `.env` sudah sesuai dengan XAMPP MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pertamina_rig_dashboard
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan**: Buat database bernama `pertamina_rig_dashboard` terlebih dahulu di MySQL (misal via phpMyAdmin di `http://localhost/phpmyadmin`).

### 4. Migrasi & Seeding Database
Jalankan migrasi dan seeder untuk membuat 13 tabel lengkap dengan baseline data operasional:
```bash
php artisan migrate --seed
```

### 5. Kompilasi Asset & Menjalankan Server
```bash
# Mode Produksi (Build Asset)
npm run build

# Atau Mode Development (Hot Module Reload)
npm run dev

# Jalankan server aplikasi Laravel
php artisan serve
```

Aplikasi siap diakses melalui browser di: **`http://127.0.0.1:8000`**

---

## 👥 Kredensial Pengguna Default

Database telah diisi 10 user awal dengan kata sandi: `password`

| Nama | Email / Username | Jabatan / Role | Hak Akses Modul |
| :--- | :--- | :--- | :--- |
| **Budi Santoso** | `budi.santoso@gmail.com` (`budi.santoso`) | **Administrator** | Akses Penuh (Alat, Lokasi, Jadwal, User, Audit Log, Dokumentasi, Import/Export) |
| **Siti Rahayu** | `siti.rahayu@gmail.com` (`siti.rahayu`) | **Administrator** | Akses Penuh |
| **Hendra Gunawan** | `hendra.gunawan@gmail.com` (`hendra.gunawan`) | **Administrator** | Akses Penuh |
| **Agus Wijaya** | `agus.wijaya@gmail.com` (`agus.wijaya`) | **Pekerja** | Alat: ✅, Lokasi: ❌, Jadwal: ✅ (Import/Export: ❌) |
| **Dewi Lestari** | `dewi.lestari@gmail.com` (`dewi.lestari`) | **Pekerja** | Alat: ❌, Lokasi: ✅, Jadwal: ✅ (Import/Export: ❌) |
| **Fajar Ramadhan** | `fajar.ramadhan@gmail.com` (`fajar.ramadhan`) | **Pekerja** | Alat: ✅, Lokasi: ❌, Jadwal: ❌ (Import/Export: ❌) |

---

## 📌 Ringkasan Fitur & Modul

1. **Dashboard Utama (`/dashboard`)**:
   - 4 Kartu KPI: Total Rig, Rig Aktif, Tajak 2026, Completed 2026.
   - Diagram Gantt 19 Bulan (Jan 2026 – Jul 2027) dengan blok jadwal interaktif, kode warna dinamis, dan tooltip informatif.
   - Tabel Status Rig dengan pencarian live dan filter status.

2. **Modul Alat (`/alat`)**:
   - 4 Kartu KPI: Total Alat, Operasi, Siap Operasi, Perbaikan.
   - Pencarian live + filter kategori, merek, dan status.
   - CRUD Rig & Alat (Tambah, Edit, Hapus) dengan otorisasi berbasis permission.
   - **Upload Foto Alat**: Dukungan unggah foto fisik rig/alat (JPG, PNG, WEBP hingga 2MB) disimpan di storage publik dengan pratinjau interaktif di tabel dan modal detail.

3. **Modul Lokasi (`/lokasi`)**:
   - Peta Interaktif Leaflet dengan pin custom beranimasi denyut (*pulse*) sesuai status lapangan.
   - Filter live status dan pencarian kode lapangan.
   - Deep-linking (`?kode=...`) langsung membuka modal detail lapangan dengan daftar sumur dan rig yang beroperasi.
   - CRUD Lapangan.

4. **Modul Jadwal (`/jadwal`)**:
   - 3 Kartu KPI: Total Jadwal, Operasi Berjalan, Selesai.
   - Gantt Chart lengkap 19 bulan dengan penanda garis vertikal *Hari Ini*.
   - Filter lapangan deep-link dan CRUD jadwal operasi.

5. **Manajemen User & Hak Akses (`/users`)**:
   - Eksklusif untuk Administrator.
   - 2 Kartu KPI: Total User (Admin/Pekerja) dan Status Online/Offline.
   - CRUD Akun Pengguna + Pengaturan Hak Akses per modul (`alat`, `lokasi`, `jadwal`) via toggle switch.

6. **Live Audit Log (`/audit-log`)**:
   - Rekam jejak otomatis secara *real-time* atas mutasi operasional pengguna (Login, Logout, Tambah, Edit, Hapus, Export, Import) pada seluruh modul.
   - Mempertahankan 25 riwayat data historis awal.
   - Filter multi-kriteria: Aksi, Modul, dan Jabatan.

7. **Dokumentasi & Legend (`/dokumentasi`)**:
   - Manajemen legenda warna dan pola (Solid, Putus-putus, Border) yang dipakai pada Gantt Jadwal.
   - CRUD Panel dan CRUD Item per panel (eksklusif Administrator).

8. **Pusat Bantuan & FAQ Dinamis (`/faq`)**:
   - Manajemen tanya-jawab operasional berbasis database (`faqs` table) dengan 11 seed data awal komprehensif WK Zona 4.
   - Pengelompokan 4 kategori terstruktur: *Operasional Rig & Alat*, *Peta & Lapangan*, *Jadwal Operasi & Gantt*, dan *Akun & Hak Akses*.
   - Live Search instan dan Category Filter Pills dengan counter badge dinamis.
   - Accordion interaktif berbasis Alpine.js dengan transisi halus.
   - CRUD FAQ penuh untuk Administrator terintegrasi dengan Live Audit Log.
   - Hak akses Pekerja bersifat *Read-only*.
   - Kartu Bantuan Helpdesk Operasional (email support & hotline telepon 24/7 Pertamina WK Zona 4).

9. **Profil Pengguna (`/profil`)**:
   - Pengaturan data akun pribadi, sesi aktif, dan identitas pengguna.
   - **Upload Foto Profil**: Dukungan unggah foto profil asli pengguna (disimpan di disk storage publik) dengan pratinjau live serta fallback inisial otomatis.

10. **Modul Import & Export (`/import-export`)** *(Menu ke-12)*:
    - Eksklusif untuk **Administrator** (middleware proteksi admin).
    - **4 Kartu Modul KPI**: Hitungan data live untuk Alat, Lokasi, Jadwal, dan Audit Log dengan indikator badge kesiapan ekspor.
    - **Ekspor Data (Export)**:
      - Modul yang didukung: **Alat**, **Lokasi**, **Jadwal**, dan **Audit Log**.
      - Format berkas: **Excel (`.xlsx`)** dan **CSV (`.csv`)** dengan UTF-8 BOM untuk kompatibilitas penuh dengan Microsoft Excel.
      - **Formula Injection Neutralization**: Sel berawalan karakter `=`, `+`, `-`, atau `@` dinetralkan secara otomatis dengan prepend `'` demi keamanan saat dibuka di spreadsheet client.
    - **Impor Data (Import) — 3 Langkah Terstruktur**:
      - **Langkah 1 (Unduh Template)**: Pembuatan template Excel 2-sheet (`Template` untuk format input baris & `Petunjuk` berisi spesifikasi kolom, aturan enum, format tanggal, dan contoh baris) untuk modul Alat, Lokasi, dan Jadwal.
      - **Langkah 2 (Unggah Berkas)**: Validasi ketat di server (ekstensi `.xlsx`/`.csv`, MIME valid, batas ukuran maks 5 MB, batas data maks 1.000 baris, parser aman tanpa eksekusi formula/makro).
      - **Langkah 3 (Pratinjau & Konfirmasi)**:
        - Tampilan ringkasan status baris via chip berwarna: **Total**, **Baru**, **Perbarui**, **Dilewati**, dan **Error**.
        - Radio toggle mode penanganan: **Lewati yang duplikat (Skip)** vs **Perbarui yang sudah ada (Update)** — mengganti mode langsung mengkalkulasi ulang status baris secara instan tanpa perlu unggah ulang file.
        - Tabel pratinjau interaktif (maksimal 500 baris).
        - **Unduh Laporan Error (`.xlsx`)**: Tombol otomatis muncul bila ada baris tidak valid, menghasilkan berkas Excel berisi baris bermasalah beserta kolom alasan error detail.
    - **Eksekusi Atomik & Normalisasi Data**:
      - Seluruh batch import dieksekusi dalam transaksi atomik `DB::transaction()` untuk menjamin konsistensi data.
      - *Alat*: Insert / Update dengan normalisasi enum status (`aktif`, `standby`, `bermasalah`) dan kategori (`rig`, `alat_pendukung`). Kolom opsional kosong mengosongkan nilai lama (Opsi A disetujui).
      - *Lokasi*: Penyeragaman kode lapangan ke huruf besar (`strtoupper`) dan otomatisasi pengisian tanggal pemetaan.
      - *Jadwal*: Pemetaan nama rig ke foreign key `alat_id`, mapping kategori dokumentasi ke `dokumentasi_item`, normalisasi enum status operasional (`selesai`, `progres`, `kendala`), serta konversi format periode bulan (`Mmm-YY` / `YYYY-MM` -> `YYYY-MM-01`).
    - **Panel Riwayat Aktivitas**:
      - Menampilkan 50 riwayat aktivitas import dan export terbaru dari tabel `import_export_logs`.
      - Badge status visual: **Sukses** (hijau), **Sebagian** (kuning), dan **Gagal** (merah).
      - Format waktu Bahasa Indonesia terstandarisasi (`d M Y, H:i`).
    - **UX, Keamanan & Aksesibilitas**:
      - Komponen Toast mengambang responsif (auto-hide 3.6 detik).
      - Proteksi klik ganda (*double-submit prevention*) pada seluruh tombol aksi.
      - Navigasi keyboard penuh (`tabindex="0"`, outline fokus merah, handler Enter/Space).

---

## 🗄️ Struktur Tabel Riwayat (`import_export_logs`)

Tabel `import_export_logs` mencatat seluruh mutasi ekspor dan impor data:

| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | `bigint unsigned (PK)` | Identifikator unik log |
| `user_id` | `bigint unsigned (FK)` | Relasi ke pengguna (`users.id`) yang melakukan operasi |
| `jenis` | `enum('import', 'export')` | Jenis operasi data |
| `modul` | `varchar(50)` | Modul target (`alat`, `lokasi`, `jadwal`, `audit_log`) |
| `format` | `enum('xlsx', 'csv')` | Format file yang diekspor/diimpor |
| `status` | `enum('sukses', 'sebagian', 'gagal')` | Status keberhasilan pemrosesan |
| `nama_file` | `varchar(255)` | Nama berkas yang diunduh atau diunggah |
| `baris_berhasil`| `int unsigned` | Jumlah baris data yang berhasil diproses/diekspor |
| `baris_total` | `int unsigned` | Total baris data dalam berkas |
| `keterangan` | `text (nullable)` | Catatan detail status atau alasan kegagalan |
| `created_at` | `timestamp` | Waktu pencatatan log |
| `updated_at` | `timestamp` | Waktu pembaharuan data |

---

## ⚙️ Batasan & Ketentuan Operasional

| Parameter | Batasan | Catatan |
| :--- | :--- | :--- |
| **Format Berkas** | `.xlsx`, `.csv` | CSV diekspor dengan UTF-8 BOM untuk kompatibilitas Excel |
| **Ukuran Berkas Unggahan** | Maksimal 5 MB | Divalidasi di sisi server (HTTP 422 bila melebihi batas) |
| **Maksimal Baris Impor** | 1.000 baris | Perlindungan beban memori server |
| **Maksimal Pratinjau UI** | 500 baris | Menjaga performa perenderan DOM di browser |
| **Masa Aktif Token Pratinjau**| 2 Jam | Disimpan dalam cache sistem (`impex_preview_{token}`) |
| **Batas Riwayat Tampilan** | 50 entri terbaru | Menampilkan data terbaru secara terurut menurun |
| **Formula Protection** | Prefix `'` pada `=, +, -, @` | Mencegah eksekusi spreadsheet formula injection |

---

## 🤝 Keputusan Desain & Rekonsiliasi (PRD §10)

1. **Cakupan Modul Impor**: Hanya modul **Alat**, **Lokasi**, dan **Jadwal**. Modul **Audit Log** bersifat *Read-only* (hanya mendukung Ekspor).
2. **Format Berkas**: Mendukung penuh XLSX dan CSV dengan UTF-8 BOM.
3. **Kebijakan Nilai Kosong pada Mode Update**: **Opsi A Disetujui** — nilai kosong pada kolom opsional di file Excel akan menimpa/mengosongkan data lama di database.
4. **Format Tanggal Jadwal Operasi**: Fleksibel mendukung format `Mmm-YY` (misal: `Jun-26`, `Jul-26`) serta `YYYY-MM` dalam rentang waktu yang didukung (Juni 2026 s/d Desember 2027).
5. **Keamanan Formula**: Netralisasi otomatis karakter formula injection saat ekspor data.
6. **Driver Database Pengujian**: Menggunakan SQLite in-memory untuk kecepatan dan isolasi test suite, serta MySQL untuk lingkungan produksi XAMPP.

---

## 🔮 Rekomendasi Pengembangan Lanjutan (Future Work)

Berikut adalah saran perbaikan di masa depan (tidak mengikat tahap ini):
1. **Background Queue / Job Workers**: Jika kebutuhan dataset meningkat hingga lebih dari 5.000 baris, implementasikan pemrosesan asynchronous via Laravel Queue (`database` / `redis`) disertai progress bar real-time (SSE atau WebSockets).
2. **Automated Scheduled Export**: Konfigurasi `php artisan schedule:run` untuk menghasilkan cadangan data berkala (misal ekspor otomatis Audit Log mingguan ke storage terproteksi).
3. **Template & Impor Modul Tambahan**: Menyediakan template dan mekanisme impor untuk master data Dokumentasi Legenda atau FAQ jika diperlukan input massal oleh tim admin.

---

## 🧪 Pengujian Otomatis (Automated Testing)

Seluruh modul, live audit logging, upload file, FAQ dinamis, RBAC, dan fitur Import/Export telah dilengkapi feature test komprehensif:

```bash
php artisan test
```

Hasil pengujian terkini: **117 tests passed (644 assertions, 100% passing)**.

### Rincian Test Suite:
- **Modul Tahap 1 Baseline**: 82 tests (382 assertions)
  - `AuthenticationTest`, `DashboardTest`, `AlatTest`, `LokasiTest`, `JadwalTest`, `UserManagementTest`, `AuditLogTest`, `DokumentasiTest`, `FaqTest`, `ProfileTest`.
- **Modul Tahap 2 (Import / Export)**: 35 tests baru (262 assertions baru)
  - `ImportExportFoundationTest` (6 tests): Otorisasi admin vs pekerja, routing `/import-export`, relasi model `ImportExportLog`, dan seeder.
  - `ExportDataTest` (6 tests): Ekspor format XLSX/CSV untuk 4 modul, filter live count, dan proteksi formula injection.
  - `ImportPreviewTest` (8 tests): Generator template 2-sheet, validasi upload file aman, parsing & pratinjau status (Baru, Perbarui, Dilewati, Error), serta pembuatan laporan error XLSX.
  - `ImportConfirmTest` (9 tests): Transaksi atomik `DB::transaction()`, mode skip vs update, normalisasi Alat/Lokasi/Jadwal, pencatatan log riwayat & live audit log.
  - `ImportExportUiAndHistoryTest` (6 tests): Aksesibilitas keyboard, proteksi klik ganda, formatting waktu lokal Indonesia, badge visual, dan sinkronisasi tampilan riwayat.
