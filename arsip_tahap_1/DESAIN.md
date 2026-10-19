# DESAIN.md — Referensi Desain (Wajib Dibaca Sebelum Coding)

> **ATURAN WAJIB:** Sebelum menulis satu baris kode pun, buka dan baca seluruh file
> di folder **`desainku/`** — folder ini berisi 11 file HTML statis yang merupakan
> **prototipe final dan sumber kebenaran visual** untuk aplikasi ini. Jangan menebak
> tampilan dari deskripsi teks di bawah saja — buka file HTML-nya langsung, lihat
> struktur DOM, class CSS, dan JavaScript-nya, lalu konversi jadi Blade view.

## 1. Isi folder `desainku/`

11 file HTML (buka di browser & baca source-nya satu per satu):

| File (judul tab browser) | Menu / Route tujuan |
|---|---|
| `Home — Pertamina Rig Monitoring.html` | Landing page publik `/` |
| `About — Pertamina Admin Panel.html` | Halaman publik `/about` |
| `Dashboard — Pertamina Rig Monitoring.html` | `/dashboard` (setelah login) |
| `Alat — Pertamina Admin Panel.html` | `/alat` |
| `Lokasi — Pertamina Admin Panel.html` | `/lokasi` |
| `Jadwal — Pertamina Admin Panel.html` | `/jadwal` |
| `Manajemen User — Pertamina Admin Panel.html` | `/users` |
| `Audit Log — Pertamina Admin Panel.html` | `/audit-log` |
| `Dokumentasi — Pertamina Admin Panel.html` | `/dokumentasi` |
| `FAQ — Pertamina Admin Panel.html` | `/faq` |
| `Profil — Pertamina Admin Panel.html` | `/profil` |

Setiap file **self-contained** (HTML+CSS+JS dalam satu file, memakai `localStorage`
sebagai data dummy). Tugas agent adalah memindahkan **tampilan & interaksinya**
ke Blade + Alpine.js, dan mengganti sumber datanya dari `localStorage` ke database
MySQL lewat Controller/Model Laravel — bukan menyalin mentah `localStorage`-nya.

## 2. Sistem Desain (Design Tokens)

Ambil persis dari `<style>` tiap file HTML. Ringkasannya:

**Warna utama**
```css
--red:#ea2027; --red-strong:#c8141b; --red-soft:rgba(234,32,39,.16);
--bg:#212326; --panel:#3b2226; --panel-2:#492226; --panel-3:#54282d;
--border:rgba(234,32,39,.28); --border-soft:rgba(255,255,255,.08);
--text:#f6f5f4; --text-dim:rgba(246,245,244,.66); --text-faint:rgba(246,245,244,.4);
```
Halaman publik (Home/About) pakai palet lebih gelap netral (`--bg:#151113` dst) — lihat file aslinya.

Mendukung light mode via `@media (prefers-color-scheme: light)` — token warna berubah,
struktur tetap sama. Pertahankan pola ini.

**Tipografi**: `Space Grotesk` (judul/heading), `Inter` (body), `IBM Plex Mono` (angka/tanggal/kode).
Di-load dari Google Fonts persis seperti di `<head>` tiap file.

**Radius & spacing**: `--radius:14px` untuk card/panel, `9-10px` untuk tombol/input.

## 3. Struktur Layout Baku

Semua halaman **internal** (Dashboard, Alat, Lokasi, Jadwal, Manajemen User, Audit Log,
Dokumentasi, FAQ, Profil) memakai layout yang **identik**:

- **Topbar** merah solid (`--red`), berisi: logo block (kiri, lebar tetap 264px),
  judul halaman + ikon, lalu di kanan: tombol Home/About/Dashboard, ikon notifikasi, jam digital.
- **Sidebar** merah solid, lebar 264px, sticky, isi grup menu (Utama / Manajemen Data /
  System & Security / Support) + info user di bagian bawah (avatar inisial, klik → Profil).
- **Content area** background `--bg` gelap, panel-panel dengan background `--panel`.

