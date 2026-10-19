<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedApp extends Model
{
    use HasFactory;

    protected $table = 'connected_apps';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nama_aplikasi',
        'scope',
        'connected_at',
        'terakhir_digunakan',
    ];

    protected function casts(): array
    {
        return [
            'connected_at' => 'datetime',
            'terakhir_digunakan' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
