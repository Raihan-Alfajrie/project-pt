<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Jadwal;
use App\Models\TargetRk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main operational dashboard.
     */
    public function index(Request $request): View
    {
        // 1. KPI Alat Counts (Rig only as per prototype)
        $totalRig = Alat::where('kategori', 'rig')->count();
        $rigAktif = Alat::where('kategori', 'rig')->where('status', 'aktif')->count();
        $rigStandby = Alat::where('kategori', 'rig')->where('status', 'standby')->count();
        $rigBermasalah = Alat::where('kategori', 'rig')->where('status', 'bermasalah')->count();
        $rigAktifPercent = $totalRig > 0 ? round(($rigAktif / $totalRig) * 100) : 0;

        // 2. Target RK
        $targetRk = TargetRk::where('tahun', 2026)->first() ?? TargetRk::first();

        // 3. 19 Months Gantt Range: Jun-2026 to Dec-2027
        $startDate = Carbon::create(2026, 6, 1);
        $months = [];
        for ($i = 0; $i < 19; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i);
            $months[] = [
                'index' => $i,
                'key' => $currentMonth->format('Y-m'),
                'label' => $currentMonth->format('M-y'),
                'is_now' => ($i === 1), // Jul-26 is now-col
            ];
        }

        // 4. Load Rigs with their Lokasi and Jadwal
        $rigs = Alat::where('kategori', 'rig')
            ->with(['lokasi', 'jadwals.kategori', 'jadwals.lokasi'])
            ->orderBy('id')
            ->get();

        // Format Rigs for Gantt and Table
        $ganttRigs = $rigs->map(function ($rig) use ($startDate) {
            $blocks = $rig->jadwals->map(function ($j) use ($startDate) {
                $itemStart = Carbon::parse($j->periode_mulai);
                // Calculate month difference from June 2026
                $diffYears = $itemStart->year - $startDate->year;
                $diffMonths = ($diffYears * 12) + ($itemStart->month - $startDate->month);
                $startIndex = max(0, $diffMonths);
                $duration = $j->durasi_bulan ?: 1;

                // Color & styling from dokumentasi_item
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

            // Active well for table
            $firstJadwal = $rig->jadwals->first();
            $lastJadwal = $rig->jadwals->last();
            $sumurAktif = $firstJadwal ? $firstJadwal->label : ($rig->keterangan ?: '—');
            
            // Progress calculation for display
            $progres = 0;
            if ($rig->status === 'aktif') {
                $progres = $firstJadwal ? ($firstJadwal->status === 'selesai' ? 100 : 65) : 50;
            } elseif ($rig->status === 'standby') {
                $progres = 20;
            } else {
                $progres = 0;
            }

            return [
                'id' => $rig->id,
                'no' => $rig->id,
                'nama' => $rig->nama,
                'status' => $rig->status,
                'statusLabel' => ucfirst($rig->status),
                'zona' => $rig->lokasi ? $rig->lokasi->nama_tempat : 'Zona 4',
                'sumurAktif' => $sumurAktif,
                'progres' => $progres,
                'blocks' => $blocks,
                'plan' => 6,
                'prognose' => max(1, round($blocks->count() * 2.2)),
            ];
        });

        return view('dashboard', [
            'totalRig' => $totalRig,
            'rigAktif' => $rigAktif,
            'rigAktifPercent' => $rigAktifPercent,
            'rigStandby' => $rigStandby,
            'rigBermasalah' => $rigBermasalah,
            'targetRk' => $targetRk,
            'months' => $months,
            'ganttRigs' => $ganttRigs,
        ]);
    }
}
