<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetRk extends Model
{
    use HasFactory;

    protected $table = 'target_rk';

    public $timestamps = false;

    protected $fillable = [
        'tahun',
        'tajak_target',
        'completed_target',
        'operasional_target',
        'tajak_realisasi',
        'tajak_rkap',
        'tajak_wpb',
        'completed_realisasi',
        'completed_rkap',
        'completed_wpb',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'tajak_target' => 'integer',
            'completed_target' => 'integer',
            'operasional_target' => 'integer',
            'tajak_realisasi' => 'integer',
            'tajak_rkap' => 'integer',
            'tajak_wpb' => 'integer',
            'completed_realisasi' => 'integer',
            'completed_rkap' => 'integer',
            'completed_wpb' => 'integer',
        ];
    }
}
