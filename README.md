# Pertamina Admin Panel — Rig Monitoring WK Zona 4

Platform pemantauan rig pemboran dan kerja ulang (workover) internal untuk wilayah kerja Pertamina Hulu Rokan (PHR) Zona 4. Sistem ini memantau status alat/rig, lokasi lapangan kerja, jadwal operasional sumur, manajemen hak akses pengguna, log audit, dokumentasi legenda warna, modul **Import & Export Data Excel/CSV**, serta **Sistem Notifikasi Real-time & Dropdown Navbar**.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 13 (PHP 8.2+)
- **Frontend**: Blade Components + Alpine.js + Tailwind CSS (via Vite)
- **Spreadsheet Engine**: `phpoffice/phpspreadsheet` (v5.10.0) — Export XLSX, CSV (UTF-8 BOM), Import & Multi-sheet Template Parsing
- **Notifikasi**: Laravel Database Notifications (Morph relation, polymorphic storage, real-time polling Alpine.js)
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
Jalankan migrasi dan seeder untuk membuat 14 tabel lengkap dengan baseline data operasional dan contoh notifikasi:
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
| **Budi Santoso** | `budi.santoso@gmail.com` (`budi.santoso`) | **Administrator** | Akses Penuh (Alat, Lokasi, Jadwal, User, Audit Log, Dokumentasi, Import/Export, Notifikasi) |
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
   - **Event Trigger Notifikasi**: Otomatis memicu notifikasi saat ada rig/alat baru atau perubahan status menjadi bermasalah.

3. **Modul Lokasi (`/lokasi`)**:
   - Peta Interaktif Leaflet dengan pin custom beranimasi denyut (*pulse*) sesuai status lapangan.
   - Filter live status dan pencarian kode lapangan.
   - Deep-linking (`?kode=...`) langsung membuka modal detail lapangan dengan daftar sumur dan rig yang beroperasi.
   - CRUD Lapangan.

4. **Modul Jadwal (`/jadwal`)**:
   - 3 Kartu KPI: Total Jadwal, Operasi Berjalan, Selesai.
   - Gantt Chart lengkap 19 bulan dengan penanda garis vertikal *Hari Ini*.
   - Filter lapangan deep-link dan CRUD jadwal operasi.
   - **Event Trigger Notifikasi**: Otomatis memicu notifikasi saat ada jadwal operasi baru atau jadwal yang mengalami kendala.

5. **Manajemen User & Hak Akses (`/users`)**:
   - Eksklusif untuk Administrator.
   - 2 Kartu KPI: Total User (Admin/Pekerja) dan Status Online/Offline.
   - CRUD Akun Pengguna + Pengaturan Hak Akses per modul (`alat`, `lokasi`, `jadwal`) via toggle switch.
   - **Event Trigger Notifikasi**: Memberitahu Administrator saat ada user baru terdaftar, dan memberitahu user bersangkutan ketika hak aksesnya diperbarui.

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
    - **Ekspor Data (Export)**: Format XLSX dan CSV (UTF-8 BOM), Formula Injection Neutralization (`=, +, -, @`).
    - **Impor Data (Import)**: Wizard 3 langkah, template 2-sheet, validasi ketat 5MB & 1.000 baris, pratinjau status (Baru, Perbarui, Dilewati, Error), mode skip vs update, unduh laporan error `.xlsx`.
    - **Eksekusi Atomik & Normalisasi Data**: `DB::transaction()` rollback saat error, normalisasi enum dan relasi Alat, Lokasi, Jadwal.
    - **Panel Riwayat Aktivitas**: Menampilkan 50 log mutasi import/export terbaru pada tabel `import_export_logs`.
    - **Event Trigger Notifikasi**: Otomatis mengirimkan notifikasi ringkasan hasil impor data kepada Administrator.

