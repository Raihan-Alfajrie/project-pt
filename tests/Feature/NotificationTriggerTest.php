<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DokumentasiItem;
use App\Models\DokumentasiPanel;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTriggerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $workerWithAlat;
    protected User $workerWithoutAlat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'jabatan' => 'administrator',
            'status' => 'online',
        ]);

        $this->workerWithAlat = User::factory()->create([
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);
        UserPermission::create([
            'user_id' => $this->workerWithAlat->id,
            'modul' => 'alat',
            'diizinkan' => 1,
        ]);
        UserPermission::create([
            'user_id' => $this->workerWithAlat->id,
            'modul' => 'jadwal',
            'diizinkan' => 1,
        ]);

        $this->workerWithoutAlat = User::factory()->create([
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);
        UserPermission::create([
            'user_id' => $this->workerWithoutAlat->id,
            'modul' => 'alat',
            'diizinkan' => 0,
        ]);
        UserPermission::create([
            'user_id' => $this->workerWithoutAlat->id,
            'modul' => 'jadwal',
            'diizinkan' => 0,
        ]);
    }

    public function test_notification_triggered_when_alat_created(): void
    {
        $lokasi = Lokasi::create([
            'kode' => 'LIM',
            'nama_tempat' => 'Lapangan Limau',
            'provinsi' => 'Sumatera Selatan',
            'tanggal_dipetakan' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)->post('/alat', [
            'nama' => 'Rig N-80UE',
            'kategori' => 'rig',
            'status' => 'aktif',
            'merek' => 'National',
            'model' => 'N-80',
            'tahun_perolehan' => 2020,
            'no_seri' => 'RIG-N80-001',
            'lokasi_id' => $lokasi->id,
        ]);

        $response->assertRedirect('/alat');

        // Admin and worker with alat access should receive notification
        $this->assertTrue($this->admin->notifications()->where('data->modul', 'alat')->exists());
        $this->assertTrue($this->workerWithAlat->notifications()->where('data->modul', 'alat')->exists());

        // Worker without alat access should NOT receive it
        $this->assertFalse($this->workerWithoutAlat->notifications()->where('data->modul', 'alat')->exists());
    }

    public function test_notification_triggered_when_alat_status_changes_to_bermasalah(): void
    {
        $alat = Alat::create([
            'nama' => 'Rig N-80UE',
            'kategori' => 'rig',
            'status' => 'aktif',
            'merek' => 'National',
            'no_seri' => 'RIG-002',
        ]);

        $response = $this->actingAs($this->admin)->put("/alat/{$alat->id}", [
            'nama' => 'Rig N-80UE',
            'kategori' => 'rig',
            'status' => 'bermasalah',
            'merek' => 'National',
            'no_seri' => 'RIG-002',
        ]);

        $response->assertRedirect('/alat');

        $notif = $this->admin->notifications()->where('data->modul', 'alat')->latest()->first();
        $this->assertNotNull($notif);
        $this->assertSame('danger', $notif->tipe);
        $this->assertStringContainsString('Bermasalah', $notif->judul);
    }

    public function test_notification_triggered_when_jadwal_created(): void
    {
        $alat = Alat::create([
            'nama' => 'Rig Test',
            'kategori' => 'rig',
            'status' => 'aktif',
            'merek' => 'Merek',
            'no_seri' => 'RIG-003',
        ]);

        $panel = DokumentasiPanel::create([
            'judul' => 'Panel Operasi',
            'urutan' => 1,
        ]);

        $kategori = DokumentasiItem::create([
            'panel_id' => $panel->id,
            'label' => 'Kategori Operasi',
            'warna' => '#ff0000',
            'gaya' => 'solid',
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->admin)->post('/jadwal', [
            'alat_id' => $alat->id,
            'label' => 'Operasi Tajak LMU-102',
            'kategori_id' => $kategori->id,
            'status' => 'progres',
            'bulan_index' => 0,
            'durasi_bulan' => 2,
        ]);

        $response->assertRedirect('/jadwal');

        $this->assertTrue($this->admin->notifications()->where('data->modul', 'jadwal')->exists());
        $this->assertTrue($this->workerWithAlat->notifications()->where('data->modul', 'jadwal')->exists());
        $this->assertFalse($this->workerWithoutAlat->notifications()->where('data->modul', 'jadwal')->exists());
    }

    public function test_notification_triggered_when_jadwal_status_changes_to_kendala(): void
    {
        $alat = Alat::create([
            'nama' => 'Rig Test',
            'kategori' => 'rig',
            'status' => 'aktif',
            'merek' => 'Merek',
            'no_seri' => 'RIG-004',
        ]);

        $panel = DokumentasiPanel::create([
            'judul' => 'Panel Operasi',
            'urutan' => 1,
        ]);

        $kategori = DokumentasiItem::create([
            'panel_id' => $panel->id,
            'label' => 'Kategori Operasi',
            'warna' => '#ff0000',
            'gaya' => 'solid',
            'urutan' => 1,
        ]);

        $jadwal = Jadwal::create([
            'alat_id' => $alat->id,
            'kategori_id' => $kategori->id,
            'label' => 'Operasi Tajak LMU-103',
            'status' => 'progres',
            'periode_mulai' => '2026-06-01',
            'durasi_bulan' => 1,
        ]);

        $response = $this->actingAs($this->admin)->put("/jadwal/{$jadwal->id}", [
            'alat_id' => $alat->id,
            'label' => 'Operasi Tajak LMU-103',
            'kategori_id' => $kategori->id,
            'status' => 'kendala',
            'bulan_index' => 0,
            'durasi_bulan' => 1,
        ]);

        $response->assertRedirect('/jadwal');

        $notif = $this->admin->notifications()->where('data->modul', 'jadwal')->latest()->first();
        $this->assertNotNull($notif);
        $this->assertSame('warning', $notif->tipe);
        $this->assertStringContainsString('Kendala', $notif->judul);
    }

    public function test_notification_triggered_when_user_permissions_updated(): void
    {
        $response = $this->actingAs($this->admin)->put("/users/{$this->workerWithoutAlat->id}/permissions", [
            'alat' => 1,
            'lokasi' => 1,
            'jadwal' => 0,
        ]);

        $response->assertRedirect('/users');

        $notif = $this->workerWithoutAlat->notifications()->where('data->modul', 'user')->first();
        $this->assertNotNull($notif);
        $this->assertSame('Hak Akses Diperbarui', $notif->judul);
    }

    public function test_notification_triggered_when_new_user_registered(): void
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'nama' => 'Rahmat Hidayat',
            'username' => 'rahmat.hidayat',
            'email' => 'rahmat.hidayat@gmail.com',
            'password' => 'password123',
            'jabatan' => 'pekerja',
            'status' => 'offline',
        ]);

        $response->assertRedirect('/users');

        $notif = $this->admin->notifications()->where('data->modul', 'user')->latest()->first();
        $this->assertNotNull($notif);
        $this->assertSame('Pengguna Baru Terdaftar', $notif->judul);
    }

    public function test_notification_service_notify_import_completed(): void
    {
        NotificationService::notifyImportCompleted($this->admin, 'alat', 15, 15, 'sukses');

        $notif = $this->admin->notifications()->where('data->modul', 'import_export')->latest()->first();
        $this->assertNotNull($notif);
        $this->assertSame('success', $notif->tipe);
        $this->assertStringContainsString('15 data Alat & Rig berhasil', $notif->pesan);
    }
}
