# PROMPT_LANJUTAN.md — Prompt Kickoff "Tahapan Pengembangan Lanjutan"

> Tempel **semua isi di antara dua garis `---` di bawah** sebagai pesan pertama ke AI agent.
> Sebelum itu pastikan di root proyek tersedia: `AGENTS.md`, `PRD.md`, `DESAIN.md`, `TASK.md` (versi 2),
> `README.md` (progres tahap 1), folder `desainku/` (12 file HTML — termasuk `Import / Export — Pertamina Admin Panel.html`),
> dan (opsional, referensi historis) `pertamina_rig_dashboard.sql`.

---

# KONTEKS

Kamu melanjutkan proyek **Pertamina Admin Panel — Rig Monitoring WK Zona 4**, web dashboard internal berbasis
**Laravel 13 + Blade + Alpine.js + Tailwind CSS + MySQL (XAMPP) + Node.js/Vite + Laravel Breeze**.

**Tahap 1 sudah selesai.** Menurut `README.md`: 11 menu (Home, About, Dashboard, Alat, Lokasi, Jadwal, Manajemen User, Audit Log,
Dokumentasi, FAQ, Profil) sudah berjalan, ada RBAC Administrator/Pekerja, dan **82 feature test lulus**.
Kamu **tidak** memulai dari nol. Kamu bekerja di atas kode yang sudah berjalan, jadi tugas pertamamu adalah *memahami* sebelum *mengubah*.

Tahap yang sekarang disebut **"Tahapan Pengembangan Lanjutan"** punya dua tujuan:

1. **Membangun menu baru "Import / Export"** (menu ke-12) — unduh/unggah data Excel untuk modul Alat, Lokasi, Jadwal (+ Audit Log hanya export).
   Prototipe final + perilakunya ada di `desainku/Import / Export — Pertamina Admin Panel.html` (prototipe ini berfungsi sungguhan membaca & menulis .xlsx di browser — jadikan itu spesifikasi perilaku).
2. **Rekonsiliasi**: `README.md` memperlihatkan beberapa perbedaan antara hasil tahap 1 dan desain/PRD awal. Perbedaan itu harus **dicatat dan diputuskan user**, bukan diubah atau dihapus sepihak.

# URUTAN BACAAN WAJIB (jangan menulis kode sebelum semuanya selesai)

1. `AGENTS.md` — aturan kerjamu. Mengikat semua tindakanmu.
2. `PRD.md` (versi 2) — terutama §5 (requirement), §6 (kolom & validasi per modul), §8 (perubahan data), §9 (perbedaan README vs desain), §10 (hal terbuka).
3. `DESAIN.md` (versi 2) — terutama §5 (spesifikasi halaman Import / Export), §5.5 (teks pesan), §8.
4. `TASK.md` (versi 2) — Bagian B, Fase L0–L6.
5. `README.md` — catatan progres tahap 1.
6. Folder `desainku/` — buka **keduabelas** file HTML dan baca source-nya (HTML, CSS, JavaScript). Untuk `Import / Export …html`, pelajari fungsi
   `MODS` (definisi kolom & alias), `parseRows`, `clean` per modul, `parseBulan`, `classify`, serta alur export/template/laporan error/riwayat.
7. Repositori: `routes/web.php`, controller, model, middleware/policy, migrasi, seeder, view & komponen Blade, `tests/`, `composer.json`, `package.json`.

Setelah itu, jangan langsung bekerja. Ikuti bagian "YANG HARUS KAMU LAKUKAN SEKARANG".

# ATURAN INTI (ringkasan — rincian lengkap di AGENTS.md)