11. **Sistem Notifikasi Terpadu (`/notifikasi` & Popover Navbar)**:
    - **Ikon Lonceng & Badge Counter Dinamis**:
      - Terletak pada navbar atas di semua halaman.
      - Badge counter merah/putih yang menghitung notifikasi belum dibaca secara instan saat render server awal.
      - Otomatis disembunyikan jika seluruh notifikasi telah dibaca (`unread_count === 0`).
    - **Popover Dropdown Interaktif**:
      - Klik tombol lonceng membuka panel dropdown popover yang elegan (lebar 380px) tepat di bawah navbar dengan animasi transisi halus.
      - **Filter Tabs**: Tab *"Semua"* dan *"Belum Dibaca"* dengan indikator jumlah unread.
      - **Tandai Semua Dibaca Cepat**: Tombol satu-klik pada header untuk menandai semua notifikasi sudah dibaca secara instan.
      - **Daftar Kartu Notifikasi**: Ikon tematik per modul (`danger`, `warning`, `success`, `info`), indikator titik dot belum dibaca, teks pesan ringkas, dan format waktu relatif Bahasa Indonesia.
      - Klik pada item notifikasi otomatis menandai dibaca dan mengarahkan pengguna ke halaman terkait.
      - **Empty State**: Ilustrasi ramah saat tidak ada notifikasi.
    - **Polling Latar Belakang**: Polling AJAX ringan setiap 30 detik via Fetch API & Alpine.js untuk memperbarui badge unread count tanpa perlu refresh halaman.
    - **Halaman Penuh Riwayat Notifikasi (`/notifikasi`)**:
      - Tampilan terstruktur untuk meninjau seluruh riwayat notifikasi pengguna.
      - Filter ganda: berdasarkan status (*Semua*, *Belum Dibaca*) dan berdasarkan modul (*Semua Modul*, *Alat*, *Jadwal*, *Import/Export*, *User*).
      - Tombol aksi massal *"Tandai Semua Sudah Dibaca"* dan navigasi paginasi.
    - **Event Triggers Otomatis & Relevansi Hak Akses (RBAC)**:
      - *Alat & Rig*: Notifikasi penambahan alat baru & peringatan rig bermasalah (dikirim ke Admin & Pekerja yang berhak atas modul `alat`).
      - *Jadwal Operasi*: Notifikasi jadwal baru & kendala operasi tajak (dikirim ke Admin & Pekerja yang berhak atas modul `jadwal`).
      - *Import / Export*: Notifikasi keberhasilan impor batch data massal ke Administrator.
      - *Pengguna*: Notifikasi registrasi user baru ke Admin, dan notifikasi mutasi hak akses ke user bersangkutan.

---

## 🗄️ Struktur Tabel Sistem Notifikasi (`notifications`)

Tabel `notifications` menggunakan skema resmi polymorphic database notification Laravel:

| Kolom | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `id` | `uuid (PK)` | Identifikator unik format UUID |
| `type` | `varchar(255)` | Kelas notifikasi (`App\Notifications\SystemNotification`) |
| `notifiable_type` | `varchar(255)` | Model target relasi (`App\Models\User`) |
| `notifiable_id` | `bigint unsigned` | ID pengguna penerima notifikasi (`users.id`) |
| `data` | `text` | Payload JSON: `judul`, `pesan`, `modul`, `tipe`, `url`, `icon` |
| `read_at` | `timestamp (nullable, indexed)` | Waktu notifikasi dibaca (NULL = belum dibaca) |
| `created_at` | `timestamp` | Waktu notifikasi dibuat |
| `updated_at` | `timestamp` | Waktu pembaharuan data |

---

## 🗄️ Struktur Tabel Riwayat Import/Export (`import_export_logs`)

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
| **Tampilan Dropdown Notifikasi** | 8 item terbaru | Disertai scrolling halus dan link ke halaman penuh |
| **Interval Polling Notifikasi** | 30 Detik | Request ringan ke `/notifications/unread-count` |
| **Formula Protection** | Prefix `'` pada `=, +, -, @` | Mencegah eksekusi spreadsheet formula injection |

---

## 🤝 Keputusan Desain & Rekonsiliasi (PRD §10)

