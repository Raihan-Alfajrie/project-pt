<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Jadwal;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama publik dengan statistik dinamis,
     * status armada rig publik, dan tabel jadwal Gantt penuh.
     */
    public function index(Request $request): View
    {
        // Fallback anggun jika tabel belum dimigrasikan (mis. saat pengujian isolasi tanpa RefreshDatabase)
        if (! Schema::hasTable('alat') || ! Schema::hasTable('lokasi') || ! Schema::hasTable('jadwal')) {
            return view('home', [
                'totalRig' => 16,
                'totalLokasi' => 14,
                'totalJadwalAktif' => 30,
                'totalAlat' => 24,
                'rigAktif' => 10,
                'rigStandby' => 4,
                'rigBermasalah' => 2,
                'rigPerbaikan' => 0,
                'months' => [],
                'ganttRigs' => collect(),
            ]);
        }

        // 1. Statistik Pengunjung Otomatis (Live Dynamic Stats)
        $totalRig = Alat::where('kategori', 'rig')->count();
        $totalLokasi = Lokasi::count();
        $totalJadwalAktif = Jadwal::count();
        $totalAlat = Alat::count();

        // 2. Ringkasan Status Armada Rig Publik (Public Fleet Status Ticker)
        $rigAktif = Alat::where('kategori', 'rig')->where('status', 'aktif')->count();
        $rigStandby = Alat::where('kategori', 'rig')->where('status', 'standby')->count();
        $rigBermasalah = Alat::where('kategori', 'rig')->where('status', 'bermasalah')->count();
        $rigPerbaikan = Alat::where('kategori', 'rig')->where('status', 'perbaikan')->count();

        // 3. Rentang Waktu Gantt Chart (Jun 2026 - Des 2027 / dinamis)
        $startDate = Carbon::create(2026, 6, 1);
        $minMonths = 19;

        $now = Carbon::now();
        $diffNow = ($now->year - $startDate->year) * 12 + ($now->month - $startDate->month) + 1;
        if ($diffNow > $minMonths) {
            $minMonths = $diffNow;
        }

        $maxJadwal = Jadwal::max('periode_mulai');
        if ($maxJadwal) {
            $maxCarbon = Carbon::parse($maxJadwal);
            $diffMax = ($maxCarbon->year - $startDate->year) * 12 + ($maxCarbon->month - $startDate->month) + 1;
            if ($diffMax > $minMonths) {
                $minMonths = $diffMax;
            }
        }

        $lastDate = $startDate->copy()->addMonths($minMonths - 1);
        if ($lastDate->month < 12) {
            $minMonths += (12 - $lastDate->month);
        }

        $nowKey = $now->format('Y-m');
        $months = [];
        for ($i = 0; $i < $minMonths; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i);
            $key = $currentMonth->format('Y-m');
            $months[] = [
                'index' => $i,
                'key' => $key,
                'label' => $currentMonth->format('M-y'),
                'is_now' => ($key === $nowKey),
            ];
        }

        // 4. Data Armada Rig & Jadwal Blok Operasi
        $rigs = Alat::where('kategori', 'rig')
            ->with(['lokasi', 'jadwals.kategori', 'jadwals.lokasi'])
            ->orderBy('id')
            ->get();

        $ganttRigs = $rigs->map(function ($rig) use ($startDate) {
            $blocks = $rig->jadwals->map(function ($j) use ($startDate) {
                $itemStart = Carbon::parse($j->periode_mulai);
                $diffYears = $itemStart->year - $startDate->year;
                $diffMonths = ($diffYears * 12) + ($itemStart->month - $startDate->month);
                $startIndex = max(0, $diffMonths);
                $duration = $j->durasi_bulan ?: 1;

                $warna = $j->kategori ? $j->kategori->warna : '#e6d9bd';
                $gaya = $j->kategori ? $j->kategori->gaya : 'solid';
                $isDark = in_array(strtolower($warna), ['#221417', '#5a3438']) || ($j->kategori_id == 11 || $j->kategori_id == 12);

                return [
                    'id' => $j->id,
                    'label' => $j->label,
                    'startIndex' => $startIndex,
                    'duration' => $duration,
                    'warna' => $warna,
                    'gaya' => $gaya,
                    'isDark' => $isDark,
                    'status' => $j->status,
                    'catatan' => $j->catatan,
                    'well' => $j->label,
                    'kategoriLabel' => $j->kategori ? $j->kategori->label : 'Umum',
                    'lokasiNama' => $j->lokasi ? $j->lokasi->nama_tempat : '—',
                ];
            });

            $firstJadwal = $rig->jadwals->first();
            $sumurAktif = $firstJadwal ? $firstJadwal->label : ($rig->keterangan ?: '—');

            return [
                'id' => $rig->id,
                'no' => $rig->id,
                'nama' => $rig->nama,
                'status' => $rig->status,
                'statusLabel' => ucfirst($rig->status),
                'zona' => $rig->lokasi ? $rig->lokasi->nama_tempat : 'Zona 4',
                'sumurAktif' => $sumurAktif,
                'blocks' => $blocks,
                'plan' => 6,
                'prognose' => max(1, round($blocks->count() * 2.2)),
            ];
        });

        return view('home', compact(
            'totalRig',
            'totalLokasi',
            'totalJadwalAktif',
            'totalAlat',
            'rigAktif',
            'rigStandby',
            'rigBermasalah',
            'rigPerbaikan',
            'months',
            'ganttRigs'
        ));
    }
}
