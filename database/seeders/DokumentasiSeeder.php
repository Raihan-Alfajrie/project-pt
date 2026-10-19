<?php

namespace Database\Seeders;

use App\Models\DokumentasiItem;
use App\Models\DokumentasiPanel;
use Illuminate\Database\Seeder;

class DokumentasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $panels = [
            ['id' => 1, 'judul' => 'Kesiapan Operasional', 'deskripsi' => 'Warna blok pada Gantt Jadwal & Dashboard menandakan fase kerja sumur saat ini.', 'urutan' => 0],
            ['id' => 2, 'judul' => 'Kesiapan Administrasi', 'deskripsi' => 'Gaya border pada blok Gantt menandakan estimasi window persetujuan FID per kuartal.', 'urutan' => 1],
        ];

        foreach ($panels as $panel) {
            DokumentasiPanel::updateOrCreate(['id' => $panel['id']], $panel);
        }

        $items = [
            ['id' => 1, 'panel_id' => 1, 'label' => 'Sudah Tajak', 'gaya' => 'solid', 'warna' => '#3ec776', 'urutan' => 0],
            ['id' => 2, 'panel_id' => 1, 'label' => 'MIRU / RFD (Ready For Drilling)', 'gaya' => 'solid', 'warna' => '#f0a93b', 'urutan' => 1],
            ['id' => 3, 'panel_id' => 1, 'label' => 'Konstruksi', 'gaya' => 'solid', 'warna' => '#f4d03f', 'urutan' => 2],
            ['id' => 4, 'panel_id' => 1, 'label' => 'Lahan Bebas', 'gaya' => 'solid', 'warna' => '#8fd9a8', 'urutan' => 3],
            ['id' => 5, 'panel_id' => 1, 'label' => 'Proses Pembayaran / Pengajuan KUS-APT', 'gaya' => 'solid', 'warna' => '#4b9bf0', 'urutan' => 4],
            ['id' => 6, 'panel_id' => 1, 'label' => 'Finalisasi KJPP', 'gaya' => 'solid', 'warna' => '#c084f5', 'urutan' => 5],
            ['id' => 7, 'panel_id' => 1, 'label' => 'Pengajuan ARS / Survei Sosialisasi', 'gaya' => 'solid', 'warna' => '#3ecfc9', 'urutan' => 6],
            ['id' => 8, 'panel_id' => 1, 'label' => 'Proses Pengadaan Rig', 'gaya' => 'solid', 'warna' => '#a5a3ab', 'urutan' => 7],
            ['id' => 9, 'panel_id' => 1, 'label' => 'Rig Acceptance', 'gaya' => 'solid', 'warna' => '#9fc6ee', 'urutan' => 8],
            ['id' => 10, 'panel_id' => 1, 'label' => 'Lain-lain', 'gaya' => 'solid', 'warna' => '#e6d9bd', 'urutan' => 9],
            ['id' => 11, 'panel_id' => 1, 'label' => 'Sumur Eksplorasi / Kompleks', 'gaya' => 'solid', 'warna' => '#221417', 'urutan' => 10],
            ['id' => 12, 'panel_id' => 1, 'label' => 'Kontrak Habis', 'gaya' => 'solid', 'warna' => '#5a3438', 'urutan' => 11],
            ['id' => 13, 'panel_id' => 2, 'label' => 'Est. Approval FID Jan – Mar', 'gaya' => 'dashed', 'warna' => '#f0a93b', 'urutan' => 0],
            ['id' => 14, 'panel_id' => 2, 'label' => 'Est. Approval FID Apr – Jun', 'gaya' => 'dashed', 'warna' => '#c084f5', 'urutan' => 1],
            ['id' => 15, 'panel_id' => 2, 'label' => 'Est. Approval FID Jul – Sep', 'gaya' => 'border', 'warna' => '#4b9bf0', 'urutan' => 2],
            ['id' => 16, 'panel_id' => 2, 'label' => 'Est. Approval FID Oct – Dec', 'gaya' => 'border', 'warna' => '#ea2027', 'urutan' => 3],
        ];

        foreach ($items as $item) {
            DokumentasiItem::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