1. **Satu fase satu waktu.** Selesai satu fase → berhenti, laporkan, **tunggu izin eksplisit** dariku. Dilarang mengerjakan fase berikutnya "sekalian".
2. **Tanya dulu.** Di awal setiap fase dan setiap kali ada hal ambigu atau tidak tertulis. Jangan berasumsi. Kumpulkan pertanyaan dalam satu pesan bernomor, beri opsi + rekomendasimu.
3. **Jangan menambah fitur di luar desain** (prototipe di `desainku/` + PRD) tanpa izin eksplisitku. Ide bagus → sampaikan sebagai saran.
4. **Jangan merusak yang sudah ada.** Jalankan `php artisan test` di awal & akhir fase; jangan ada test lama yang jadi gagal. Jangan refactor kode yang tidak terkait.
5. **Jangan menghapus/mengubah fitur tahap 1 yang berbeda dari desain awal** (Leaflet, upload foto, audit log live, FAQ dinamis, dst.). Itu dibahas di Fase L0 dan aku yang memutuskan.
6. **Migrasi lama tidak boleh diedit.** Perubahan skema = migrasi baru, setelah rancangannya kusetujui.
7. **Dilarang perintah destruktif** (`migrate:fresh`, `migrate:refresh`, `db:wipe`, seed yang menimpa data, TRUNCATE/DELETE massal) tanpa izin eksplisitku dan tanpa konfirmasi database mana yang dituju.
8. **Dependensi baru** (`composer require` / `npm install`) hanya setelah kamu mengusulkan ≥ 2 opsi, memverifikasi kompatibilitas dengan Laravel 13 / versi PHP proyek / ekstensi PHP di XAMPP, dan aku menyetujui.
9. **Git:** jangan commit/push/merge/ganti branch tanpa izinku.
10. **Jangan mengubah** `PRD.md`, `DESAIN.md`, `AGENTS.md`, atau isi `desainku/`. Usul perubahan dokumen → sampaikan padaku.
11. **Keamanan:** validasi & otorisasi di server; unggahan divalidasi (ekstensi, MIME, ukuran, jumlah baris); file sementara non-publik & dibersihkan; rumus/makro tidak pernah dieksekusi; export menetralkan sel berawalan `=`, `+`, `-`, `@`; simpan batch dengan transaksi; data pribadi tidak masuk log.
12. **Tulis feature test** untuk setiap perilaku baru (positif & negatif). Ikuti gaya test yang sudah ada.
13. Elemen khusus prototipe (tombol "Reset data contoh", `localStorage`, mekanisme unduhan platform pratinjau) **tidak dibawa** ke aplikasi.

# RINGKASAN FITUR YANG AKAN DIBANGUN (detail: PRD §5–§6 dan DESAIN §5)

- **Hak akses:** hanya Administrator. Item sidebar "Import / Export" di grup *Manajemen Data* tepat setelah *Jadwal*, hanya tampil untuk admin. Pekerja ditolak.
- **Halaman `/import-export`**, 3 panel bertumpuk:
  1. **Export Data** — pilih modul (Alat/Lokasi/Jadwal/Audit Log), filter dinamis per modul, format .xlsx/.csv, teks "N baris akan diekspor dari …", tombol Unduh (nonaktif jika 0).
  2. **Import Data** — Langkah 1: pilih modul + lihat kolom + unduh template (sheet `Template` & `Petunjuk`). Langkah 2: dropzone. Langkah 3: pratinjau per baris (Baru/Perbarui/Dilewati/Error + nomor baris Excel + pesan), ringkasan, dua mode (skip / update), unduh laporan error, Batal, Konfirmasi Import (N baris).
  3. **Riwayat** — Waktu · User · Jenis · Modul · Nama File · Baris · Status (Sukses/Sebagian/Gagal).
- **Kunci pencocokan:** Alat = No. Seri · Lokasi = Kode Lapangan · Jadwal = Rig + Nama Sumur/Pekerjaan.
- **Baris Error tidak diimpor; baris Dilewati tidak diubah** (impor parsial). Kegagalan sistem → rollback seluruh batch → status riwayat **Gagal**.
- **Round-trip:** judul kolom export identik dengan template import.

# YANG HARUS KAMU LAKUKAN SEKARANG — HANYA FASE L0

Fase L0 = **Sinkronisasi & Audit Kesesuaian — TANPA mengubah satu pun file kode, migrasi, konfigurasi, atau data.**

### Langkah 0 — Tanyakan dulu padaku (sebelum menyentuh apa pun)
Kirim pertanyaan bernomor berikut (boleh kamu tambah bila perlu):
1. Bolehkah kamu menjalankan `php artisan test`? Memakai database test terpisah / `.env.testing`? Database mana yang aman dipakai agar data development-ku tidak terhapus?
2. Apakah proyek ini memakai Git? Apa kebijakan commit-nya (boleh commit lokal? per fase? format pesan?).
3. Apakah ada aturan/gaya kode tertentu yang belum tertulis (mis. cara membuat test, penamaan route, struktur folder komponen)?
4. Apakah ada bagian dari `README.md` yang perlu kamu ketahui sudah *kusetujui* sebagai perubahan dari desain awal? (kalau aku tidak tahu, kamu catat sebagai "tidak diketahui").
Tunggu jawabanku.

### Langkah 1 — Baca & jelajahi (read-only)
Selesaikan **URUTAN BACAAN WAJIB** di atas. Jelajahi repositori secara menyeluruh.

