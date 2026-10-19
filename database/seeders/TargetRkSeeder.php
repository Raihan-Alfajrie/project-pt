<?php

namespace Database\Seeders;

use App\Models\TargetRk;
use Illuminate\Database\Seeder;

class TargetRkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targets = [
            [
                'id' => 1,
                'tahun' => 2025,
                'tajak_target' => null,
                'completed_target' => null,
                'operasional_target' => null,
                'tajak_realisasi' => 43,
                'tajak_rkap' => 69,
                'tajak_wpb' => 100,
                'completed_realisasi' => 39,
                'completed_rkap' => 76,
                'completed_wpb' => 100,
            ],
            [
                'id' => 2,
                'tahun' => 2026,
                'tajak_target' => 86,
                'completed_target' => 83,
                'operasional_target' => 94,
                'tajak_realisasi' => null,
                'tajak_rkap' => null,
                'tajak_wpb' => null,
                'completed_realisasi' => null,
                'completed_rkap' => null,
                'completed_wpb' => null,
            ],
            [
                'id' => 3,
                'tahun' => 2027,
                'tajak_target' => 44,
                'completed_target' => 55,
                'operasional_target' => 55,
                'tajak_realisasi' => null,
                'tajak_rkap' => null,
                'tajak_wpb' => null,
                'completed_realisasi' => null,
                'completed_rkap' => null,
                'completed_wpb' => null,
            ],
        ];

        foreach ($targets as $t) {
            TargetRk::updateOrCreate(['id' => $t['id']], $t);
        }
    }
}
