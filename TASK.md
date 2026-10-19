# TASK.md — Roadmap: "Tahapan Pengembangan Lanjutan"

> **ATURAN WAJIB:** satu fase per satu waktu. Selesai satu fase → **berhenti**, laporkan, **tunggu izin eksplisit** user.
> Awal setiap fase → ajukan pertanyaan "❓" fase itu dulu. Detail aturan: `AGENTS.md`.
> Dokumen ini menggantikan TASK tahap 1 (arsip: `arsip_tahap_1/`).

---

## BAGIAN A — Tahap 1 (SELESAI)

Berdasarkan `README.md` hasil kerja agent sebelumnya, Fase 0–10 tahap 1 dianggap **selesai**:
setup Laravel 13 + MySQL, Breeze, layout & Dashboard, Alat, Lokasi, Jadwal, Manajemen User & hak akses,
Audit Log, Dokumentasi, FAQ, Profil, polish — dengan 82 feature test lulus.
Tidak ada yang perlu dikerjakan ulang dari bagian ini **kecuali** diputuskan user di Fase L0.

---

## BAGIAN B — Tahapan Pengembangan Lanjutan

| Fase | Nama | Mengubah kode? |
|---|---|---|
| L0 | Sinkronisasi & Audit Kesesuaian | ❌ (hanya baca + laporan) |
| L1 | Fondasi Import / Export | ✅ |
| L2 | Export Data | ✅ |
| L3 | Import — Template, Unggah, Validasi & Pratinjau | ✅ |
| L4 | Import — Konfirmasi & Penyimpanan | ✅ |
| L5 | Riwayat & Penyempurnaan | ✅ |
| L6 | Penutupan & Serah Terima | ✅ (dokumentasi/test) |

---

### Fase L0 — Sinkronisasi & Audit Kesesuaian  *(tidak ada perubahan kode)*

**Tujuan:** memahami kondisi repositori yang sebenarnya dan menyelaraskan dokumen dengan kenyataan sebelum menambah fitur.

**Langkah:**
1. Baca semua dokumen & folder `desainku/` (urutan di `AGENTS.md` §2), termasuk prototipe Import / Export.
2. Jelajahi repositori: `routes/web.php`, controller, model, policy/middleware, migrasi, seeder, view Blade, komponen, test.
3. Jalankan `php artisan test` **sebelum menyentuh apa pun** dan catat hasilnya (README mengklaim 82 test lulus).
4. Buat **laporan kesesuaian** (tabel) untuk setiap butir di `PRD.md` §9 (13 butir) + temuan baru: *Desain/PRD · Implementasi di kode · Bukti (file/baris) · Status dugaan (kemungkinan disetujui / kemungkinan menyimpang / tidak diketahui) · Rekomendasi (pertahankan / kembalikan / perbarui dokumen)*.
5. Catat informasi teknis yang dibutuhkan fase berikutnya: versi PHP & Laravel (`php -v`, `composer show laravel/framework`), ekstensi PHP aktif, nilai ENUM status Alat/Jadwal yang sebenarnya di migrasi, jendela bulan Gantt yang dipakai (di kode), struktur tabel `audit_log` (nilai `modul`/`aksi` yang boleh), library yang sudah terpasang (`composer.json`, `package.json`), pola middleware/otorisasi admin, komponen Blade yang bisa dipakai ulang (toast, modal, chip, tabel).
6. Periksa apakah kode berada di bawah Git (jangan melakukan commit/perubahan apa pun; hanya catat).

**❓ Hal yang wajib ditanyakan sebelum mulai L0:**
- Apakah ada instruksi khusus tentang cara menjalankan test (database test terpisah? `.env.testing`?).
- Apakah boleh menjalankan `php artisan test` (menggunakan database test) — dan database mana yang aman dipakai?
- Apakah proyek memakai Git, dan bagaimana kebijakan commit (lihat `AGENTS.md` §3.9)?

**Definition of Done:** laporan kesesuaian + catatan teknis diserahkan; user memberi keputusan untuk **setiap** butir perbedaan
(pertahankan / kembalikan / perbarui dokumen) dan menjawab pertanyaan teknis yang muncul. Tidak ada perubahan file kode.

