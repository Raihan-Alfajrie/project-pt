# DESAIN.md — Referensi Desain (Versi 2: "Tahapan Pengembangan Lanjutan")

> **ATURAN WAJIB:** Sebelum menulis kode, buka dan baca seluruh file di folder **`desainku/`** —
> sekarang berisi **12 file HTML prototipe** (11 lama + 1 baru: Import / Export).
> Prototipe adalah sumber kebenaran visual & perilaku. Jangan menebak dari deskripsi teks —
> buka file HTML-nya, lihat struktur DOM, class CSS, dan JavaScript-nya.
>
> Dokumen ini menggantikan versi tahap 1 (arsip di `arsip_tahap_1/`).

## 1. Isi folder `desainku/` (12 file)

| File (judul tab browser) | Route | Status implementasi |
|---|---|---|
| `Home — Pertamina Rig Monitoring.html` | `/` | ✅ tahap 1 |
| `About — Pertamina Admin Panel.html` | `/about` | ✅ tahap 1 |
| `Dashboard — Pertamina Rig Monitoring.html` | `/dashboard` | ✅ tahap 1 |
| `Alat — Pertamina Admin Panel.html` | `/alat` | ✅ tahap 1 |
| `Lokasi — Pertamina Admin Panel.html` | `/lokasi` | ✅ tahap 1 |
| `Jadwal — Pertamina Admin Panel.html` | `/jadwal` | ✅ tahap 1 |
| `Manajemen User — Pertamina Admin Panel.html` | `/users` | ✅ tahap 1 |
| `Audit Log — Pertamina Admin Panel.html` | `/audit-log` | ✅ tahap 1 |
| `Dokumentasi — Pertamina Admin Panel.html` | `/dokumentasi` | ✅ tahap 1 |
| `FAQ — Pertamina Admin Panel.html` | `/faq` | ✅ tahap 1 (lihat §8, ada perbedaan) |
| `Profil — Pertamina Admin Panel.html` | `/profil` | ✅ tahap 1 |
| **`Import / Export — Pertamina Admin Panel.html`** | **`/import-export`** | 🆕 **tahap lanjutan** |

> Catatan: file lama di folder ini adalah desain **awal**. Beberapa halaman di aplikasi sekarang
> sudah berbeda dari file itu (§8). Perbedaan tersebut harus diselesaikan lewat Fase L0, bukan diubah diam-diam.

Semua file **self-contained** (HTML+CSS+JS, data dummy di `localStorage`). Tugas agent: memindahkan **tampilan & interaksi**
ke Blade + Alpine.js dan mengganti sumber data ke database lewat Controller/Model Laravel.

## 2. Sistem Desain (tidak berubah)

```css
--red:#ea2027; --red-strong:#c8141b; --red-soft:rgba(234,32,39,.16);
--bg:#212326; --panel:#3b2226; --panel-2:#492226; --panel-3:#54282d;
--border:rgba(234,32,39,.28); --border-soft:rgba(255,255,255,.08);
--text:#f6f5f4; --text-dim:rgba(246,245,244,.66); --text-faint:rgba(246,245,244,.4);
--green:#4ade80; --amber:#f0a93b; --blue:#7ab4f5;   /* dipakai chip status & pratinjau import */
```
- Font: `Space Grotesk` (judul), `Inter` (isi), `IBM Plex Mono` (angka/tanggal/kode).
- Radius: 14px (panel/kartu), 9–12px (tombol/input/kartu kecil). Mode terang via `prefers-color-scheme: light` (token berubah, struktur sama).
- Layout internal: topbar merah + sidebar merah 264px + area konten. Layout publik (Home/About) terpisah.
- Komponen berulang jadikan Blade component: KPI card, toolbar, tabel + kolom aksi, modal (Form/Detail/Confirm), status chip, toast.

## 3. Struktur Database

Baseline: `pertamina_rig_dashboard.sql` (11 tabel) — **namun aplikasi sekarang memiliki 12 tabel** (`faqs`) dan mungkin kolom tambahan
(foto, dsb.). **Sumber kebenaran skema saat ini = migrasi di repositori**, bukan file SQL lama.
Perubahan skema baru: lewat migrasi baru, dan **dikonfirmasi dulu ke user** (lihat `AGENTS.md`).

## 4. Sidebar (tahap lanjutan)

Grup **Manajemen Data**: Alat · Lokasi · Jadwal · **Import / Export** (baru, hanya tampil untuk Administrator).
Ikon: panah turun ke baki — `<path d="M12 3v12M8 11l4 4 4-4"/><path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>`.
Topbar halaman ini: judul "Import / Export" dengan lencana kecil "Khusus Administrator", deskripsi "Unduh & unggah data dalam format Excel".