Halaman **publik** (Home, About) pakai layout beda: topbar transparan/blur tanpa sidebar,
tombol Masuk/Daftar di kanan atas. Jangan disamakan dengan layout internal.

Jadikan layout ini **satu Blade layout/component** (`layouts/app.blade.php` untuk internal,
`layouts/guest.blade.php` untuk publik) dipakai semua halaman terkait — jangan duplikasi markup topbar/sidebar di tiap view.

## 4. Komponen yang Berulang (jadikan Blade component/partial)

- **KPI card** (kartu angka + label + ikon) — dipakai di Dashboard, Alat, Manajemen User, Jadwal.
- **Toolbar** (search + filter dropdown + tombol tambah) — dipakai di semua halaman CRUD.
- **Tabel data** dengan kolom Aksi (ikon lihat/edit/hapus) — pola sama di semua CRUD.
- **Modal** (overlay + modal box): 3 varian — Form (tambah/edit), Detail (read-only), Confirm (hapus).
  Struktur HTML & class CSS-nya identik di semua file, tinggal isi kontennya beda.
- **Status chip** (pill kecil berwarna: aktif/hijau, standby-progres/amber, bermasalah-kendala/merah).
- **Gantt chart** (di Dashboard & Jadwal) — grid CSS custom, bukan library eksternal. Lihat
  struktur `.gantt`, `.g-row`, `.block` di file `Jadwal` untuk detail penuh sebelum reimplementasi.

## 5. Struktur Database

Gunakan **`pertamina_rig_dashboard.sql`** (disertakan terpisah) sebagai baseline skema —
berisi 11 tabel: `lokasi`, `alat`, `users`, `user_permissions`, `user_sessions`,
`connected_apps`, `dokumentasi_panel`, `dokumentasi_item`, `jadwal`, `audit_log`, `target_rk`.

Konversi ke Laravel migration **mengikuti struktur ini** (nama tabel, kolom, tipe data,
foreign key, ENUM values) — jangan mengubah nama kolom/tabel secara sepihak. Kalau
menurut agent ada yang perlu disesuaikan untuk kebutuhan Eloquent/Laravel convention
(misal `id` → tetap, tambah `remember_token` untuk Breeze di tabel `users`), **tanyakan
dulu ke user sebelum mengubah**, jangan langsung ubah dan lanjut.

Catatan penting yang sudah didokumentasikan di file SQL (baca komentar di dalamnya):
- `jadwal.kategori_id` adalah foreign key ke `dokumentasi_item`, bukan ENUM tetap.
- `users.password_hash` di seed data masih placeholder — ganti proses register/seeder
  supaya pakai `Hash::make()` Laravel yang sesungguhnya.
- `users.status` (online/offline) idealnya diturunkan dari `user_sessions` aktif —
  ini keputusan desain yang masih terbuka, techarch boleh pilih cache column atau
  computed, tapi **informasikan pilihannya ke user**, jangan diam-diam pilih sendiri.

## 6. Data Seed

File SQL sudah berisi `INSERT` untuk semua tabel (14 lokasi, 24 alat, 10 user, 32 item
jadwal, 16 item dokumentasi, 24 audit log, 3 target_rk) — data ini **konsisten** dengan
apa yang tampil di prototipe HTML (nama rig, nama user, dst identik). Pakai data ini
sebagai seeder Laravel (`database/seeders/`), jangan bikin data dummy baru yang beda.

## 7. Batasan Desain (Jangan Dilanggar)

- Jangan mengganti skema warna, font, atau struktur layout dengan alasan "lebih modern"
  atau preferensi pribadi agent — desain ini sudah final dan disetujui.
  Lihat `AGENTS.md` pasal soal larangan menambah/mengubah fitur di luar desain.
- Jangan menghapus/menyederhanakan komponen yang ada di prototipe (mis. menghilangkan
  modal Detail karena "dianggap redundan") tanpa bertanya dulu.
- Kalau ada halaman yang secara teknis sulit direplikasi 1:1 di Blade (mis. bagian SVG
  peta di Lokasi), agent WAJIB bertanya ke user dulu sebelum menyederhanakan.