---

### Fase L1 — Fondasi Import / Export

**Lingkup:** persiapan tanpa logika Excel.
- Usulkan library Excel (≥ 2 opsi, kelebihan/kekurangan, kompatibilitas Laravel 13/PHP/ekstensi XAMPP) → **tunggu persetujuan** → baru `composer require`.
- Usulkan skema tabel riwayat → **tunggu persetujuan** → migrasi baru (jangan edit migrasi lama).
- Route + middleware/policy khusus Administrator; item sidebar "Import / Export" (hanya admin) setelah Jadwal.
- Halaman Blade `import-export` mereplikasi **tampilan** prototipe (3 panel, stepper, dropzone, kartu modul, tabel riwayat) dengan Alpine.js tetapi **belum** logika server — tombol belum berfungsi atau memberi pesan jelas.
- Model + factory untuk riwayat.

**❓ Konfirmasi dulu:** library, skema riwayat, nama route, apakah halaman memakai komponen toast/modal yang sudah ada, serta jawaban PRD §10 butir 1, 2, 7, 8.

**Test minimal:** Administrator bisa membuka halaman (200); Pekerja ditolak (403/redirect sesuai pola yang ada); tamu diarahkan ke login; item sidebar hanya muncul untuk admin.

**Definition of Done:** halaman tampil serupa prototipe, akses terkontrol, migrasi berjalan di database dev tanpa menghapus data, semua test lama + baru lulus.

---

### Fase L2 — Export Data

- Hitungan baris sesuai filter (endpoint ringan) untuk teks "N baris akan diekspor".
- Export 4 modul: Alat, Lokasi, Jadwal, Audit Log — `.xlsx` & `.csv` (BOM UTF-8), nama file sesuai FR-EX-05, judul kolom persis PRD §6.
- Filter per modul (FR-EX-02) dipakai juga oleh unduhan (tidak boleh beda dengan hitungan).
- Netralisasi formula injection (FR-EX-09).
- Catat ke riwayat (Export, Sukses).
- Hubungkan UI: kartu modul, filter dinamis, segmented format, tombol Unduh (nonaktif bila 0).

**❓ Konfirmasi dulu:** nilai tampilan Status Alat/Kategori Jadwal yang diekspor (istilah aplikasi sekarang vs prototipe), kolom tambahan yang ada di aplikasi tetapi tak ada di prototipe (mis. foto, koordinat) — ikut diekspor atau tidak (default: tidak), PRD §10 butir 5, 6.

**Test minimal:** isi & header file per modul; filter; format csv vs xlsx; nilai berawalan `=`/`+`/`-`/`@` ternetralisasi; akses admin saja; entri riwayat tercipta; jumlah baris = hitungan.

**DoD:** semua kombinasi modul × format berfungsi, hasil export bisa dibuka di Excel, test lulus.

---

### Fase L3 — Import: Template, Unggah, Validasi & Pratinjau  *(SELESAI)*

- [x] Unduh template per modul (sheet `Template` + `Petunjuk`).
- [x] Endpoint unggah: validasi file sisi server (ekstensi, MIME, ukuran 5MB, maks 1.000 baris), parsing aman tanpa eksekusi formula/makro.
- [x] Parser + validator **per modul** (`ImportService`): pencocokan judul kolom (case/tanda baca-insensitif + alias), normalisasi nilai, semua aturan domain PRD §6, deteksi duplikat di dalam file, deteksi data sudah ada di database (`existingMap`), klasifikasi Baru/Perbarui/Dilewati/Error sesuai mode.
- [x] Respons JSON pratinjau → UI langkah 3: ringkasan chips (Total, Baru, Perbarui, Dilewati, Error), radio mode (ganti mode memperbarui klasifikasi tanpa unggah ulang), tabel pratinjau (maks 500 baris), pesan sesuai `DESAIN.md` §5.5.
- [x] Unduh laporan error (`laporan_error_{modul}_{tanggal}.xlsx`) dengan sheet `Error`.
- [x] Belum ada penulisan ke tabel Alat/Lokasi/Jadwal pada fase ini (disimpan sementara di Cache bertoken untuk konfirmasi Fase L4).
- [x] 8 feature test baru di `ImportPreviewTest` lulus (total 102 test, 505 assertions lulus).

