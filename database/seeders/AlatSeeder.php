<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alats = [
            ['id' => 1, 'nama' => 'PDSI #36.1/SKYTOP650-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'SKYTOP650-M', 'tahun_perolehan' => 2016, 'no_seri' => 'RIG-3610-2016', 'status' => 'aktif', 'keterangan' => 'Rig utama pengeboran Zona 4, saat ini pekerjaan Well Intervention.', 'lokasi_id' => 1],
            ['id' => 2, 'nama' => 'PDSI #16.2/NT45-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'NT45-M', 'tahun_perolehan' => 2014, 'no_seri' => 'RIG-1620-2014', 'status' => 'aktif', 'keterangan' => 'Menangani sumur MJ-A22 dan MJ-A9.', 'lokasi_id' => 1],
            ['id' => 3, 'nama' => 'PDSI #17.2/NT45-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'NT45-M', 'tahun_perolehan' => 2014, 'no_seri' => 'RIG-1720-2014', 'status' => 'aktif', 'keterangan' => 'Sumur eksplorasi Rosella Shallow South.', 'lokasi_id' => 2],
            ['id' => 4, 'nama' => 'AIRLANGGA #55/550 HP', 'kategori' => 'rig', 'merek' => 'Airlangga Drilling', 'model' => '550 HP', 'tahun_perolehan' => 2011, 'no_seri' => 'RIG-5500-2011', 'status' => 'bermasalah', 'keterangan' => 'Kontrak rig berakhir, menunggu proses re-tender.', 'lokasi_id' => null],
            ['id' => 5, 'nama' => 'TMMJ #23/550 HP', 'kategori' => 'rig', 'merek' => 'TMMJ', 'model' => '550 HP', 'tahun_perolehan' => 2012, 'no_seri' => 'RIG-2300-2012', 'status' => 'standby', 'keterangan' => 'Proses pengadaan, rencana inplace Zona 1 Juni 2026.', 'lokasi_id' => 1],
            ['id' => 6, 'nama' => 'PDSI #18.2/LTO-650', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'LTO-650', 'tahun_perolehan' => 2015, 'no_seri' => 'RIG-1820-2015', 'status' => 'aktif', 'keterangan' => 'Konstruksi lokasi BKB-AR.016 & BKB-AR.012.', 'lokasi_id' => 4],
            ['id' => 7, 'nama' => 'PDSI #20.2/EMSCOD2-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'EMSCOD2-M', 'tahun_perolehan' => 2013, 'no_seri' => 'RIG-2020-2013', 'status' => 'aktif', 'keterangan' => 'BEL-K8, BEL-K9, KRG-PN2/PN4.', 'lokasi_id' => 5],
            ['id' => 8, 'nama' => 'PDSI #25.2/LTO750-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'LTO750-M', 'tahun_perolehan' => 2017, 'no_seri' => 'RIG-2520-2017', 'status' => 'aktif', 'keterangan' => 'Konstruksi lokasi BKB-AR.008, menunggu penyelesaian tanam tumbuh.', 'lokasi_id' => 4],
            ['id' => 9, 'nama' => 'ACS #21/750 HP', 'kategori' => 'rig', 'merek' => 'ACS', 'model' => '750 HP', 'tahun_perolehan' => 2018, 'no_seri' => 'RIG-2100-2018', 'status' => 'aktif', 'keterangan' => 'KRG-PK2, amandemen PJWK.', 'lokasi_id' => 6],
            ['id' => 10, 'nama' => 'AMS #01/750 HP ex WBR #5', 'kategori' => 'rig', 'merek' => 'AMS', 'model' => '750 HP', 'tahun_perolehan' => 2010, 'no_seri' => 'RIG-0100-2010', 'status' => 'aktif', 'keterangan' => 'Eks WBR #5, menangani KRG-PB8 dan BEL-K10.', 'lokasi_id' => 6],
            ['id' => 11, 'nama' => 'UNSPEC RIG #4/750 HP', 'kategori' => 'rig', 'merek' => 'Belum ditentukan', 'model' => '750 HP', 'tahun_perolehan' => null, 'no_seri' => 'RIG-0004-TBD', 'status' => 'standby', 'keterangan' => 'Proses pengadaan re-tender, est. inplace Agustus 2026.', 'lokasi_id' => null],
            ['id' => 12, 'nama' => 'PDSI #01.2/N80B-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'N80B-M', 'tahun_perolehan' => 2012, 'no_seri' => 'RIG-0120-2012', 'status' => 'aktif', 'keterangan' => 'Konstruksi sumur pengganti TMB-PG3/5/7, TLJT-04.', 'lokasi_id' => 7],
            ['id' => 13, 'nama' => 'PDSI #05.2/OW760-M', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'OW760-M', 'tahun_perolehan' => 2016, 'no_seri' => 'RIG-0520-2016', 'status' => 'aktif', 'keterangan' => 'TTB-A1, ABB-A5, LVT-J.', 'lokasi_id' => 9],
            ['id' => 14, 'nama' => 'PDSI #30.2/D1000-E', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'D1000-E', 'tahun_perolehan' => 2019, 'no_seri' => 'RIG-3020-2019', 'status' => 'aktif', 'keterangan' => 'LBA-X13, KRG-PB5/HZ1.', 'lokasi_id' => 12],
            ['id' => 15, 'nama' => 'PDSI #32.2/N80UE', 'kategori' => 'rig', 'merek' => 'PDSI', 'model' => 'N80UE', 'tahun_perolehan' => 2013, 'no_seri' => 'RIG-3220-2013', 'status' => 'aktif', 'keterangan' => 'ABB-A6 amandemen kontrak, BRG-U9.', 'lokasi_id' => 10],
            ['id' => 16, 'nama' => 'TMMJ #05/1000 HP', 'kategori' => 'rig', 'merek' => 'TMMJ', 'model' => '1000 HP', 'tahun_perolehan' => 2011, 'no_seri' => 'RIG-0500-2011', 'status' => 'aktif', 'keterangan' => 'BKT-AW2, BKT-AW3, survei sosialisasi berjalan.', 'lokasi_id' => 14],
            ['id' => 17, 'nama' => 'Genset Cummins 500kVA', 'kategori' => 'alat_pendukung', 'merek' => 'Cummins', 'model' => 'C500D5', 'tahun_perolehan' => 2019, 'no_seri' => 'GEN-0192-2019', 'status' => 'aktif', 'keterangan' => 'Sumber daya cadangan lokasi konstruksi.', 'lokasi_id' => null],
            ['id' => 18, 'nama' => 'Mud Pump Triplex', 'kategori' => 'alat_pendukung', 'merek' => 'National Oilwell Varco', 'model' => 'NOV 12-P-160', 'tahun_perolehan' => 2018, 'no_seri' => 'MPT-0455-2018', 'status' => 'aktif', 'keterangan' => 'Digunakan bergantian antar rig sesuai jadwal pemboran.', 'lokasi_id' => null],
            ['id' => 19, 'nama' => 'Crane Crawler 80T', 'kategori' => 'alat_pendukung', 'merek' => 'Kobelco', 'model' => 'CK850G', 'tahun_perolehan' => 2020, 'no_seri' => 'CRN-0080-2020', 'status' => 'aktif', 'keterangan' => 'Mobilisasi alat berat ke lokasi konstruksi baru.', 'lokasi_id' => null],
            ['id' => 20, 'nama' => 'Compressor Udara', 'kategori' => 'alat_pendukung', 'merek' => 'Atlas Copco', 'model' => 'XATS 900', 'tahun_perolehan' => 2021, 'no_seri' => 'CMP-0900-2021', 'status' => 'standby', 'keterangan' => 'Belum ditugaskan, standby di gudang Zona 4.', 'lokasi_id' => null],
            ['id' => 21, 'nama' => 'BOP Stack', 'kategori' => 'alat_pendukung', 'merek' => 'Cameron', 'model' => 'U II 13-5/8"', 'tahun_perolehan' => 2017, 'no_seri' => 'BOP-1358-2017', 'status' => 'aktif', 'keterangan' => 'Blowout preventer, wajib terpasang saat operasi pemboran.', 'lokasi_id' => null],
            ['id' => 22, 'nama' => 'Top Drive System', 'kategori' => 'alat_pendukung', 'merek' => 'National Oilwell Varco', 'model' => 'TDS-11SA', 'tahun_perolehan' => 2016, 'no_seri' => 'TDS-1101-2016', 'status' => 'bermasalah', 'keterangan' => 'Perlu maintenance sebelum digunakan kembali.', 'lokasi_id' => null],
            ['id' => 23, 'nama' => 'Mobile Workshop Unit', 'kategori' => 'alat_pendukung', 'merek' => 'Mercedes-Benz', 'model' => 'Actros 3340', 'tahun_perolehan' => 2015, 'no_seri' => 'MWU-3340-2015', 'status' => 'aktif', 'keterangan' => 'Unit bengkel keliling untuk perbaikan lapangan.', 'lokasi_id' => null],
            ['id' => 24, 'nama' => 'Water Truck', 'kategori' => 'alat_pendukung', 'merek' => 'Hino', 'model' => 'FM 260 JD', 'tahun_perolehan' => 2019, 'no_seri' => 'WTR-0260-2019', 'status' => 'aktif', 'keterangan' => 'Suplai air untuk kebutuhan konstruksi & operasional lokasi.', 'lokasi_id' => null],
        ];

        foreach ($alats as $alat) {
            Alat::updateOrCreate(['id' => $alat['id']], $alat);
        }
    }
}
