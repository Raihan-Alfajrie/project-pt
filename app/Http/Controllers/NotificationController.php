<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mengambil daftar notifikasi untuk pengguna yang sedang terautentikasi.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');
        $limit = min(max((int) $request->query('limit', 8), 1), 50);

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->take($limit)->get();

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications()->count(),
            'total' => $user->notifications()->count(),
            'data' => $notifications->map(fn (Notification $notif) => [
                'id' => $notif->id,
                'judul' => $notif->judul,
                'pesan' => $notif->pesan,
                'modul' => $notif->modul,
                'modul_label' => $notif->modul_label,
                'tipe' => $notif->tipe,
                'url' => $notif->url,
                'icon' => $notif->icon,
                'waktu_display' => $notif->waktu_display,
                'is_read' => $notif->is_read,
                'read_at' => $notif->read_at ? $notif->read_at->toIso8601String() : null,
                'created_at' => $notif->created_at ? $notif->created_at->toIso8601String() : null,
            ]),
        ]);
    }

    /**
     * Mengambil jumlah notifikasi yang belum dibaca (unread count).
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Menandai satu notifikasi tertentu sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        /** @var Notification|null $notification */
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
            'unread_count' => $user->unreadNotifications()->count(),
            'data' => [
                'id' => $notification->id,
                'is_read' => true,
                'read_at' => $notification->read_at ? $notification->read_at->toIso8601String() : null,
            ],
        ]);
    }

    /**
     * Menandai seluruh notifikasi pengguna sebagai sudah dibaca.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Seluruh notifikasi ditandai sudah dibaca.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Menghapus satu notifikasi milik pengguna.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus.',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Menampilkan halaman penuh daftar notifikasi (/notifikasi).
     */
    public function page(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');
        $modul = $request->query('modul', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->unread();
        }

        if ($modul !== 'all' && in_array($modul, ['alat', 'jadwal', 'lokasi', 'import_export', 'user', 'sistem'])) {
            $query->where('data->modul', $modul);
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'activeFilter' => $filter,
            'activeModul' => $modul,
        ]);
    }
}
