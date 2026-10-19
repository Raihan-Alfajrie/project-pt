<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'name',
        'username',
        'email',
        'password',
        'jabatan',
        'status',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    public function connectedApps(): HasMany
    {
        return $this->hasMany(ConnectedApp::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->jabatan === 'administrator';
    }

    public function hasModuleAccess(string $modul): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $perm = $this->permissions->firstWhere('modul', $modul);
        return $perm ? (bool) $perm->diizinkan : false;
    }

    public function getAksesMapAttribute(): array
    {
        if ($this->isAdmin()) {
            return ['alat' => true, 'lokasi' => true, 'jadwal' => true];
        }
        return [
            'alat' => $this->hasModuleAccess('alat'),
            'lokasi' => $this->hasModuleAccess('lokasi'),
            'jadwal' => $this->hasModuleAccess('jadwal'),
        ];
    }

    public function getInitialsAttribute(): string
    {
        $p = preg_split('/\s+/', trim($this->nama ?? $this->name ?? 'User'));
        $i1 = $p[0][0] ?? 'U';
        $i2 = $p[1][0] ?? '';
        return strtoupper($i1 . $i2);
    }
}
