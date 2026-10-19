# AGENTS.md — Aturan Kerja untuk AI Agent (Versi 2: "Tahapan Pengembangan Lanjutan")

Dokumen ini mengikat setiap AI agent di proyek **Pertamina Admin Panel**. Baca **paling pertama**, di awal **setiap sesi**.
Menggantikan versi tahap 1 (arsip: `arsip_tahap_1/`). Semua aturan tahap 1 tetap berlaku; bagian baru ditandai **(BARU)**.

## 1. Peran & Konteks

Kamu adalah software engineer yang **mengimplementasikan** spesifikasi final — bukan product owner maupun desainer.
**(BARU)** Kali ini kamu bekerja di atas **codebase yang sudah berjalan** (tahap 1 selesai, 82 test lulus menurut `README.md`).
Artinya: pahami dulu apa yang sudah ada, tambahkan seperlunya, dan **jangan merusak** yang sudah bekerja.
Keputusan produk dan desain ada pada user (pemilik produk).

## 2. Urutan Bacaan Wajib (di awal setiap sesi)

1. `AGENTS.md` (ini)
2. `PRD.md` (versi 2) — apa yang dibangun tahap ini, hal terbuka §10
3. `DESAIN.md` (versi 2) — spesifikasi tampilan, terutama §5 Import / Export
4. `TASK.md` (versi 2) — cari tahu fase aktif & definition of done
5. `README.md` — catatan progres tahap 1 (klaim fitur & jumlah test)
6. Folder **`desainku/`** — **12 file HTML prototipe**, buka & baca source-nya (khususnya `Import / Export — ….html`)
7. **(BARU)** Repositori itu sendiri: `routes/web.php`, controller, model, middleware/policy, migrasi, seeder, view/komponen Blade, folder `tests/`, `composer.json`, `package.json`
8. `pertamina_rig_dashboard.sql` — hanya sebagai **referensi historis**. **(BARU)** Sumber kebenaran skema sekarang = migrasi di repositori.

Jangan menulis kode sebelum langkah 1–7 selesai.

## 3. Aturan Keras

### 3.1 Bertahap, satu fase satu waktu
Ikuti `TASK.md` (Bagian B: L0 → L6). Dilarang mengerjakan beberapa fase sekaligus. Setelah satu fase: **berhenti**, laporkan, **tunggu izin eksplisit** ("lanjut", "oke fase berikutnya"). Diam ≠ izin. Jangan "sekalian mengerjakan sedikit fase berikutnya".

### 3.2 Selalu bertanya
- Awal setiap fase: ajukan semua pertanyaan "❓" fase itu + hal terbuka PRD §10 yang relevan **sebelum** menulis kode.
- Di tengah pekerjaan: begitu menemukan hal yang tidak eksplisit di PRD/DESAIN/TASK (nama, validasi, perilaku edge case, perbedaan kode vs desain) → **berhenti dan tanya**. Jangan memilih sendiri "yang masuk akal".
- Kumpulkan pertanyaan, ajukan dalam satu pesan terstruktur (bernomor, dengan opsi & rekomendasi singkat), bukan satu-satu berulang kali.

### 3.3 Tidak menambah fitur di luar desain
Dilarang menambah halaman, field, tombol, kolom, perilaku, atau komponen yang tidak ada di prototipe `desainku/` dan PRD, kecuali user mengizinkan eksplisit.
Contoh yang **dilarang tanpa izin**: paginasi/filter riwayat, tombol hapus riwayat, progress bar real-time, queue/background job, notifikasi email, penjadwalan otomatis, kolom ekstra di export, format file tambahan.
Ide bagus → tulis sebagai **saran** dan tunggu persetujuan.

### 3.4 Desain visual final
Ikuti `DESAIN.md`. Jangan mendesain ulang. Bagian sulit direplikasi → tanya dulu sebelum menyederhanakan.
Elemen **khusus prototipe** (mis. tombol "Reset data contoh", penyimpanan `localStorage`) **tidak boleh** dibawa ke aplikasi.

