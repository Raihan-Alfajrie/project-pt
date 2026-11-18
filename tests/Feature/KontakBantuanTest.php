<?php

namespace Tests\Feature;

use App\Models\KontakBantuan;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KontakBantuanTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_kontak_bantuan_record(): void
    {
        $kontak = KontakBantuan::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'no_hp' => '08123456789',
            'instansi' => 'PT Mitra Migas',
            'kategori' => 'bantuan',
            'perihal' => 'Permohonan Koordinasi Rig',
            'pesan' => 'Mohon bantuan jadwal pergerakan rig di WK Zona 4.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test Agent',
        ]);

        $this->assertDatabaseHas('kontak_bantuan', [
            'id' => $kontak->id,
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'status' => 'baru',
            'kategori' => 'bantuan',
        ]);

        $this->assertEquals('Permohonan Bantuan / Layanan', $kontak->kategori_label);
        $this->assertEquals('Baru', $kontak->status_label);
    }

    public function test_kategori_label_accessor_for_different_categories(): void
    {
        $faq = KontakBantuan::create([
            'nama' => 'Rina',
            'email' => 'rina@example.com',
            'kategori' => 'pertanyaan_faq',
            'perihal' => 'Pertanyaan Rig',
            'pesan' => 'Bagaimana cara melihat jadwal?',
        ]);

        $operasional = KontakBantuan::create([
            'nama' => 'Agus',
            'email' => 'agus@example.com',
            'kategori' => 'operasional',
            'perihal' => 'Kendala Lapangan',
            'pesan' => 'Peralatan di sumur tertunda.',
        ]);

        $this->assertEquals('Pertanyaan / FAQ Publik', $faq->kategori_label);
        $this->assertEquals('Operasional Lapangan & Rig', $operasional->kategori_label);
    }

    public function test_scope_baru_and_scope_kategori(): void
    {
        KontakBantuan::create([
            'nama' => 'User 1',
            'email' => 'u1@example.com',
            'kategori' => 'bantuan',
            'perihal' => 'Hal 1',
            'pesan' => 'Pesan 1',
            'status' => 'baru',
        ]);

        KontakBantuan::create([
            'nama' => 'User 2',
            'email' => 'u2@example.com',
            'kategori' => 'pertanyaan_faq',
            'perihal' => 'Hal 2',
            'pesan' => 'Pesan 2',
            'status' => 'dibaca',
        ]);

        $this->assertCount(1, KontakBantuan::baru()->get());
        $this->assertCount(1, KontakBantuan::kategori('pertanyaan_faq')->get());
    }

    public function test_scope_search_matches_various_fields(): void
    {
        KontakBantuan::create([
            'nama' => 'Doni Operator',
            'email' => 'doni@pertamina.com',
            'instansi' => 'Pertamina EP Asset 2',
            'kategori' => 'operasional',
            'perihal' => 'Jadwal Rig Limau',
            'pesan' => 'Status pengeboran sumur baru.',
        ]);

        $this->assertCount(1, KontakBantuan::search('Doni')->get());
        $this->assertCount(1, KontakBantuan::search('doni@pertamina.com')->get());
        $this->assertCount(1, KontakBantuan::search('Asset 2')->get());
        $this->assertCount(1, KontakBantuan::search('Limau')->get());
        $this->assertCount(1, KontakBantuan::search('pengeboran')->get());
        $this->assertCount(0, KontakBantuan::search('KataKunciYangTidakAda')->get());
    }

    public function test_notification_service_triggers_admin_notification_on_kontak_bantuan(): void
    {
        $admin1 = User::factory()->create([
            'jabatan' => 'administrator',
            'status' => 'online',
        ]);

        $admin2 = User::factory()->create([
            'jabatan' => 'administrator',
            'status' => 'online',
        ]);

        $worker = User::factory()->create([
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);

        $kontak = KontakBantuan::create([
            'nama' => 'Hendra Kusuma',
            'email' => 'hendra@example.com',
            'kategori' => 'bantuan',
            'perihal' => 'Permintaan Akses Peta',
            'pesan' => 'Tolong berikan informasi peta wilayah kerja Prabumulih.',
        ]);

        NotificationService::notifyKontakBantuanSubmitted($kontak);

        // Pastikan kedua admin menerima notifikasi
        $admin1Notifs = Notification::where('notifiable_id', $admin1->id)->get();
        $admin2Notifs = Notification::where('notifiable_id', $admin2->id)->get();
        $workerNotifs = Notification::where('notifiable_id', $worker->id)->get();

        $this->assertCount(1, $admin1Notifs);
        $this->assertCount(1, $admin2Notifs);
        $this->assertCount(0, $workerNotifs);

        $notif = $admin1Notifs->first();
        $this->assertEquals('Pesan Baru: Hendra Kusuma', $notif->judul);
        $this->assertStringContainsString('Permohonan Bantuan / Layanan', $notif->pesan);
        $this->assertStringContainsString('Permintaan Akses Peta', $notif->pesan);
        $this->assertEquals('kontak_bantuan', $notif->modul);
        $this->assertEquals('Bantuan & FAQ', $notif->modul_label);
        $this->assertEquals('mail', $notif->icon);
    }
}
