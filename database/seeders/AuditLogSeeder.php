<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->keyBy('username');

        $logs = [
            ['un' => 'budi.santoso', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-29 09:12:00', 'ket' => 'Login ke sistem'],
            ['un' => 'budi.santoso', 'aksi' => 'edit', 'modul' => 'jadwal', 'waktu' => '2026-09-29 09:20:00', 'ket' => 'Mengedit status item jadwal BKB-AR.012 menjadi Kendala'],
            ['un' => 'dewi.lestari', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-29 09:05:00', 'ket' => 'Login ke sistem'],
            ['un' => 'dewi.lestari', 'aksi' => 'tambah', 'modul' => 'lokasi', 'waktu' => '2026-09-29 09:31:00', 'ket' => 'Menambahkan kode lapangan baru: KRG-PJ1'],
            ['un' => 'agus.wijaya', 'aksi' => 'tambah', 'modul' => 'alat', 'waktu' => '2026-09-28 16:44:00', 'ket' => 'Menambahkan alat baru: Compressor Udara Atlas Copco XATS 900'],
            ['un' => 'agus.wijaya', 'aksi' => 'edit', 'modul' => 'alat', 'waktu' => '2026-09-28 16:50:00', 'ket' => 'Mengedit status BOP Stack Cameron menjadi Aktif'],
            ['un' => 'rina.kartika', 'aksi' => 'edit', 'modul' => 'jadwal', 'waktu' => '2026-09-28 14:22:00', 'ket' => 'Mengubah durasi pekerjaan KRG-PK8 menjadi 2 bulan'],
            ['un' => 'rina.kartika', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-28 14:02:00', 'ket' => 'Login ke sistem'],
            ['un' => 'siti.rahayu', 'aksi' => 'tambah', 'modul' => 'user', 'waktu' => '2026-09-27 20:15:00', 'ket' => 'Menambahkan akun user baru: Maya Anggraini (@maya.anggraini)'],
            ['un' => 'siti.rahayu', 'aksi' => 'edit', 'modul' => 'user', 'waktu' => '2026-09-27 20:22:00', 'ket' => 'Mengatur hak akses Fajar Ramadhan: Kelola Alat diaktifkan'],
            ['un' => 'siti.rahayu', 'aksi' => 'logout', 'modul' => 'sistem', 'waktu' => '2026-09-27 20:40:00', 'ket' => 'Logout dari sistem'],
            ['un' => 'yusuf.hidayat', 'aksi' => 'edit', 'modul' => 'jadwal', 'waktu' => '2026-09-27 11:08:00', 'ket' => 'Mengubah status TAP-NW1 menjadi Selesai'],
            ['un' => 'yusuf.hidayat', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-27 10:55:00', 'ket' => 'Login ke sistem'],
            ['un' => 'putri.ayu', 'aksi' => 'edit', 'modul' => 'lokasi', 'waktu' => '2026-09-26 15:37:00', 'ket' => 'Melengkapi alamat kota/kecamatan lapangan BEL'],
            ['un' => 'putri.ayu', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-26 15:20:00', 'ket' => 'Login ke sistem'],
            ['un' => 'hendra.gunawan', 'aksi' => 'hapus', 'modul' => 'alat', 'waktu' => '2026-09-26 09:48:00', 'ket' => 'Menghapus data alat: Water Truck Hino FM 260 JD (rusak permanen, sudah dipensiunkan)'],
            ['un' => 'hendra.gunawan', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-26 09:30:00', 'ket' => 'Login ke sistem'],
            ['un' => 'fajar.ramadhan', 'aksi' => 'edit', 'modul' => 'alat', 'waktu' => '2026-09-25 13:12:00', 'ket' => 'Mengedit keterangan Genset Cummins 500kVA'],
            ['un' => 'fajar.ramadhan', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-25 12:58:00', 'ket' => 'Login ke sistem'],
            ['un' => 'budi.santoso', 'aksi' => 'hapus', 'modul' => 'jadwal', 'waktu' => '2026-09-24 17:05:00', 'ket' => 'Menghapus item jadwal duplikat: KRG-PN3 (CF 2025)'],
            ['un' => 'budi.santoso', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-24 08:55:00', 'ket' => 'Login ke sistem'],
            ['un' => 'dewi.lestari', 'aksi' => 'logout', 'modul' => 'sistem', 'waktu' => '2026-09-23 19:41:00', 'ket' => 'Logout dari sistem'],
            ['un' => 'dewi.lestari', 'aksi' => 'tambah', 'modul' => 'jadwal', 'waktu' => '2026-09-23 16:18:00', 'ket' => 'Menambahkan item jadwal baru: KRG-PK5 (CF 2027)'],
            ['un' => 'siti.rahayu', 'aksi' => 'edit', 'modul' => 'user', 'waktu' => '2026-09-22 10:27:00', 'ket' => 'Mengubah jabatan Yusuf Hidayat dari Pekerja tetap sebagai Pekerja (verifikasi ulang)'],
            ['un' => 'maya.anggraini', 'aksi' => 'login', 'modul' => 'sistem', 'waktu' => '2026-09-21 08:40:00', 'ket' => 'Login pertama kali ke sistem'],
        ];

        foreach ($logs as $i => $item) {
            $user = $users->get($item['un']);
            AuditLog::updateOrCreate(
                ['id' => $i + 1],
                [
                    'user_id' => $user?->id,
                    'username_snapshot' => $item['un'],
                    'email_snapshot' => $user?->email ?? "{$item['un']}@gmail.com",
                    'jabatan_snapshot' => $user?->jabatan ?? 'pekerja',
                    'aksi' => $item['aksi'],
                    'modul' => $item['modul'],
                    'keterangan' => $item['ket'],
                    'waktu' => $item['waktu'],
                ]
            );
        }
    }
}
