# PANDUAN INSTRUKSI AI: PEMBUATAN ALUR KERJA (FLOWCHART) SISTEM
## Aplikasi Pertamina Admin Panel — Rig Monitoring Zona 4

Dokumen ini berisi instruksi terstruktur bagi Artificial Intelligence (AI) untuk menyusun **alur logika kerja (*flowchart*) utama secara naratif** dari aplikasi web **Pertamina Rig Monitoring Zona 4**. Hasil dari instruksi ini dirancang khusus untuk kebutuhan penulisan bab analisis dan perancangan sistem pada dokumen laporan proyek / laporan teknis.

---

## 1. Konteks & Profil Sistem

- **Nama Aplikasi**: Pertamina Admin Panel — Rig Monitoring Zona 4 (Regional 1 Sumatera).
- **Tujuan Sistem**: Platform pemantauan operasional terintegrasi (*single source of truth*) untuk memonitor armada rig pemboran, status kesiapan peralatan pendukung, timeline penjadwalan tajak sumur, dan pemetaan geospasial lapangan migas di Wilayah Kerja Zona 4.
- **Teknologi**: Laravel 13, Blade Templates, Tailwind CSS, Alpine.js, MySQL, Leaflet Geospasial.
- **Tingkatan Hak Akses (*User Roles*)**:
  1. **Administrator**: Akses manajerial penuh (kelola seluruh modul, manajemen pengguna, konfigurasi sistem, audit trail, serta impor/ekspor data massal).
  2. **Pengawas Lapangan**: Akses monitoring operasional harian, pembaruan status rig/alat, dan pelacakan jadwal pekerjaan sumur.
  3. **Pekerja**: Akses pemantauan data publik operasional, jadwal aktif, serta pengaturan profil mandiri.

---

## 2. Instruksi bagi AI (System Prompt)

Saat menyusun alur kerja (*flowchart*) berdasarkan dokumen ini, AI wajib mengikuti aturan format berikut:

1. **Format Penyajian**:
   - Sajikan dalam bentuk **teks narasi logika langkah demi langkah** yang terstruktur, formal, dan mudah dipahami.
   - Jangan menyertakan kode sintaks diagram (seperti Mermaid atau PlantUML).
   - Gunakan terminologi standar flowchart pada setiap tahapan, seperti:
     - `[Titik Mulai]` (*Start / Entry Point*)
     - `[Input / Data]` (*User Input / Data Entry*)
     - `[Keputusan / Percabangan]` (*Decision / Conditional Logic*)
     - `[Proses]` (*System Processing / Execution*)
     - `[Otorisasi Role]` (*Access Control Validation*)
     - `[Output / Tampilan]` (*Display / View Screen*)
     - `[Titik Selesai]` (*End / Termination*)
2. **Bahasa**:
   - Gunakan Bahasa Indonesia formal dan baku yang sesuai dengan kaidah penulisan laporan teknis/akademis enterprise.
3. **Cakupan Alur**:
   - Fokus pada **Alur Utama Global Sistem (End-to-End System Flow)** dari saat pengguna pertama kali mengakses aplikasi, melewati gerbang autentikasi, mengakses dashboard operasional berdasarkan role, menjalankan navigasi menu utama, hingga mengakhiri sesi kerja (logout).

---

## 3. Rincian Logika Alur Utama Global (Spesifikasi untuk AI)

AI harus menyusun alur naratif global yang mencakup 6 fase berurutan berikut:

### Fase 1: Akses Awal & Halaman Beranda (Landing Page)
- Pengguna mengakses alamat web sistem.
- Sistem menampilkan halaman Beranda publik (`Home`) yang memuat ringkasan platform, metrik statistik rig (16 rig termonitor, 14 struktur lapangan, dll.), dan profil WK Zona 4 (`About`).
- Pengguna dapat memilih preferensi tema antarmuka (Mode Gelap / Mode Terang) melalui tombol navbar yang tersimpan di memori lokal (*localStorage*).
- Pengguna memilih tindakan berikutnya: melakukan pendaftaran akun baru atau masuk ke sistem (*login*).

### Fase 2: Autentikasi Pengguna (Login & Registrasi)
- **Kasus A: Registrasi Akun Baru (`/register`)**:
  1. Pengguna memasukkan data: Nama Lengkap, Username, Email, dan Password (minimal 6 karakter).
  2. Sistem memvalidasi kelengkapan data, keunikan username, serta format email.
  3. *Percabangan*:
     - Jika data tidak valid / sudah terdaftar: Sistem menampilkan pesan kesalahan (*validation error*) dan mengembalikan pengguna ke form pendaftaran.
     - Jika data valid: Sistem membuat akun pengguna baru dengan peran default sebagai **Pekerja**, mencatat riwayat ke audit log, menginisialisasi sesi login, dan mengarahkan pengguna ke Dashboard Utama.