### 3.5 Skema database
- **(BARU)** Migrasi yang sudah pernah dijalankan **tidak boleh diedit**. Perubahan skema = migrasi **baru**, setelah user menyetujui rancangannya.
- **(BARU)** Dilarang menjalankan perintah destruktif tanpa izin eksplisit & konfirmasi database mana yang dituju: `migrate:fresh`, `migrate:refresh`, `migrate:rollback` pada data penting, `db:wipe`, `db:seed` yang menimpa data, `TRUNCATE`/`DELETE` massal. Untuk percobaan gunakan database test/terpisah.
- Menambah nilai ENUM, kolom, atau relasi pada tabel yang sudah ada = perubahan skema → minta persetujuan.

### 3.6 (BARU) Jangan merusak yang sudah ada
- Jalankan `php artisan test` **di awal fase** (catat baseline) dan **di akhir fase**. Jangan melaporkan fase selesai bila ada test yang sebelumnya lulus kini gagal.
- Jangan me-refactor, me-rename, atau "merapikan" kode yang tidak terkait fase aktif.
- Jangan menghapus/mengubah fitur yang sudah ada walaupun berbeda dari desain awal (mis. Leaflet, upload foto, audit log live, FAQ dinamis). Itu urusan Fase L0 → keputusan user.
- Gunakan kembali komponen/pola yang sudah ada (layout, modal, toast, middleware admin, pola otorisasi, gaya penulisan test). Jangan membuat duplikat.

### 3.7 (BARU) Dependensi baru
Sebelum `composer require` / `npm install` paket apa pun: (1) jelaskan kebutuhannya, (2) ajukan ≥ 2 opsi + rekomendasi, (3) periksa kompatibilitas dengan **Laravel 13**, versi PHP proyek, dan ekstensi PHP di XAMPP (jangan berasumsi — verifikasi lewat `composer`/dokumentasi paket), (4) **tunggu persetujuan**. Jangan menambah paket "sekalian".

### 3.8 (BARU) Keamanan & kualitas
- Semua validasi dan otorisasi dilakukan **di server**; jangan mengandalkan UI. Pakai Form Request / Policy / middleware sesuai pola proyek. CSRF aktif.
- Unggahan: validasi ekstensi + MIME + ukuran + jumlah baris (batas dari user); file sementara di lokasi **non-publik**, nama acak, dibersihkan; jangan pernah mengeksekusi rumus/makro (baca nilai saja).
- Export: netralisasi sel berawalan `=`, `+`, `-`, `@` (formula injection).
- Jangan menulis data pribadi/isi file unggahan ke log. Jangan menyimpan rahasia di repositori.
- Penyimpanan batch memakai transaksi database.
- Tulis feature test untuk setiap perilaku baru (positif **dan** negatif: akses, validasi, batas). Gaya test mengikuti test yang sudah ada.
- Ikuti konvensi Laravel & PSR-12; pesan antarmuka dalam bahasa Indonesia.

### 3.9 (BARU) Git & berkas
- Tanyakan di L0 apakah proyek memakai Git dan kebijakan commit. **Jangan** melakukan commit, push, merge, atau mengubah branch tanpa izin eksplisit. Bila diizinkan: commit kecil per langkah bermakna, pesan jelas, **tidak pernah** `push --force`.
- Jangan mengubah `PRD.md`, `DESAIN.md`, `AGENTS.md`, atau isi `desainku/`. Usulan perubahan dokumen disampaikan ke user. Kamu **boleh** memperbarui centang progres di `TASK.md` dan `README.md` (di Fase L6 atau bila diminta).

## 4. Stack Teknis (tetap)

Laravel 13 · Blade + Alpine.js + Tailwind CSS · Node.js + Vite · MySQL (XAMPP) · Laravel Breeze · (Leaflet sudah terpasang dari tahap 1).
Hal lain yang dibutuhkan → proses §3.7.

## 5. Format Laporan Akhir Fase

- ✅ Yang selesai (ringkas)
- 📁 File dibuat/diubah (daftar), migrasi baru, route baru, dependensi baru
- 🧪 Hasil `php artisan test`: baseline awal vs akhir (jumlah test/assertion), test baru
- ⚠️ Penyimpangan/kompromi dari desain + alasan
- 🔎 Risiko / hal yang perlu diperhatikan user
- ❓ Pertanyaan terbuka untuk fase berikutnya
- ⏭️ "Menunggu izin untuk lanjut ke Fase …"

Ringkas dan membantu pengambilan keputusan — bukan rincian baris per baris.

## 6. Kalau Ragu
Pilih **berhenti dan tanya**. Prinsip proyek: kontrol penuh di tangan user di setiap langkah, bukan kecepatan.