**DoD:** pratinjau akurat untuk 3 modul, perilaku identik dengan prototipe pada kasus uji yang sama, tidak ada perubahan data modul, test lulus.

---

### Fase L4 — Import: Konfirmasi & Penyimpanan  *(SELESAI)*

- [x] Endpoint konfirmasi (`POST /import-export/confirm`): memproses seluruh baris batch, memakai transaksi atomik `DB::transaction()`; Baru → insert, Perbarui → update (Opsi A: nilai kosong di Excel mengosongkan nilai lama di database), Dilewati/Error → tidak diubah.
- [x] Efek samping per modul:
  - Lokasi: kode huruf besar (`strtoupper`), tanggal_dipetakan otomatis terisi.
  - Alat: normalisasi enum status & kategori ke schema database (aktif/standby/bermasalah, rig/alat_pendukung).
  - Jadwal: pencocokan nama rig ke `alat_id`, relasi kategori ke `dokumentasi_item`, status enum (selesai/progres/kendala), konversi bulan ke format tanggal `YYYY-MM-01`.
- [x] Pencatatan Riwayat (`import_export_logs`): status `sukses`, `sebagian` (bila ada error yang dilewati), atau `gagal` (saat fatal rollback) dengan jumlah baris_berhasil dan baris_total.
- [x] Pencatatan live Audit Log: merekam aksi import dengan format keterangan terstandarisasi.
- [x] Pesan sukses persis `DESAIN.md` §5.5 dengan UI toast mengambang responsif; reset state form import ke kondisi awal; reload untuk sinkronisasi hitungan kartu dan riwayat.
- [x] Pembersihan cache pratinjau bertoken setelah konfirmasi berhasil dieksekusi.
- [x] 9 feature test baru di `ImportConfirmTest` lulus (total 111 test, 619 assertions lulus).

**DoD:** import end-to-end berfungsi untuk 3 modul, data konsisten, test lulus.

---

### Fase L5 — Riwayat & Penyempurnaan  *(SELESAI)*

- [x] Panel Riwayat tampil dari database (urut terbaru, batas 50 entri sesuai prototipe, read-only tanpa tombol hapus riwayat sesuai kesepakatan).
- [x] Toast mengambang responsif dengan varian sukses (hijau) & error (merah), empty state ramah ("Belum ada riwayat import / export."), loading indicator saat unggah/proses, pencegahan klik ganda pada seluruh tombol aksi.
- [x] Aksesibilitas keyboard lengkap (`tabindex="0"`, focus-visible merah terstandarisasi, penanganan Enter dan Space pada kartu dan radio).
- [x] Responsivitas multi-breakpoint (ponsel, tablet, desktop) dan dukungan mode gelap/terang.
- [x] Format tanggal lokal Bahasa Indonesia terstandarisasi (`waktu_display`: misal "11 Okt 2026, 13:30").
- [x] Teks antarmuka dan pesan validasi 100% selaras `DESAIN.md` §5.5.
- [x] 6 feature test baru di `ImportExportUiAndHistoryTest` lulus (total 117 test, 644 assertions lulus).

**DoD:** tampilan & perilaku selaras prototipe di semua kondisi (kosong, error, sukses, memproses), test lulus.

---

### Fase L6 — Penutupan & Serah Terima  *(SELESAI)*

- [x] Jalankan seluruh test; laporkan jumlah test & assertion baru (117 test, 644 assertions lulus, 100% passing).
- [x] Perbarui `README.md`: fitur Import/Export, library & versinya, batas ukuran/baris, cara pakai, struktur tabel riwayat, jumlah test.
- [x] Daftar akhir: keputusan §10 PRD & status rekonsiliasi L0 (seluruh butir telah terjawab dan terdokumentasi).
- [x] Rekomendasi lanjutan (hanya saran, jangan dikerjakan): queue untuk file besar, export terjadwal, import modul lain.

**DoD:** `composer install`, `npm install`, `php artisan migrate --seed`, `npm run build`, `php artisan serve` → aplikasi berjalan; seluruh test lulus; README mutakhir; laporan akhir diserahkan.
