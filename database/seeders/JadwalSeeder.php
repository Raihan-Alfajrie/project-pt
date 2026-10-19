<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jadwals = [
            ['id' => 1, 'alat_id' => 1, 'lokasi_id' => 1, 'kategori_id' => 10, 'label' => 'Pekerjaan WI Zona 4', 'status' => 'progres', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 6, 'catatan' => 'Menyelesaikan pekerjaan WI sebelum lanjut ke sumur eksplorasi PHE Jambi Merang.'],
            ['id' => 2, 'alat_id' => 1, 'lokasi_id' => 1, 'kategori_id' => 11, 'label' => 'MJ-A21 (CF 2026)', 'status' => 'progres', 'periode_mulai' => '2027-03-01', 'durasi_bulan' => 2, 'catatan' => 'Est. tajak M3 Desember 2026, on progress persiapan tender rig.'],
            ['id' => 3, 'alat_id' => 2, 'lokasi_id' => 1, 'kategori_id' => 1, 'label' => 'MJ-A22 29.07-IP', 'status' => 'selesai', 'periode_mulai' => '2026-07-01', 'durasi_bulan' => 1, 'catatan' => 'IP 29 Juli 2026 — completed sesuai plan.'],
            ['id' => 4, 'alat_id' => 2, 'lokasi_id' => 1, 'kategori_id' => 3, 'label' => 'MJ-A9 (CF 2026)', 'status' => 'progres', 'periode_mulai' => '2027-02-01', 'durasi_bulan' => 2, 'catatan' => 'CF 2025→2026, konstruksi lokasi berjalan.'],
            ['id' => 5, 'alat_id' => 3, 'lokasi_id' => 2, 'kategori_id' => 11, 'label' => 'ROSELLA SHALLOW SOUTH (CO)', 'status' => 'progres', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 3, 'catatan' => 'Change Order, sumur eksplorasi.'],
            ['id' => 6, 'alat_id' => 3, 'lokasi_id' => 3, 'kategori_id' => 6, 'label' => 'TAP-NW1 (CF 2025,2026)', 'status' => 'progres', 'periode_mulai' => '2027-02-01', 'durasi_bulan' => 2, 'catatan' => 'Finalisasi KJPP tanam tumbuh.'],
            ['id' => 7, 'alat_id' => 4, 'lokasi_id' => null, 'kategori_id' => 12, 'label' => 'Kontrak Habis', 'status' => 'kendala', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 2, 'catatan' => 'Kontrak rig berakhir, perlu proses re-tender sebelum lanjut sumur berikutnya.'],
            ['id' => 8, 'alat_id' => 5, 'lokasi_id' => null, 'kategori_id' => 8, 'label' => 'Proses Pengadaan Rig — Inplace Jun 2026', 'status' => 'kendala', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 5, 'catatan' => 'On progress persiapan tender, rencana beroperasi di Zona 1 akhir tahun; MJ-OS2 dikaji rig lain sementara.'],
            ['id' => 9, 'alat_id' => 5, 'lokasi_id' => 1, 'kategori_id' => 10, 'label' => 'MJ-OS2 (CF 2025)', 'status' => 'progres', 'periode_mulai' => '2026-12-01', 'durasi_bulan' => 1, 'catatan' => 'Sudah dihindari lokasi sengketa, est. penyampaian nilai JKPP M4 Agustus 2026.'],
            ['id' => 10, 'alat_id' => 6, 'lokasi_id' => 4, 'kategori_id' => 3, 'label' => 'BKB-AR.016 (CF 2025)', 'status' => 'progres', 'periode_mulai' => '2026-07-01', 'durasi_bulan' => 2, 'catatan' => 'Est. selesai konstruksi M4 September 2026.'],
            ['id' => 11, 'alat_id' => 6, 'lokasi_id' => 4, 'kategori_id' => 5, 'label' => 'BKB-AR.012 (CF 2025,2026)', 'status' => 'kendala', 'periode_mulai' => '2026-10-01', 'durasi_bulan' => 2, 'catatan' => 'Masih negosiasi nilai tanam tumbuh lahan sawah, perlu konstruksi jembatan.'],
            ['id' => 12, 'alat_id' => 7, 'lokasi_id' => 5, 'kategori_id' => 1, 'label' => 'BEL-K9 19.06-IP', 'status' => 'selesai', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 1, 'catatan' => 'Est. release 5 Agustus 2026.'],
            ['id' => 13, 'alat_id' => 7, 'lokasi_id' => 5, 'kategori_id' => 1, 'label' => 'BEL-K8', 'status' => 'selesai', 'periode_mulai' => '2026-07-01', 'durasi_bulan' => 1, 'catatan' => 'Sudah tajak.'],
            ['id' => 14, 'alat_id' => 7, 'lokasi_id' => 6, 'kategori_id' => 3, 'label' => 'KRG-PN2 / PN4', 'status' => 'progres', 'periode_mulai' => '2026-08-01', 'durasi_bulan' => 2, 'catatan' => 'Konstruksi lokasi berjalan.'],
            ['id' => 15, 'alat_id' => 8, 'lokasi_id' => 4, 'kategori_id' => 3, 'label' => 'Konstruksi (Penggantian Tanam Tumbuh)', 'status' => 'kendala', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 2, 'catatan' => 'Menunggu penyelesaian isu tanam tumbuh sebelum lanjut konstruksi.'],
            ['id' => 16, 'alat_id' => 8, 'lokasi_id' => 4, 'kategori_id' => 1, 'label' => 'BKB-AR.008', 'status' => 'selesai', 'periode_mulai' => '2026-09-01', 'durasi_bulan' => 1, 'catatan' => 'Sudah tajak.'],
            ['id' => 17, 'alat_id' => 9, 'lokasi_id' => 6, 'kategori_id' => 1, 'label' => 'KRG-PK2 20.05-IP', 'status' => 'selesai', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 2, 'catatan' => 'IP tercapai sesuai jadwal.'],
            ['id' => 18, 'alat_id' => 9, 'lokasi_id' => 6, 'kategori_id' => 7, 'label' => 'Amandemen PJWK', 'status' => 'progres', 'periode_mulai' => '2026-09-01', 'durasi_bulan' => 1, 'catatan' => 'Proses administrasi amandemen kontrak.'],
            ['id' => 19, 'alat_id' => 10, 'lokasi_id' => 6, 'kategori_id' => 3, 'label' => 'KRG-PB8', 'status' => 'progres', 'periode_mulai' => '2026-07-01', 'durasi_bulan' => 1, 'catatan' => 'Konstruksi lokasi berjalan.'],
            ['id' => 20, 'alat_id' => 10, 'lokasi_id' => 5, 'kategori_id' => 10, 'label' => 'BEL-K10', 'status' => 'progres', 'periode_mulai' => '2026-10-01', 'durasi_bulan' => 2, 'catatan' => 'Pembayaran tanam tumbuh lokasi existing masih menunggu memo lanjut konstruksi tahap 1.'],
            ['id' => 21, 'alat_id' => 11, 'lokasi_id' => null, 'kategori_id' => 8, 'label' => 'Proses Pengadaan Rig (Re-Tender) — Est. Agustus 2026', 'status' => 'kendala', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 5, 'catatan' => 'Rig acceptance menyusul setelah proses tender ulang selesai.'],
            ['id' => 22, 'alat_id' => 12, 'lokasi_id' => 7, 'kategori_id' => 3, 'label' => 'Konstruksi Lokasi (Sumur Pengganti)', 'status' => 'progres', 'periode_mulai' => '2026-06-01', 'durasi_bulan' => 3, 'catatan' => 'Konstruksi lokasi sumur pengganti RK 2026.'],
            ['id' => 23, 'alat_id' => 12, 'lokasi_id' => 8, 'kategori_id' => 1, 'label' => 'TLJT-04', 'status' => 'selesai', 'periode_mulai' => '2026-09-01', 'durasi_bulan' => 1, 'catatan' => 'Sudah tajak.'],
            ['id' => 24, 'alat_id' => 13, 'lokasi_id' => 9, 'kategori_id' => 10, 'label' => 'TTB-A1 (CF 2025)', 'status' => 'progres', 'periode_mulai' => '2026-07-01', 'durasi_bulan' => 1, 'catatan' => 'Est. release 12/13 Agustus 2026.'],
            ['id' => 25, 'alat_id' => 13, 'lokasi_id' => 10, 'kategori_id' => 1, 'label' => 'ABB-A5', 'status' => 'selesai', 'periode_mulai' => '2026-08-01', 'durasi_bulan' => 1, 'catatan' => 'Sudah tajak.'],
            ['id' => 26, 'alat_id' => 13, 'lokasi_id' => 11, 'kategori_id' => 6, 'label' => 'LVT-J (CF 2025)', 'status' => 'progres', 'periode_mulai' => '2026-12-01', 'durasi_bulan' => 1, 'catatan' => 'Finalisasi KJPP.'],
            ['id' => 27, 'alat_id' => 14, 'lokasi_id' => 12, 'kategori_id' => 10, 'label' => 'LBA-X13 (CF 2023,2025)', 'status' => 'progres', 'periode_mulai' => '2026-08-01', 'durasi_bulan' => 1, 'catatan' => 'On progress pengajuan reposisi SS.'],
            ['id' => 28, 'alat_id' => 14, 'lokasi_id' => 6, 'kategori_id' => 10, 'label' => 'KRG-PB5/HZ1 (CF 2026)', 'status' => 'progres', 'periode_mulai' => '2026-11-01', 'durasi_bulan' => 1, 'catatan' => 'M2 Juli 2026 sudah permohonan pengajuan PLK ke SKK Migas.'],
            ['id' => 29, 'alat_id' => 15, 'lokasi_id' => 10, 'kategori_id' => 10, 'label' => 'ABB-A6 — Amandemen Kontrak', 'status' => 'progres', 'periode_mulai' => '2026-08-01', 'durasi_bulan' => 1, 'catatan' => 'Amandemen kontrak dan evaluasi kesiapan rig.'],
            ['id' => 30, 'alat_id' => 15, 'lokasi_id' => 13, 'kategori_id' => 5, 'label' => 'BRG-U9', 'status' => 'progres', 'periode_mulai' => '2026-10-01', 'durasi_bulan' => 1, 'catatan' => 'Est. lahan bebas M1 Agustus 2026.'],
            ['id' => 31, 'alat_id' => 16, 'lokasi_id' => 14, 'kategori_id' => 7, 'label' => 'BKT-AW2', 'status' => 'progres', 'periode_mulai' => '2026-11-01', 'durasi_bulan' => 1, 'catatan' => 'Menggunakan slot UKL-UPL BKT-C (sebelumnya BKT-E).'],
            ['id' => 32, 'alat_id' => 16, 'lokasi_id' => 14, 'kategori_id' => 7, 'label' => 'BKT-AW3', 'status' => 'progres', 'periode_mulai' => '2026-12-01', 'durasi_bulan' => 1, 'catatan' => 'On progress survei sosialisasi, est. lahan bebas M2 September 2026, tetap gunakan slot UKL-UPL BKT-F.'],
        ];

        foreach ($jadwals as $jadwal) {
            Jadwal::updateOrCreate(['id' => $jadwal['id']], $jadwal);
        }
    }
}
