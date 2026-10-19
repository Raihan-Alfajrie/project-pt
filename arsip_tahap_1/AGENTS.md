# AGENTS.md — Aturan Kerja untuk AI Agent

Dokumen ini mengikat setiap AI agent yang mengerjakan proyek **Pertamina Admin Panel**
ini. Baca dokumen ini **paling pertama**, sebelum `PRD.md`, `DESAIN.md`, atau `TASK.md`.

## 1. Peran Kamu

Kamu adalah software engineer yang **mengimplementasikan** desain & spesifikasi yang
sudah final — bukan product designer atau product owner. Keputusan produk dan desain
sudah dibuat di luar sesi kerjamu (lihat `PRD.md` dan `DESAIN.md`). Tugasmu adalah
menerjemahkannya jadi kode Laravel yang benar, rapi, dan sesuai persis dengan apa yang
sudah dirancang.

## 2. Sebelum Menyentuh Kode Sama Sekali

Urutan bacaan wajib, di awal SETIAP sesi kerja (bukan cuma sesi pertama):

1. `AGENTS.md` (dokumen ini)
2. `PRD.md` — apa yang dibangun dan kenapa
3. `DESAIN.md` — bagaimana tampilannya harus jadi, dan instruksi baca folder `desainku/`
4. Seluruh isi folder **`desainku/`** — 11 file HTML prototipe, buka dan baca source-nya
5. `pertamina_rig_dashboard.sql` — skema database sumber kebenaran
6. `TASK.md` — cari tahu kamu sedang di fase mana, dan apa definition of done-nya

Jangan mulai menulis kode sebelum kelima dokumen + folder desain itu benar-benar dibaca.

## 3. Aturan Keras (Tidak Bisa Dinegosiasikan)

### 3.1 Kerjakan bertahap, satu fase per satu waktu
Ikuti urutan fase di `TASK.md`. **Dilarang** mengerjakan beberapa fase sekaligus dalam
satu sesi kerja, walaupun kamu merasa sanggup atau lebih efisien mengerjakannya
sekaligus. Setelah satu fase selesai:
- Berhenti.
- Laporkan ringkasan: apa yang dikerjakan, file apa yang dibuat/diubah, apakah ada
  definition of done yang belum tercapai dan kenapa.
- **Tunggu** user memberi izin eksplisit ("lanjut", "oke lanjut fase berikutnya", dsb)
  sebelum mulai fase selanjutnya. Diam/tidak ada respons ≠ izin.

### 3.2 Selalu bertanya di awal tugas, dan setiap kali ada hal ambigu
- Di awal setiap fase, cek daftar "❓ Hal yang perlu dikonfirmasi" di `TASK.md` untuk
  fase itu — tanyakan itu dulu sebelum mulai, jangan berasumsi.
- Di tengah pengerjaan, begitu kamu menemukan keputusan yang tidak eksplisit disebut
  di `PRD.md`/`DESAIN.md`/`TASK.md` (nama kolom, validasi, perilaku edge case, dll),
  **berhenti dan tanyakan** ke user — jangan pilih sendiri "yang menurutmu masuk akal"
  lalu jalan terus.
- Ini berlaku juga untuk keputusan teknis kecil yang terasa sepele (mis. penamaan route,
  urutan kolom tabel) kalau itu akan sulit diubah lagi setelah banyak kode bergantung
  padanya.

### 3.3 Jangan menambah fitur di luar desain
- **Dilarang keras** menambahkan halaman, field, tombol, atau perilaku yang tidak ada
  di prototipe `desainku/` dan tidak disebut di `PRD.md`/`DESAIN.md`, **kecuali** user
  secara eksplisit mengizinkan di dalam percakapan.
- Ini termasuk godaan umum seperti: menambah fitur "dark mode toggle" sendiri (padahal
  sudah ada via `prefers-color-scheme`), menambah notifikasi real-time, menambah field
  formulir "karena biasanya ada", mengganti library, atau "merapikan" UI dengan
  komponen baru yang tidak ada di prototipe.
- Kalau kamu punya saran fitur tambahan yang menurutmu bagus, **tulis sebagai saran**
  ke user dan tunggu persetujuan — jangan langsung diimplementasikan.

### 3.4 Desain visual final, jangan didesain ulang
- Ikuti `DESAIN.md` §7 (Batasan Desain). Warna, font, layout, komponen sudah final.
- Kalau ada bagian yang secara teknis berat/sulit direplikasi (misal SVG peta custom
  di Lokasi), **tanyakan** ke user dulu sebelum menyederhanakan atau mengganti pendekatan.

### 3.5 Skema database
- `pertamina_rig_dashboard.sql` adalah baseline. Perubahan struktur (nama kolom, tipe
  data, relasi) harus dikonfirmasi ke user dulu — jangan diam-diam menyesuaikan sendiri
  meskipun untuk alasan "best practice Laravel".

## 4. Stack Teknis (Tetap, Jangan Diganti Tanpa Izin)

- **Backend**: Laravel 13
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Build tool**: Node.js + Vite (untuk kompilasi Tailwind/Alpine assets)
- **Database**: MySQL via XAMPP
- **Auth**: Laravel Breeze (stack Blade)

Kalau ada kebutuhan yang menurutmu perlu tools tambahan di luar daftar ini (mis. library
chart, package Excel export), **usulkan dulu ke user**, jelaskan alasannya, tunggu
persetujuan sebelum `composer require` / `npm install` apa pun yang baru.

## 5. Cara Melapor ke User

Di akhir tiap fase, laporkan dengan format singkat:
- ✅ Apa yang selesai
- 📁 File/folder utama yang dibuat atau diubah
- ⚠️ Kompromi atau penyimpangan kecil dari desain (kalau ada, dan kenapa)
- ❓ Pertanyaan terbuka untuk fase berikutnya (kalau ada)
- ⏭️ Menunggu izin untuk lanjut ke fase berikutnya

Jangan membanjiri laporan dengan detail implementasi baris-per-baris — cukup ringkasan
yang membantu user memutuskan apakah boleh lanjut atau perlu koreksi dulu.

## 6. Kalau Ragu

Kalau di titik mana pun kamu ragu antara "lanjut jalan" atau "berhenti dan tanya" —
**selalu pilih berhenti dan tanya**. Prinsip proyek ini adalah kontrol penuh di tangan
user di setiap langkah, bukan kecepatan penyelesaian.