## 5. Halaman Import / Export — Spesifikasi Tampilan & Perilaku

Satu kolom konten (lebar maks ±1200px), **tiga panel bertumpuk** (`.panel`: border `--border`, radius 14, header + body).

### 5.1 Panel 1 — Export Data
- Header: ikon unggah, judul "Export Data", subjudul "Unduh data ke file Excel (.xlsx) atau CSV".
- Label bagian: **"1. Pilih data"** → grid 4 kartu modul (Alat, Lokasi, Jadwal, Audit Log); kartu terpilih: border & ring merah, ikon latar `--red-soft`. Tiap kartu: ikon, nama, "N baris".
- Label **"2. Filter & format"** → baris filter dinamis (dropdown "Semua" + opsi; lihat PRD §5.1 FR-EX-02) + segmented control **.xlsx | .csv** (default .xlsx).
- Footer panel (garis putus-putus di atas): kiri teks hitungan `<b>N</b> baris akan diekspor dari <Modul>`; kanan tombol merah **Unduh** (nonaktif bila N=0).
- Mengganti modul mereset filter. Mengubah filter memperbarui hitungan seketika.

### 5.2 Panel 2 — Import Data
- Header: ikon, "Import Data", subjudul "Unggah file Excel, periksa hasil validasi, lalu konfirmasi" + **stepper** di kanan (3 langkah: Pilih modul & template → Unggah file → Pratinjau & konfirmasi; langkah selesai = lingkaran hijau ✓, aktif = merah, sisanya abu).
  Saat belum ada file: langkah 1 selesai, langkah 2 aktif. Setelah file terbaca: langkah 3 aktif.
- Dua kartu berdampingan (grid 1 : 1.2, satu kolom di layar sempit):
  - **Langkah 1 — Pilih modul & template**: segmented Alat | Lokasi | Jadwal; "Kolom di file" berupa chip (wajib berwarna amber + tanda `*`, tooltip = keterangan); catatan "Kolom bertanda * wajib diisi. Urutan kolom bebas, nama kolom tidak peka huruf besar/kecil."; tombol ghost **Unduh template Excel**.
  - **Langkah 2 — Unggah file**: dropzone (border 2px putus-putus; hover/drag → latar `--red-soft` & border merah), ikon unggah, teks "Tarik file ke sini, atau klik untuk memilih", sub "Format .xlsx, .xls, atau .csv" (sesuaikan dengan keputusan §10 PRD). Area di bawahnya untuk alert error (latar merah transparan, teks merah muda).
- **Langkah 3 (muncul setelah file terbaca; dipisah garis putus-putus)**:
  - Judul bagian "Langkah 3 — Pratinjau & konfirmasi" + nama file (mono).
  - Baris chip ringkasan: **Total · Baru (titik hijau) · Perbarui (biru) · Dilewati (abu) · Error (merah)**, angka besar Space Grotesk.
  - "Jika data sudah ada (berdasarkan <kunci modul>)": dua opsi radio-kartu (mode skip / update), terpilih = border merah + titik radio merah.
  - Tabel pratinjau (scroll maks ±380px, header lengket): kolom **Baris · Status · kolom kunci modul · Pesan**. Kolom kunci: Alat (Nama, Kategori, Merek, No. Seri, Status) · Lokasi (Kode, Nama, Provinsi, Kota) · Jadwal (Rig, Nama Sumur/Pekerjaan, Kategori, Bulan Mulai, Durasi, Status). Catatan di bawah bila baris > 500.
  - Pesan per status: Baru "OK, akan ditambahkan" · Perbarui "Sudah ada — akan diperbarui" · Dilewati "Sudah ada — dilewati" · Error = daftar pesan dipisah `; ` (teks merah muda).
  - Baris aksi: kiri ghost **Unduh laporan error** (nonaktif jika 0 error); kanan ghost **Batal** + tombol merah **Konfirmasi Import (N baris)** (nonaktif bila N=0, label menjadi "Tidak ada baris yang bisa diimpor").
- Ganti modul / Batal → menghapus file & pratinjau dan kembali ke langkah 2.

### 5.3 Panel 3 — Riwayat Import / Export
- Header: ikon jam, "Riwayat Import / Export", subjudul "Catatan setiap unduhan dan unggahan data".
- Tabel: Waktu (mono) · User · Jenis (pil: Import biru, Export ungu) · Modul · Nama File (mono) · Baris · Status (pil: Sukses hijau, Sebagian amber, Gagal merah). Kosong → "Belum ada riwayat import / export."
- ⚠️ Tombol **"Reset data contoh (prototipe)"** di header panel ini **HANYA ada di prototipe** — jangan diimplementasikan.