- **Kasus B: Masuk ke Akun (`/login`)**:
  1. Pengguna memasukkan identitas berupa Username atau Email, serta Password.
  2. Pengguna dapat mencentang opsi "Ingat Saya" (*Remember Me*).
  3. Sistem memvalidasi kredensial pengguna ke basis data dengan keamanan hash bcrypt.
  4. *Percabangan*:
     - Jika kredensial salah: Sistem menolak akses, menampilkan pesan peringatan autentikasi gagal, dan mencatat percobaan gagal ke log keamanan.
     - Jika kredensial benar: Sistem memperbarui waktu aktivitas terakhir pengguna (status online real-time), mencatat aktivitas berhasil masuk ke Audit Log, dan membuka sesi kerja pengguna.

### Fase 3: Verifikasi Hak Akses & Inisialisasi Dashboard
- Sistem memeriksa atribut peran (*role*) dari pengguna yang berhasil login:
  - **Jika Pengguna berstatus Administrator**: Sistem memberikan menu navigasi penuh di sidebar, mencakup Dashboard, Alat/Rig, Lokasi, Jadwal, Manajemen User, dan Audit Log.
  - **Jika Pengguna berstatus Pengawas Lapangan**: Sistem menampilkan menu operasional terfokus untuk pemantauan data rig, lokasi lapangan, dan jadwal tajak sumur.
  - **Jika Pengguna berstatus Pekerja**: Sistem menampilkan menu pemantauan informasi operasional standar dan profil akun.
- Sistem menampilkan antarmuka **Dashboard Utama** yang menyajikan KPI ringkasan:
  - Jumlah total rig aktif, standby, moving, dan breakdown.
  - Statistik realisasi tajak dan completed sumur.
  - Jam operasional sinkron berbasis zona waktu Asia/Jakarta (WIB).

### Fase 4: Siklus Navigasi & Aktivitas Operasional
Pengguna memilih menu operasional sesuai kewenangannya:
1. **Modul Monitoring Alat & Rig**:
   - Melihat daftar inventaris armada rig (Drilling & Workover).
   - Memeriksa status kesiapan operasional alat dan foto fisik rig.
   - Admin/Pengawas dapat memperbarui data status operasional.
2. **Modul Pemetaan Lapangan (Lokasi)**:
   - Membuka peta interaktif geospasial berbasis Leaflet yang memetakan titik koordinat sumur dan rig di struktur lapangan Zona 4 (Prabumulih, Limau, Pendopo, Adera, Ramba, dll.).
   - Peta secara otomatis menyesuaikan tampilan latar (*Dark Canvas* atau *Light Canvas*) mengikuti tema aktif sistem.
3. **Modul Penjadwalan Pemboran (Jadwal)**:
   - Memantau timeline pekerjaan sumur berbasis visualisasi bagan waktu (*Gantt timeline*).
   - Memeriksa kategori status: Selesai, Progres, Kendala, atau Menunggu.

### Fase 5: Notifikasi & Preferensi Sesi
- Pengguna dapat memantau peringatan dini melalui ikon lonceng di navbar kanan (misalnya jika ada kendala rig atau perubahan jadwal operasional).
- Pengguna dapat mengubah mode tampilan visual kapan saja antara **Mode Terang** (ikon bulan sabit) dan **Mode Gelap** (ikon matahari) tanpa memutus sesi kerja aktif.

### Fase 6: Pengakhiran Sesi (Logout)
- Pengguna menekan tombol "Keluar" (*Logout*).
- Sistem memproses penutupan sesi:
  1. Menghapus token sesi kerja autentikasi.
  2. Memperbarui status keaktifan user menjadi non-aktif/offline.
  3. Mencatat aktivitas keluar ke Audit Log sistem.
  4. Mengarahkan kembali antarmuka pengguna ke Halaman Beranda (`Home`) atau halaman Login.
- Alur kerja sistem selesai.

---

## 4. Format Output yang Diharapkan dari AI

AI yang membaca instruksi ini diharapkan menyajikan narasi alur kerja laporan dengan struktur berikut:

```markdown
### ALUR KERJA UTAMA SISTEM (GLOBAL WORKFLOW)
#### Sistem Pemantauan Operasional Rig Pertamina Zona 4

1. [Titik Mulai]: Pengguna mengakses URL aplikasi web.
2. [Tampilan]: Sistem menyajikan Halaman Beranda publik (Home) ...
3. [Keputusan]: Apakah pengguna sudah memiliki akun?
   - Jika Belum: Menuju alur Pendaftaran Akun ...
   - Jika Sudah: Menuju alur Masuk Akun ...
... (dilanjutkan hingga selesai) ...
N. [Titik Selesai]: Sesi kerja berakhir dan pengguna keluar dari sistem.
```

---

## 5. Ringkasan Karakteristik Alur untuk Laporan

| Parameter | Keterangan Alur Global |
| :--- | :--- |
| **Titik Masuk Utama** | Web Browser (`/` atau `/login`) |
| **Lapisan Keamanan** | Middleware Autentikasi Laravel & Proteksi Role berbasis Otorisasi |
| **Zona Waktu Operasional** | Asia/Jakarta (WIB, UTC+7) pada seluruh modul & audit trail |
| **Penyimpanan Sesi** | Secure Session Cookie + Database Token |
| **Penyimpanan Preferensi** | Web Local Storage (`pertamina_theme` untuk Light/Dark mode) |
| **Titik Akhir Sesi** | Invalidation Session via HTTP POST `/logout` |

