<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokasis = [
            ['id' => 1, 'kode' => 'MJ', 'nama_tempat' => 'Lapangan MJ', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur MJ-A9, MJ-A21, MJ-A22, MJ-OS2.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 2, 'kode' => 'RSS', 'nama_tempat' => 'Lapangan Rosella Shallow South', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Sumur eksplorasi shallow.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 3, 'kode' => 'TAP', 'nama_tempat' => 'Lapangan TAP', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur TAP-NW.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 4, 'kode' => 'BKB', 'nama_tempat' => 'Lapangan BKB', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur BKB-AR.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 5, 'kode' => 'BEL', 'nama_tempat' => 'Lapangan BEL', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur BEL-K.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 6, 'kode' => 'KRG', 'nama_tempat' => 'Lapangan KRG', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur KRG-PN/PK/PB/HZ.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 7, 'kode' => 'TMB', 'nama_tempat' => 'Lapangan TMB', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur pengganti TMB-PG.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 8, 'kode' => 'TLJT', 'nama_tempat' => 'Lapangan TLJT', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Sumur TLJT-04.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 9, 'kode' => 'TTB', 'nama_tempat' => 'Lapangan TTB', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Sumur TTB-A1.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 10, 'kode' => 'ABB', 'nama_tempat' => 'Lapangan ABB', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur ABB-A.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 11, 'kode' => 'LVT', 'nama_tempat' => 'Lapangan LVT', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Sumur LVT-J.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 12, 'kode' => 'LBA', 'nama_tempat' => 'Lapangan LBA', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Sumur LBA-X13.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 13, 'kode' => 'BRG', 'nama_tempat' => 'Lapangan BRG', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Sumur BRG-U9.', 'tanggal_dipetakan' => '2026-07-30'],
            ['id' => 14, 'kode' => 'BKT', 'nama_tempat' => 'Lapangan BKT', 'provinsi' => 'Sumatera Selatan', 'kota' => null, 'kecamatan' => null, 'keterangan' => 'Cluster sumur BKT-AW.', 'tanggal_dipetakan' => '2026-07-30'],
        ];

        foreach ($lokasis as $lokasi) {
            Lokasi::updateOrCreate(['id' => $lokasi['id']], $lokasi);
        }
    }
}
