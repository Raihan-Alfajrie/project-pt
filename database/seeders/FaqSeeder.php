<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Operasional Rig & Alat
            [
                'kategori' => 'alat_rig',
                'pertanyaan' => 'Bagaimana cara membedakan Rig aktif dan alat pendukung pada modul Alat?',
                'jawaban' => 'Pada modul Alat, setiap item dikelompokkan ke dalam kategori "Rig" atau "Alat Pendukung". Rig ditandai dengan ikon menara bor khusus dan status operasional (Aktif, Tidak Aktif, atau Bermasalah). Anda dapat menyaring data berdasarkan kategori tersebut melalui dropdown filter di toolbar.',
                'urutan' => 1,
            ],
            [
                'kategori' => 'alat_rig',
                'pertanyaan' => 'Kapan status alat harus diubah menjadi "Bermasalah"?',
                'jawaban' => 'Status diubah menjadi "Bermasalah" apabila rig atau alat pendukung mengalami kendala teknis (breakdown), perbaikan besar di lapangan, atau sedang menjalani inspeksi keselamatan yang menghentikan operasi sementara.',
                'urutan' => 2,
            ],
            [
                'kategori' => 'alat_rig',
                'pertanyaan' => 'Bagaimana cara mengunggah atau memperbarui foto fisik rig/alat?',
                'jawaban' => 'Administrator atau Pekerja yang memiliki izin akses modul Alat dapat menekan tombol "Edit" pada baris alat, lalu memilih "Upload Foto Alat" pada modal form (format JPG, PNG, atau WEBP maks. 2MB). Foto akan langsung tampil di kolom thumbnail tabel dan modal detail.',
                'urutan' => 3,
            ],

            // Peta & Lapangan
            [
                'kategori' => 'lokasi_peta',
                'pertanyaan' => 'Apa arti animasi denyut (pulse pin) pada peta lokasi lapangan?',
                'jawaban' => 'Pin dengan animasi denyut hijau menandakan lapangan dengan status aktif beroperasi. Pin abu-abu atau tidak berdenyut menunjukkan lapangan non-aktif atau dalam persiapan. Klik pada pin untuk melihat nama lapangan, provinsi, serta shortcut ke modal detail.',
                'urutan' => 4,
            ],
            [
                'kategori' => 'lokasi_peta',
                'pertanyaan' => 'Bagaimana cara melihat daftar sumur dan rig yang ditugaskan di suatu lapangan?',
                'jawaban' => 'Buka modul Lokasi, lalu klik tombol "Lihat detail" pada tabel lapangan atau gunakan tautan deep-link (?kode=KODE_LAPANGAN). Modal detail akan menampilkan informasi administratif, daftar rig yang bertugas, dan riwayat sumur aktif di lokasi tersebut.',
                'urutan' => 5,
            ],

            // Jadwal Operasi & Gantt
            [
                'kategori' => 'jadwal_gantt',
                'pertanyaan' => 'Bagaimana cara membaca diagram Gantt 19 bulan dan penanda "Hari Ini"?',
                'jawaban' => 'Diagram Gantt menampilkan linimasa 19 bulan (Jan 2026 hingga Jul 2027). Setiap baris mewakili rig dan blok warna mewakili durasi kegiatan sumur. Garis vertikal putus-putus merah menandakan posisi bulan dan hari saat ini sebagai acuan progres operasi.',
                'urutan' => 6,
            ],
            [
                'kategori' => 'jadwal_gantt',
                'pertanyaan' => 'Apa perbedaan warna solid, border putus-putus, dan border solid pada blok jadwal?',
                'jawaban' => 'Warna solid menandakan fase kesiapan operasional (misal: Hijau untuk Sudah Tajak, Kuning untuk MIRU/RFD). Border putus-putus dan border solid menandakan estimasi window persetujuan administrasi/FID per kuartal sesuai legenda di modul Dokumentasi.',
                'urutan' => 7,
            ],
            [
                'kategori' => 'jadwal_gantt',
                'pertanyaan' => 'Bagaimana cara menyaring jadwal berdasarkan lapangan tertentu?',
                'jawaban' => 'Gunakan dropdown "Filter Lapangan" di toolbar modul Jadwal, atau klik kode lapangan dari modul Lokasi yang secara otomatis mengarahkan ke modul Jadwal dengan parameter filter yang telah terpasang.',
                'urutan' => 8,
            ],

            // Akun & Hak Akses Sistem
            [
                'kategori' => 'akun_akses',
                'pertanyaan' => 'Apa perbedaan wewenang antara Administrator dan Pekerja?',
                'jawaban' => 'Administrator memiliki kontrol penuh terhadap seluruh sistem: mengelola akun pengguna, mengubah hak akses modul, menambah panel/item legenda, serta melakukan aksi CRUD di semua modul. Pekerja hanya dapat melihat data dashboard secara read-only kecuali diberi izin khusus untuk modul tertentu.',
                'urutan' => 9,
            ],
            [
                'kategori' => 'akun_akses',
                'pertanyaan' => 'Bagaimana cara pekerja mendapatkan izin menambah/mengedit data?',
                'jawaban' => 'Administrator dapat membuka menu "Manajemen User" (/users), menekan tombol "Atur Hak Akses", lalu mengaktifkan toggle switch untuk modul yang bersangkutan (Alat, Lokasi, atau Jadwal). Perubahan izin berlaku seketika saat disimpan.',
                'urutan' => 10,
            ],
            [
                'kategori' => 'akun_akses',
                'pertanyaan' => 'Apakah setiap aktivitas penambahan atau perubahan data dicatat?',
                'jawaban' => 'Ya. Seluruh mutasi data penting (tambah, edit, hapus) serta aktivitas login dan logout pengguna dicatat secara otomatis dan real-time ke dalam modul "Audit Log" (/audit-log) lengkap dengan rincian pengguna, modul, dan stempel waktu.',
                'urutan' => 11,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['pertanyaan' => $faq['pertanyaan']],
                $faq
            );
        }
    }
}