### Langkah 2 — Baseline test
Jalankan `php artisan test` **sekali** (setelah jawabanku di Langkah 0) dan catat: jumlah test, assertion, kegagalan/peringatan, waktu. Bandingkan dengan klaim README (82 test, 382 assertion).

### Langkah 3 — Laporan kesesuaian
Buat **tabel** untuk **setiap** dari 13 butir di `PRD.md` §9, ditambah temuan barumu. Kolom:
`# | Topik | Desain/PRD awal | Yang ada di kode (jelaskan singkat) | Bukti (file:baris / route / migrasi) | Dugaan status (kemungkinan disetujui / kemungkinan menyimpang / tidak diketahui) | Rekomendasimu (pertahankan / kembalikan ke desain / perbarui dokumen) + alasan`.
Verifikasi dengan **membaca kode**, bukan hanya mengandalkan README. Jika README dan kode berbeda, laporkan keduanya.

### Langkah 4 — Catatan teknis untuk fase berikutnya
Sajikan temuan berikut (fakta, bukan asumsi):
- Versi PHP, Laravel (`composer show laravel/framework`), Node; ekstensi PHP aktif (terutama yang relevan untuk memproses Excel: mis. zip, xml, gd, mbstring — cek apa yang disyaratkan library kandidat).
- Isi `composer.json` & `package.json` (paket terpasang, termasuk apakah sudah ada library Excel).
- Definisi **nyata** nilai Status Alat dan nilai Kategori/Status Jadwal di migrasi (apakah ENUM, nilainya apa — bandingkan dengan istilah di prototipe: Aktif / Tidak Aktif / Bermasalah; Selesai / Menunggu-Progres / Kendala).
- Kolom **nyata** tabel `alat`, `lokasi`, `jadwal`, `audit_log` (termasuk kolom tambahan seperti foto/koordinat/status lapangan bila ada) dan nilai `modul`/`aksi` yang diizinkan di `audit_log`.
- **Jendela bulan** Gantt yang benar-benar dipakai di kode (README: Jan 2026 – Jul 2027; prototipe: Jun-26 – Des-27) dan di mana nilainya didefinisikan.
- Cara kerja otorisasi admin & permission modul (middleware/policy/gate), serta bagaimana Audit Log live dicatat (observer? service? di controller?).
- Komponen Blade/Alpine yang bisa dipakai ulang (toast, modal, chip status, tabel, kartu KPI, layout).
- Struktur & gaya folder `tests/` (cara membuat user admin/pekerja di test, helper, factory).
- Status Git (jika ada): branch aktif, ada perubahan belum di-commit atau tidak (**hanya dicatat**).

### Langkah 5 — Format laporan L0 (kirim satu laporan terstruktur)
1. Ringkasan satu paragraf kondisi repositori.
2. Hasil baseline test.
3. Tabel kesesuaian (Langkah 3).
4. Catatan teknis (Langkah 4).
5. **Daftar keputusan yang kubutuhkan darimu** (bernomor, tiap butir dengan opsi + rekomendasi): termasuk butir 1–11 di `PRD.md` §10 yang sudah bisa kamu jawab usulannya dari temuan (mis. jendela bulan, nilai status yang sah, library kandidat beserta syarat ekstensi, skema tabel riwayat yang kamu usulkan **sebagai rancangan saja**, belum dibuat).
6. Risiko yang kamu lihat.
7. Penutup: "Menunggu keputusan & izin untuk lanjut ke Fase L1."

### Setelah itu: BERHENTI
Jangan memulai Fase L1. Jangan membuat migrasi, mengubah file, memasang paket, atau menjalankan perintah selain yang bersifat baca (dan `php artisan test` yang sudah kuizinkan).

# CATATAN LINGKUNGAN
- Lingkungan lokal: Windows + XAMPP (Apache/MySQL), database dev `pertamina_rig_dashboard`, server `php artisan serve`, aset `npm run dev` / `npm run build`.
- Bahasa antarmuka aplikasi: Indonesia. Bahasa komunikasi denganku: Indonesia.
- Jika sebuah perintah/perilaku di lingkungan ini tidak kamu yakini aman bagi data, **berhenti dan tanya**.

# GAYA KOMUNIKASI YANG KUHARAPKAN
- Ringkas dan terstruktur; jangan membanjiri dengan detail implementasi yang belum relevan.
- Kalau ada opsi, beri maksimal 2–3 opsi + satu rekomendasi disertai alasan singkat.
- Jika ada hal yang kamu tidak tahu, katakan terus terang daripada menebak.

Mulailah dengan **Langkah 0 (pertanyaan)** sekarang.

---