### 5.4 Pil status pratinjau
Baru = hijau `#4ade80` di latar hijau 16%; Perbarui = biru `#7ab4f5`; Dilewati = abu (`--text-faint` di `--panel-3`); Error = merah `#ff6b6f` di latar merah 18%. Bentuk: pil dengan titik kecil di kiri.

### 5.5 Teks pesan (pakai persis, bahasa Indonesia)
- Kolom wajib hilang: "Kolom wajib tidak ditemukan: **<daftar>**. Unduh template di Langkah 1 untuk melihat nama kolom yang benar."
- File kosong: "File kosong atau tidak ada baris data di bawah baris judul kolom."
- File rusak: "File tidak bisa dibaca sebagai Excel/CSV. Pastikan formatnya .xlsx, .xls, atau .csv."
- Sukses import: "Import selesai: X ditambahkan, Y diperbarui, Z baris error dilewati."
- Pesan validasi baris (contoh): "<Kolom> wajib diisi" · "Kategori harus "Rig" atau "Alat Pendukung"" · "Status harus Aktif / Tidak Aktif / Bermasalah" · "Tahun perolehan harus angka 1980–2026" · "Provinsi tidak dikenali: <nilai>" · "Rig tidak ditemukan di data Alat" · "Kategori tidak dikenal: <nilai>" · "Format Bulan Mulai tidak dikenali (pakai 2026-06 atau Jun-26)" · "Bulan Mulai di luar rentang <awal> s/d <akhir>" · "Durasi harus bilangan bulat 1–19" · "Durasi melewati <akhir jendela>" · "Duplikat di dalam file (sama dengan baris N)".
  (Angka 1–19 dan rentang bulan mengikuti keputusan jendela Gantt — PRD §10 butir 4.)

### 5.6 Toast
Notifikasi mengambang di tengah bawah, hilang otomatis ±3,6 detik; varian sukses (border hijau) dan error (border merah). Pola toast sudah dipakai di aplikasi — pakai komponen yang ada bila tersedia.

### 5.7 Perilaku yang BUKAN bagian aplikasi
Penyimpanan `localStorage`, data contoh, tombol reset, dan mekanisme konfirmasi unduhan platform pratinjau hanyalah alat prototipe.
Di aplikasi nyata: data dari database, unduhan lewat response HTTP biasa.

## 6. Pemetaan ke Aplikasi (saran struktur, tetap konfirmasi ke user)

- Satu halaman Blade `import-export` dengan state Alpine.js untuk stepper, modul terpilih, mode, pratinjau; endpoint terpisah untuk: hitung jumlah (export count), unduh export, unduh template, unggah & validasi (mengembalikan JSON pratinjau), konfirmasi import, unduh laporan error, daftar riwayat.
- Logika validasi & parsing di kelas Service/Import terpisah per modul (satu kelas per modul, mudah dites), bukan di controller.
- Pemetaan alias judul kolom, normalisasi nilai (kategori, status, bulan, provinsi) harus mereproduksi perilaku `MODS[...]` di JS prototipe — baca fungsi `parseRows`, `clean`, `parseBulan`, `classify` di file HTML prototipe.

## 7. Struktur Layout & Komponen yang Berulang
Tidak berubah dari tahap 1: satu layout internal (`layouts/app`) + satu layout publik (`layouts/guest`); KPI card, toolbar, tabel aksi, modal 3 varian, status chip, Gantt grid CSS. Gunakan komponen Blade yang **sudah ada di repositori** — jangan membuat duplikat.

## 8. Perbedaan Desain Awal vs Aplikasi Sekarang (dicatat untuk Fase L0)

Berdasarkan `README.md` tahap 1 (lihat tabel lengkap di `PRD.md` §9): peta Leaflet (desain: SVG), upload foto Alat & Profil (desain: placeholder),
Audit Log live, FAQ dinamis (desain: Coming Soon), KPI Dashboard/Alat/Jadwal berbeda dari prototipe, jendela Gantt berbeda,
hak akses "granular", Dokumentasi eksklusif Administrator, dsb.
**Aturannya:** jangan menganggap file HTML lama otomatis benar *dan* jangan menganggap implementasi sekarang otomatis benar.
Agent melaporkan, user memutuskan, baru ada perubahan.

## 9. Batasan Desain (jangan dilanggar)
- Warna, font, layout, komponen final. Jangan "memodernisasi".
- Jangan menghapus/menyederhanakan komponen prototipe tanpa bertanya.
- Bila ada bagian sulit direplikasi 1:1 → tanyakan dulu sebelum menyederhanakan.
- Fitur di luar prototipe → hanya dengan izin eksplisit user.