1. **Cakupan Modul Impor**: Hanya modul **Alat**, **Lokasi**, dan **Jadwal**. Modul **Audit Log** bersifat *Read-only* (hanya mendukung Ekspor).
2. **Format Berkas**: Mendukung penuh XLSX dan CSV dengan UTF-8 BOM.
3. **Kebijakan Nilai Kosong pada Mode Update**: **Opsi A Disetujui** — nilai kosong pada kolom opsional di file Excel akan menimpa/mengosongkan data lama di database.
4. **Format Tanggal Jadwal Operasi**: Fleksibel mendukung format `Mmm-YY` (misal: `Jun-26`, `Jul-26`) serta `YYYY-MM` dalam rentang waktu yang didukung (Juni 2026 s/d Desember 2027).
5. **Keamanan Formula**: Netralisasi otomatis karakter formula injection saat ekspor data.
6. **Sistem Notifikasi**: Memanfaatkan tabel `notifications` standar dengan relasi polymorphic, unread count badge, Alpine.js dropdown, dan granular event triggers.

---

## 🔮 Rekomendasi Pengembangan Lanjutan (Future Work)

Berikut adalah saran perbaikan di masa depan (tidak mengikat tahap ini):
1. **Background Queue / Job Workers**: Jika kebutuhan dataset impor meningkat hingga lebih dari 5.000 baris, implementasikan pemrosesan asynchronous via Laravel Queue (`database` / `redis`) disertai progress bar real-time.
2. **Web Push / Browser Desktop Notifications**: Mengintegrasikan browser Service Worker Notification API agar pengguna tetap menerima alert pop-up desktop saat browser berada di latar belakang.
3. **Automated Scheduled Export**: Konfigurasi `php artisan schedule:run` untuk menghasilkan cadangan data berkala (misal ekspor otomatis Audit Log mingguan ke storage terproteksi).

---

## 🧪 Pengujian Otomatis (Automated Testing)

Seluruh modul, live audit logging, upload file, FAQ dinamis, RBAC, fitur Import/Export, Sistem Notifikasi, dan Halaman Profil/About telah dilengkapi feature test komprehensif:

```bash
php artisan test
```

Hasil pengujian terkini: **151 tests passed (813 assertions, 100% passing)**.

### Rincian Test Suite:
- **Modul Tahap 1 Baseline**: 82 tests (382 assertions)
  - `AuthenticationTest`, `DashboardTest`, `AlatTest`, `LokasiTest`, `JadwalTest`, `UserManagementTest`, `AuditLogTest`, `DokumentasiTest`, `FaqTest`, `ProfileTest`.
- **Modul Tahap 2 (Import / Export)**: 35 tests (262 assertions)
  - `ImportExportFoundationTest` (6 tests)
  - `ExportDataTest` (6 tests)
  - `ImportPreviewTest` (8 tests)
  - `ImportConfirmTest` (9 tests)
  - `ImportExportUiAndHistoryTest` (6 tests)
- **Modul Notifikasi Terintegrasi (Fase N1 - N5)**: 29 tests (140 assertions)
  - `NotificationFoundationTest` (7 tests): Skema tabel, model kustom, accessors, scopes, dan seeder.
  - `NotificationApiTest` (10 tests): Otentikasi, isolasi per user, filter unread, unread-count endpoint, mark as read, mark all as read, dan delete.
  - `NotificationUiTest` (5 tests): Render navbar bell button, badge counter unread, popover dropdown, render halaman penuh `/notifikasi`, dan filter.
  - `NotificationTriggerTest` (7 tests): Event trigger saat penambahan/perubahan status Alat, Jadwal, Import massal, dan mutasi permissions user.
- **Modul Halaman About (Pengembangan Lanjutan)**: 5 tests baru (29 assertions baru)
  - `AboutPageTest` (5 tests): Aksesibilitas publik/tamu, navigasi shortcut user terotentikasi, perenderan 7 lapangan WK Zona 4, 6 pilar kapabilitas sistem, dan standar keamanan tata kelola.

