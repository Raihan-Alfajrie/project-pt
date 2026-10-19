<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit log entries (read-only).
     */
    public function index(Request $request): View
    {
        $logs = AuditLog::with('user')
            ->orderBy('waktu', 'desc')
            ->get();

        $logsData = $logs->map(fn($l) => [
            'id' => $l->id,
            'u' => $l->user?->nama ?? $l->username_snapshot,
            'un' => $l->username_snapshot,
            'email' => $l->email_snapshot,
            'jab' => ucfirst($l->jabatan_snapshot),
            'waktu' => $l->waktu ? $l->waktu->translatedFormat('d M Y, H:i') : '—',
            'aksi' => $l->aksi,
            'modul' => ucfirst($l->modul),
            'ket' => $l->keterangan,
            'initials' => $l->user?->initials ?? strtoupper(substr($l->username_snapshot, 0, 2)),
        ]);

        return view('audit-log.index', compact('logs', 'logsData'));
    }
}
